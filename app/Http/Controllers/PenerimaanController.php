<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenerimaanRequest;
use App\Models\Barang;
use App\Models\BatchBarang;
use App\Models\DetailPembelian;
use App\Models\DetailPenerimaan;
use App\Models\DetailPenolakan;
use App\Models\DetailProduksi;
use App\Models\DetailRetur;
use App\Models\KonversiBarang;
use App\Models\Pembelian;
use App\Models\Penerimaan;
use App\Models\Retur;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class PenerimaanController extends Controller
{
    private function resolveRasioKonversi(int $idBarang, int $idSatuan): int
    {
        $rasio = KonversiBarang::where('id_barang', $idBarang)
            ->where('id_satuan', $idSatuan)
            ->value('nilai_konversi');

        if ($rasio === null) {
            throw new Exception("Rasio konversi bahan baku ID {$idBarang} dengan Satuan ID {$idSatuan} tidak ditemukan!");
        }

        return (int) round((float) $rasio);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penerimaan = Penerimaan::select('id_penerimaan', 'no_registrasi', 'tanggal_masuk', 'id_pembelian', 'jenis_penerimaan')
            ->with([
                'pembelian' => function ($query) {
                    $query->select('id_pembelian', 'no_nota', 'id_supplier')->with('supplier:id_supplier,nama_supplier');
                }
            ])
            ->get();

        return view("admin-gudang.penerimaan.index", compact('penerimaan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $penerimaan = Penerimaan::with('detailPenerimaan.batch', 'retur')->get();
        $data_pembelian = Pembelian::select('id_pembelian', 'no_nota', 'id_supplier')->with('supplier:id_supplier,nama_supplier')->get();
        $data_retur = Retur::where('status', 'Pending')->whereDoesntHave('penerimaan')->with(['pembelian.supplier'])->get();
        $kodeRegis = Penerimaan::generateNoRegis();

        return view('admin-gudang.penerimaan.add', compact('penerimaan', 'kodeRegis', 'data_pembelian', 'data_retur'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePenerimaanRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated) {

                $isRetur = $validated['jenis_penerimaan'] === 'Retur';
                /* Detail pembelian hanya diperlukan untuk penerimaan Pembelian*/

                $detailPembelian = collect();

                if (!$isRetur) {
                    $detailPembelian = DetailPembelian::where('id_pembelian', $validated['id_pembelian'])->lockForUpdate()->get()->keyBy('id_barang');
                    foreach ($validated['id_barang'] as $index => $id_barang) {
                        if (!$detailPembelian->has($id_barang)) {
                            throw ValidationException::withMessages([
                                "id_barang.{$index}" =>
                                "Barang tersebut tidak terdapat dalam pembelian."
                            ]);
                        }
                    }
                }

                // Validasi Retur
                if ($isRetur) {
                    $retur = Retur::where('id_retur', $validated['id_retur'])->lockForUpdate()->first();

                    if (!$retur) {
                        throw ValidationException::withMessages(['id_retur' => 'Data retur tidak ditemukan.']);
                    }

                    if ($retur->status !== 'Pending') {
                        throw ValidationException::withMessages(['id_retur' => 'Retur tersebut sudah diproses dan tidak dapat diterima kembali.']);
                    }

                    if ($retur->penerimaan()->exists()) {
                        throw ValidationException::withMessages(['id_retur' => 'Retur tersebut sudah memiliki penerimaan.']);
                    }
                }

                // header penerimaan
                $penerimaan = Penerimaan::create([
                    'no_registrasi' => $validated['no_registrasi'],
                    'tanggal_masuk' => $validated['tanggal_masuk'],
                    'id_pembelian' => $validated['id_pembelian'],
                    'no_faktur' => $validated['no_faktur'] ?? null,
                    'surat_jalan' => $validated['surat_jalan'] ?? null,
                    'jenis_penerimaan' => $validated['jenis_penerimaan'],
                    'id_retur' => $validated['id_retur'] ?? null,
                ]);

                foreach ($validated['id_barang'] as $index => $id_barang) {
                    // Ambil data detail
                    $id_satuan = $validated['id_satuan'][$index];
                    $jumlah_masuk = (int) $validated['jumlah_masuk'][$index];
                    $jumlah_ditolak = isset($validated['jumlah_ditolak'][$index]) ? (int) $validated['jumlah_ditolak'][$index] : 0;
                    $expired_date = $validated['expired_date'][$index];
                    $kode_lot_supplier = $validated['kode_lot_supplier'][$index] ?? null;
                    $deskripsi = $validated['deskripsi'][$index] ?? null;
                    $alasan_penolakan = $validated['alasan_penolakan'][$index] ?? null;

                    // validasi jumlah masuk dan jumlah ditolak
                    if ($jumlah_masuk <= 0 && $jumlah_ditolak <= 0) {
                        throw ValidationException::withMessages(["jumlah_masuk.{$index}" => "Jumlah masuk atau jumlah ditolak harus diisi."]);
                    }

                    if ($jumlah_ditolak > 0 && empty($alasan_penolakan)) {
                        throw ValidationException::withMessages([
                            "alasan_penolakan.{$index}" =>
                            "Alasan penolakan wajib diisi jika terdapat barang yang ditolak."
                        ]);
                    }

                    // validasi jumlah masuk dan jumlah ditolak tidak boleh melebihi jumlah pembelian
                    if (!$isRetur) {

                        $detailBeli = $detailPembelian->get($id_barang);
                        $jumlahPembelian = $detailBeli->kuantitas;

                        $jumlahSudahDiproses = DetailPenerimaan::whereHas('penerimaan', function ($query) use ($validated) {
                            $query->where('id_pembelian', $validated['id_pembelian'])->where('jenis_penerimaan', 'Pembelian');
                        })->whereHas('batch', function ($query) use ($id_barang) {
                            $query->where('id_barang', $id_barang);
                        })->selectRaw('COALESCE(SUM(jumlah_masuk), 0) +COALESCE(SUM(jumlah_ditolak), 0) AS total')->value('total') ?? 0;

                        $jumlahSekarang = $jumlah_masuk + $jumlah_ditolak;
                        $totalSetelahPenerimaan = $jumlahSudahDiproses + $jumlahSekarang;

                        if ($totalSetelahPenerimaan != $jumlahPembelian) {
                            if ($totalSetelahPenerimaan < $jumlahPembelian) {

                                $kekurangan = $jumlahPembelian - $totalSetelahPenerimaan;
                                throw ValidationException::withMessages([
                                    "jumlah_masuk.{$index}" =>
                                    "Jumlah masuk dan jumlah ditolak belum lengkap. " .
                                        "Pembelian sebanyak {$jumlahPembelian}, " .
                                        "sedangkan total yang diproses baru {$totalSetelahPenerimaan}. " .
                                        "Masih kurang {$kekurangan}."
                                ]);
                            }

                            if ($totalSetelahPenerimaan > $jumlahPembelian) {
                                $kelebihan = $totalSetelahPenerimaan - $jumlahPembelian;

                                throw ValidationException::withMessages([
                                    "jumlah_masuk.{$index}" =>
                                    "Jumlah masuk dan jumlah ditolak melebihi jumlah pembelian. " .
                                        "Pembelian sebanyak {$jumlahPembelian}, " .
                                        "sedangkan total yang diproses {$totalSetelahPenerimaan}. " .
                                        "Kelebihan {$kelebihan}."
                                ]);
                            }
                        }
                    }

                    // Jika rasio konversi tidak dikirim, ambil dari database
                    $rasioKonversi = $validated['rasio_konversi'][$index] ?? $this->resolveRasioKonversi((int) $id_barang, (int) $id_satuan);
                    $total_gram = $jumlah_masuk * $rasioKonversi;

                    // Jika kode lot supplier dikirim, buat kode batch internal berdasarkan kode lot supplier dan urutan batch. Jika tidak, buat kode batch internal dengan format BCH-tanggal-acak.
                    if (!empty($kode_lot_supplier)) {
                        $urutan = BatchBarang::where(
                            'kode_lot_supplier',
                            $kode_lot_supplier
                        )->lockForUpdate()->count() + 1;

                        $kode_batch_internal = $kode_lot_supplier . '-' . $urutan;
                    } else {

                        $kode_batch_internal = 'BCH-' . date('Ymd', strtotime($validated['tanggal_masuk'])) . '-' . strtoupper(Str::random(4));
                    }

                    $batch = BatchBarang::create([
                        'kode_batch' => $kode_batch_internal,
                        'kode_lot_supplier' => $kode_lot_supplier,
                        'id_barang' => $id_barang,
                        'expired_date' => $expired_date,
                        'sisa_persediaan' => $total_gram,
                    ]);

                    DetailPenerimaan::create([
                        'id_penerimaan' => $penerimaan->id_penerimaan,
                        'id_batch' => $batch->id_batch,
                        'jumlah_masuk' => $jumlah_masuk,
                        'jumlah_ditolak' => $jumlah_ditolak > 0 ? $jumlah_ditolak : null,
                        'alasan_penolakan' => $jumlah_ditolak > 0 ? $alasan_penolakan : null,
                        'rasio_konversi' => $rasioKonversi,
                        'deskripsi' => $deskripsi,
                    ]);
                }

                // Jika penerimaan ini berasal dari Retur, maka ubah status Retur menjadi "Diretur"
                if ($isRetur) {
                    Retur::where('id_retur', $validated['id_retur'])->update(['status' => 'Diretur',]);
                }
            });

            return redirect()->route('penerimaan.index')->with('success', 'Penerimaan berhasil disimpan!');
        } catch (Throwable $e) {
            Log::error(
                'Error saat create penerimaan: ' . $e->getMessage()
            );

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_penerimaan)
    {
        $penerimaan = Penerimaan::with(['pembelian.supplier', 'detailPenerimaan.batch.barang.satuan', 'detailPenerimaan.batch.barang.konversiBarang'])->findOrFail($id_penerimaan);

        return view('admin-gudang.penerimaan.show', compact('penerimaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_penerimaan)
    {
        $penerimaan = Penerimaan::with([
            'pembelian.detailPembelian.satuan',
            'detailPenerimaan.batch.barang',
            'retur.detailRetur.batch',
            'retur.detailRetur.satuan'
        ])->findOrFail($id_penerimaan);

        // Ambil data retur yang statusnya Pending dan belum memiliki penerimaan, atau yang sudah terkait dengan penerimaan ini
        $data_retur = Retur::where(function ($query) use ($penerimaan) {
            $query->whereDoesntHave('penerimaan')->orWhere('id_retur', $penerimaan->id_retur);
        })->whereIn('status', ['Pending', 'Diretur'])->select('id_retur', 'no_retur')->get();

        return view('admin-gudang.penerimaan.edit', compact('penerimaan', 'data_retur'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(StorePenerimaanRequest $request, string $id_penerimaan)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $id_penerimaan) {

                $penerimaan = Penerimaan::with([
                    'detailPenerimaan.batch',
                    'retur',
                ])
                    ->lockForUpdate()
                    ->findOrFail($id_penerimaan);

                if (
                    $validated['jenis_penerimaan']
                    !== $penerimaan->jenis_penerimaan
                ) {
                    throw ValidationException::withMessages([
                        'jenis_penerimaan' =>
                        'Jenis penerimaan tidak dapat diubah saat melakukan edit.'
                    ]);
                }

                $isRetur = $penerimaan->jenis_penerimaan === 'Retur';
                $detailLama = $penerimaan->detailPenerimaan->keyBy('id_detail_penerimaan');

                // pastiin detail yang dikirim memang merupakan detail dari penerimaan ini
                foreach ($validated['id_detail_penerimaan'] as $index => $idDetail) {
                    if (!$detailLama->has($idDetail)) {
                        throw ValidationException::withMessages([
                            "id_detail_penerimaan.{$index}" =>
                            'Detail penerimaan tidak valid.'
                        ]);
                    }
                }

                // pembelian
                $detailPembelian = collect();
                if (!$isRetur) {
                    if (
                        (int) $validated['id_pembelian']
                        !== (int) $penerimaan->id_pembelian
                    ) {
                        throw ValidationException::withMessages([
                            'id_pembelian' =>
                            'Pembelian pada penerimaan tidak dapat diganti saat melakukan edit.'
                        ]);
                    }

                    $detailPembelian =
                        DetailPembelian::where(
                            'id_pembelian',
                            $penerimaan->id_pembelian
                        )->lockForUpdate()->get()->keyBy('id_barang');
                }

                // retur
                $oldReturId = $penerimaan->id_retur;
                $newReturId = $validated['id_retur'] ?? null;

                if ($isRetur) {

                    if (!$newReturId) {
                        throw ValidationException::withMessages([
                            'id_retur' =>
                            'Penerimaan retur harus memiliki nomor retur.'
                        ]);
                    }

                    $returBaru = Retur::where(
                        'id_retur',
                        $newReturId
                    )->lockForUpdate()->first();

                    if (!$returBaru) {
                        throw ValidationException::withMessages([
                            'id_retur' =>
                            'Data retur tidak ditemukan.'
                        ]);
                    }

                    // Jika retur diganti, retur baru tidak boleh sudah digunakan oleh penerimaan lain
                    if (
                        (int) $newReturId !== (int) $oldReturId
                    ) {

                        $sudahDigunakan =
                            Penerimaan::where(
                                'id_retur',
                                $newReturId
                            )->where(
                                'id_penerimaan',
                                '!=',
                                $penerimaan->id_penerimaan
                            )->exists();

                        if ($sudahDigunakan) {
                            throw ValidationException::withMessages([
                                'id_retur' => 'Retur tersebut sudah memiliki penerimaan.'
                            ]);
                        }

                        // retur yang sudah diretur gabisa dipilih
                        if ($returBaru->status === 'Diretur') {
                            throw ValidationException::withMessages([
                                'id_retur' => 'Retur tersebut sudah diterima.'
                            ]);
                        }
                    }
                }

                foreach (
                    $validated['id_detail_penerimaan'] as $index => $idDetail
                ) {

                    $idBarang = $validated['id_barang'][$index];
                    $jumlahMasuk = (int) ($validated['jumlah_masuk'][$index] ?? 0);
                    $jumlahDitolak = (int) ($validated['jumlah_ditolak'][$index] ?? 0);
                    $jumlahSekarang =
                        $jumlahMasuk + $jumlahDitolak;

                    // validasi jumlah masuk dan jumlah ditolak harus lebih dari 0
                    if ($jumlahSekarang <= 0) {
                        throw ValidationException::withMessages([
                            "jumlah_masuk.{$index}" =>
                            'Jumlah masuk atau jumlah ditolak harus lebih dari 0.'
                        ]);
                    }

                    // alasan wajib diisi 
                    if (
                        $jumlahDitolak > 0
                        && empty($validated['alasan_penolakan'][$index] ?? null)
                    ) {
                        throw ValidationException::withMessages([
                            "alasan_penolakan.{$index}" =>
                            'Alasan penolakan wajib diisi jika terdapat bahan baku yang ditolak.'
                        ]);
                    }

                    // expired date validasi
                    if (
                        $jumlahMasuk > 0 && empty($validated['expired_date'][$index] ?? null)
                    ) {
                        throw ValidationException::withMessages([
                            "expired_date.{$index}" => 'Tanggal kedaluwarsa wajib diisi untuk bahan baku yang diterima.'
                        ]);
                    }

                    // validasi
                    if (!$isRetur) {
                        if (!$detailPembelian->has($idBarang)) {
                            throw ValidationException::withMessages(["id_barang.{$index}" => 'Bahan baku tersebut tidak terdapat pada pembelian.']);
                        }

                        $detailBeli = $detailPembelian->get($idBarang);

                        $jumlahPembelian = (int) $detailBeli->kuantitas;

                        /*Hitung penerimaan pembelian lain.Penerimaan yang sedang diedit dikecualikan.*/
                        $jumlahSudahDiproses = DetailPenerimaan::whereHas(
                            'batch',
                            function ($query) use ($idBarang) {
                                $query->where('id_barang', $idBarang);
                            }
                        )->whereHas('penerimaan', function ($query) use ($penerimaan) {
                            $query->where('id_pembelian', $penerimaan->id_pembelian)->where('jenis_penerimaan', 'Pembelian')->where('id_penerimaan', '!=', $penerimaan->id_penerimaan);
                        })
                            ->selectRaw('COALESCE(SUM(jumlah_masuk), 0)+ COALESCE(SUM(jumlah_ditolak), 0)AS total')->value('total') ?? 0;

                        $sisaPembelian = $jumlahPembelian - $jumlahSudahDiproses;

                        // jumlah edit harus tepat dengan sisa pembelian yang harus diproses
                        if (
                            $jumlahSekarang != $sisaPembelian
                        ) {

                            if (
                                $jumlahSekarang < $sisaPembelian
                            ) {
                                $kekurangan = $sisaPembelian - $jumlahSekarang;

                                throw ValidationException::withMessages([
                                    "jumlah_masuk.{$index}" => "Jumlah masuk dan jumlah ditolak belum lengkap. " . "Sisa pembelian yang harus diproses: " . "{$sisaPembelian}, " . "sedangkan yang dimasukkan hanya " . "{$jumlahSekarang}. " . "Masih kurang {$kekurangan}."
                                ]);
                            }

                            if (
                                $jumlahSekarang > $sisaPembelian
                            ) {
                                $kelebihan = $jumlahSekarang - $sisaPembelian;
                                throw ValidationException::withMessages([
                                    "jumlah_masuk.{$index}" => "Jumlah masuk dan jumlah ditolak melebihi sisa pembelian. " . "Sisa pembelian: {$sisaPembelian}, " . "sedangkan yang dimasukkan {$jumlahSekarang}. " . "Kelebihan {$kelebihan}."
                                ]);
                            }
                        }
                    }
                }

                // update header penerimaan
                $penerimaan->update([
                    'tanggal_masuk' => $validated['tanggal_masuk'],
                    'no_faktur' => $validated['no_faktur'] ?? null,
                    'surat_jalan' => $validated['surat_jalan'] ?? null,
                    'jenis_penerimaan' => $penerimaan->jenis_penerimaan,
                    'id_retur' => $isRetur ? $newReturId : null,
                ]);

                // update detail
                foreach (
                    $validated['id_detail_penerimaan'] as $index => $idDetail
                ) {

                    $detail = $detailLama->get($idDetail);
                    $batchLama = $detail->batch;
                    $idBarang = $validated['id_barang'][$index];
                    $jumlahMasuk = (int) ($validated['jumlah_masuk'][$index] ?? 0);
                    $jumlahDitolak = (int) ($validated['jumlah_ditolak'][$index] ?? 0);
                    $kodeLot = $validated['kode_lot_supplier'][$index] ?? null;
                    $expiredDate = $validated['expired_date'][$index] ?? null;
                    $deskripsi = $validated['deskripsi'][$index] ?? null;
                    $alasanPenolakan = $validated['alasan_penolakan'][$index] ?? null;

                    // Jika penerimaan berasal dari Retur, pastikan barang pada retur ada pada pembelian asal.
                    if ($isRetur) {

                        $returUntukSatuan = Retur::with('pembelian')->findOrFail($newReturId);
                        $idPembelianRetur = $returUntukSatuan->id_pembelian;
                        $detailBeli = DetailPembelian::where('id_pembelian', $idPembelianRetur)->where('id_barang', $idBarang)->first();

                        if (!$detailBeli) {
                            throw ValidationException::withMessages(["id_barang.{$index}" => 'Bahan baku pada retur tidak ditemukan pada pembelian asal.']);
                        }
                    } else {

                        $detailBeli = $detailPembelian->get($idBarang);
                    }

                    $idSatuan = $detailBeli->id_satuan;

                    $rasioKonversi = $this->resolveRasioKonversi((int) $idBarang, (int) $idSatuan);

                    // cek batch lama
                    if ($batchLama) {

                        $jumlahBerubah = $jumlahMasuk != $detail->jumlah_masuk;
                        $kodeLotBerubah = $kodeLot != $batchLama->kode_lot_supplier;
                        $expiredBerubah = $expiredDate != $batchLama->expired_date;

                        // batch sudah digunakan di transaksi lain?
                        $batchSudahDigunakan = DetailProduksi::where(
                            'id_batch',
                            $batchLama->id_batch
                        )->exists() ||
                            DetailPenolakan::where('id_batch', $batchLama->id_batch)->exists() ||
                            DetailRetur::where('id_batch', $batchLama->id_batch)->exists();

                        // batch yang sudah digunakan tidak boleh diubah data yang memengaruhi stok.
                        if (
                            $batchSudahDigunakan && ($jumlahBerubah || $kodeLotBerubah || $expiredBerubah)
                        ) {
                            throw ValidationException::withMessages([
                                "jumlah_masuk.{$index}" => "Batch '{$batchLama->kode_batch}' sudah digunakan " . "pada transaksi produksi, penolakan, atau retur, " . "sehingga data penerimaan yang memengaruhi batch " . "tidak dapat diubah."
                            ]);
                        }
                    }

                    if ($jumlahMasuk > 0) {
                        $totalGram = $jumlahMasuk * $rasioKonversi;

                        if ($batchLama) {

                            if (!empty($kodeLot) && $kodeLot != $batchLama->kode_lot_supplier) {

                                $urutan = BatchBarang::where('kode_lot_supplier', $kodeLot)->lockForUpdate()->count() + 1;
                                $kodeBatch = $kodeLot . '-' . $urutan;
                            } elseif (!empty($kodeLot)) {
                                $kodeBatch = $batchLama->kode_batch;
                            } else {
                                $kodeBatch = $batchLama->kode_batch;
                            }

                            $batchLama->update([
                                'kode_batch' => $kodeBatch,
                                'kode_lot_supplier' => $kodeLot,
                                'id_barang' => $idBarang,
                                'expired_date' => $expiredDate,
                                'sisa_persediaan' => $totalGram,
                            ]);

                            $idBatch = $batchLama->id_batch;
                        } else {

                            // Jika batch lama tidak ada, buat batch baru.
                            if (!empty($kodeLot)) {

                                $urutan = BatchBarang::where('kode_lot_supplier', $kodeLot)->lockForUpdate()->count() + 1;

                                $kodeBatch = $kodeLot . '-' . $urutan;
                            } else {
                                $kodeBatch = 'BCH-' . date('Ymd', strtotime($validated['tanggal_masuk'])) . '-' . strtoupper(Str::random(4));
                            }

                            $batch = BatchBarang::create([
                                'kode_batch' => $kodeBatch,
                                'kode_lot_supplier' => $kodeLot,
                                'id_barang' => $idBarang,
                                'expired_date' => $expiredDate,
                                'sisa_persediaan' => $totalGram,
                            ]);
                            $idBatch = $batch->id_batch;
                        }

                        // seluruh ditolak
                    } else {
                        if (!$batchLama) {
                            throw ValidationException::withMessages(["id_batch.{$index}" => 'Batch bahan baku tidak ditemukan.']);
                        }

                        // jika batch sudah digunakan, jumlah masuk tidak boleh diubah menjadi 0.
                        $batchSudahDigunakan = DetailProduksi::where('id_batch', $batchLama->id_batch)->exists()
                            ||
                            DetailPenolakan::where('id_batch', $batchLama->id_batch)->exists() || DetailRetur::where('id_batch', $batchLama->id_batch)->exists();

                        if (
                            $batchSudahDigunakan && $detail->jumlah_masuk > 0
                        ) {
                            throw ValidationException::withMessages([
                                "jumlah_masuk.{$index}" => "Batch '{$batchLama->kode_batch}' sudah digunakan " . "sehingga jumlah masuk tidak dapat diubah menjadi 0."
                            ]);
                        }

                        $batchLama->update([
                            'kode_lot_supplier' => $kodeLot,
                            'expired_date' => $expiredDate,
                            'sisa_persediaan' =>
                            0,
                        ]);

                        $idBatch = $batchLama->id_batch;
                    }

                    // update detail penerimaan
                    $detail->update([
                        'id_batch' => $idBatch,
                        'jumlah_masuk' => $jumlahMasuk,
                        'jumlah_ditolak' => $jumlahDitolak > 0 ? $jumlahDitolak : null,
                        'alasan_penolakan' => $jumlahDitolak > 0 ? $alasanPenolakan : null,
                        'rasio_konversi' => $rasioKonversi,
                        'deskripsi' => $deskripsi,
                    ]);
                }

                // Jika nomor retur diganti: Retur lama -> Pending. Retur baru -> Diretur
                if ($isRetur) {
                    if (
                        $oldReturId && (int) $oldReturId !== (int) $newReturId
                    ) {
                        Retur::where('id_retur', $oldReturId)->update(['status' => 'Pending',]);
                    }

                    Retur::where('id_retur', $newReturId)->update(['status' => 'Diretur',]);
                }
            });

            return redirect()->route('penerimaan.index')->with('success', 'Penerimaan berhasil diperbarui!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {

            Log::error(
                'Error saat update penerimaan: ' . $e->getMessage()
            );

            return back()->withInput()->with('error', 'Terjadi kesalahan saat mengubah data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_penerimaan)
    {
        try {

            DB::transaction(function () use ($id_penerimaan) {

                $penerimaan = Penerimaan::with(['detailPenerimaan.batch'])->lockForUpdate()->findOrFail($id_penerimaan);
                foreach ($penerimaan->detailPenerimaan as $detail) {
                    $batch = $detail->batch;
                    if (!$batch) {
                        throw new Exception("Batch pada detail penerimaan tidak ditemukan.");
                    }

                    // Cek apakah batch sudah digunakan dalam produksi
                    $digunakanProduksi = DetailProduksi::where('id_batch', $batch->id_batch)->exists();

                    // Cek apakah batch sudah digunakan dalam penolakan
                    $digunakanPenolakan = DetailPenolakan::where('id_batch', $batch->id_batch)->exists();

                    // Cek apakah batch sudah digunakan dalam retur
                    $digunakanRetur = DetailRetur::where('id_batch', $batch->id_batch)->exists();

                    if (
                        $digunakanProduksi || $digunakanPenolakan || $digunakanRetur
                    ) {
                        throw new Exception("Penerimaan tidak dapat dihapus karena batch {$batch->kode_lot_supplier} sudah digunakan dalam proses produksi, penolakan, atau retur.");
                    }

                    // Stok awal dihitung dari jumlah_masuk dikalikan rasio_konversi. Ini untuk memastikan bahwa stok awal yang diterima dari penerimaan ini tidak melebihi sisa persediaan batch saat ini
                    $stokAwal = (int) $detail->jumlah_masuk * (int) $detail->rasio_konversi;

                    // Jika sisa persediaan batch lebih kecil dari stok awal, berarti batch sudah digunakan sebagian, sehingga penerimaan tidak bisa dihapus
                    if ($batch->sisa_persediaan < $stokAwal) {
                        throw new Exception("Batch {$batch->kode_lot_supplier} sudah digunakan sehingga penerimaan tidak dapat dihapus.");
                    }

                    $detail->delete();
                    $batch->delete();
                }

                // Jika penerimaan berasal dari Retur, maka ubah status Retur menjadi "Pending" karena penerimaan ini akan dihapus
                if ($penerimaan->jenis_penerimaan === 'Retur' && $penerimaan->id_retur) {
                    Retur::where('id_retur', $penerimaan->id_retur)->update(['status' => 'Pending',]);
                }

                $penerimaan->delete();
            });

            return redirect()->route('penerimaan.index')->with('success', 'Penerimaan berhasil dihapus!');
        } catch (Throwable $e) {
            Log::error(
                'Error saat hapus penerimaan: ' . $e->getMessage()
            );

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function autocompleteNota(Request $request)
    {
        $pembelian = Pembelian::with([
            'supplier',
            'detailPembelian.barang',
            'detailPembelian.satuan'
        ])
            ->where('no_nota', 'LIKE', '%' . $request->term . '%')
            ->limit(5)
            ->get();

        $result = $pembelian->map(function ($p) {

            return [
                'label' => $p->no_nota,
                'value' => $p->no_nota,
                'id' => $p->id_pembelian,
                'id_supplier' => $p->id_supplier,
                'supplier' => $p->supplier?->nama_supplier,

                'details' => $p->detailPembelian->map(function ($detail) use ($p) {

                    /*Jumlah yang sudah diproses dari pembelian ini.Yang dihitung: jumlah_masuk + jumlah_ditolak. Penerimaan jenis Retur tidak dihitung karena barang retur merupakan barang pengganti.*/
                    $jumlahSudahDiproses = DetailPenerimaan::whereHas('batch', function ($query) use ($detail) {
                        $query->where('id_barang', $detail->id_barang);
                    })
                        ->whereHas('penerimaan', function ($query) use ($p) {
                            $query->where('id_pembelian', $p->id_pembelian)->where('jenis_penerimaan', 'Pembelian');
                        })->selectRaw('COALESCE(SUM(jumlah_masuk), 0) + COALESCE(SUM(jumlah_ditolak), 0) AS total')->value('total') ?? 0;

                    /*Jumlah yang masih dapat diproses.*/
                    $jumlahSisa = $detail->kuantitas - $jumlahSudahDiproses;

                    /* Jangan sampai menghasilkan angka negatif.
                 */
                    $jumlahSisa = max(0, $jumlahSisa);

                    return [
                        'id_barang' => $detail->id_barang,
                        'nama_barang' => $detail->barang?->nama_barang,

                        // Jumlah pembelian awal
                        'jumlah_pembelian' => $detail->kuantitas,

                        // Jumlah yang sudah diterima/ditolak
                        'jumlah_sudah_diproses' => $jumlahSudahDiproses,

                        // Jumlah yang masih boleh diproses
                        'jumlah_sisa' => $jumlahSisa,

                        'id_satuan' => $detail->id_satuan,
                        'kode_satuan' => $detail->satuan?->kode_satuan,

                        'rasio_konversi' =>
                        $this->resolveRasioKonversi(
                            (int) $detail->id_barang,
                            (int) $detail->id_satuan
                        ),

                        'deskripsi' => $detail->deskripsi,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json($result);
    }

    public function cetakPdf(string $id_penerimaan)
    {
        $penerimaan = Penerimaan::with(['pembelian.supplier', 'detailPenerimaan.batch', 'pembelian.detailPembelian'])->findOrFail($id_penerimaan);
        $pdf = Pdf::loadView('admin-gudang.penerimaan.pdf', compact('penerimaan'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Penerimaan_' . $penerimaan->no_registrasi . '.pdf');
    }

    public function getDetailRetur(string $id_retur)
    {
        $retur = Retur::with(['pembelian.supplier', 'detailRetur.batch.barang', 'detailRetur.satuan'])->find($id_retur);

        if (!$retur) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        $result = $retur->detailRetur->map(function ($item) {
            return [
                'id_detail_retur' => $item->id_detail_retur,
                'id_batch' => $item->id_batch,
                'id_barang' => $item->batch?->barang?->id_barang ?? '',
                'nama_barang' => $item->batch?->barang?->nama_barang ?? 'Barang Tidak Diketahui',
                'kode_lot_supplier' => $item->batch?->kode_lot_supplier ?? '',
                'expired_date' => $item->batch?->expired_date ?? '',
                'jumlah_retur' => $item->jumlah_retur,
                'id_satuan' => $item->id_satuan,
                'kode_satuan' => $item->satuan?->kode_satuan ?? '-',
                'rasio_konversi' => $item->nilai_konversi,
                'deskripsi' => $item->deskripsi ?? '',
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'id_retur' => $retur->id_retur,
            'id_pembelian' => $retur->id_pembelian ?? '',
            'no_nota' => $retur->pembelian?->no_nota ?? '',
            'id_supplier' => $retur->pembelian?->id_supplier ?? '',
            'nama_supplier' => $retur->pembelian?->supplier?->nama_supplier ?? '',
            'details' => $result,
        ]);
    }
}
