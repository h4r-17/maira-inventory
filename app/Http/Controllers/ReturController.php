<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReturRequest;
use App\Models\BatchBarang;
use App\Models\DetailPenerimaan;
use App\Models\DetailPenolakan;
use App\Models\DetailRetur;
use App\Models\KonversiBarang;
use App\Models\Pembelian;
use App\Models\Penerimaan;
use App\Models\Penolakan;
use App\Models\Retur;
use App\Models\Satuan;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class ReturController extends Controller
{
    private function getFolderRole() // untuk menentukan view berdasarkan role user
    {
        $role = Auth::user()->role->nama_role;
        return Str::slug($role);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $retur = Retur::with('penolakan', 'pembelian')->get();
        $folderRole = $this->getFolderRole();

        return view("{$folderRole}.retur.index", compact('retur'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $penolakan = Penolakan::where('status', 'Retur')->whereDoesntHave('retur')->get(); // Memastikan belum pernah diproses retur
        $data_supplier = Supplier::pluck('nama_supplier', 'id_supplier');
        $data_satuan = Satuan::whereNotIn('kode_satuan', ['PCS', 'GRAM'])->get();
        $kodeRetur = Retur::generateNoRetur();

        return view('bagian-keuangan.retur.add', compact('penolakan', 'data_supplier', 'data_satuan', 'kodeRetur'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReturRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated) {
                $idPenolakan = $validated['id_penolakan'] ?? null;
                $idPenerimaan = $validated['id_penerimaan'] ?? null;
                $isFromPenolakan = !empty($idPenolakan);
                $isFromPenerimaan = !empty($idPenerimaan);

                // retur harus satu sumber, baik dari penolakan atau penerimaan, tidak boleh keduanya
                if (!$isFromPenolakan && !$isFromPenerimaan) {
                    throw ValidationException::withMessages([
                        'id_penolakan' => 'Sumber retur harus berasal dari penolakan atau penerimaan.'
                    ]);
                }

                if ($isFromPenolakan && $isFromPenerimaan) {
                    throw ValidationException::withMessages([
                        'id_penolakan' =>
                        'Retur tidak dapat berasal dari penolakan dan penerimaan sekaligus.'
                    ]);
                }

                $retur = Retur::create([
                    'no_retur' => $validated['no_retur'],
                    'tanggal_retur' => $validated['tanggal_retur'],
                    'id_penolakan' => $idPenolakan,
                    'id_penerimaan' => $idPenerimaan,
                    'id_pembelian' => $validated['id_pembelian'],
                    'status' => 'Pending',
                ]);

                foreach ($validated['id_barang'] as $index => $idBarang) {
                    $jumlahRetur = (int) $validated['jumlah_retur'][$index];
                    $idSatuan = (int) $validated['id_satuan'][$index];
                    $nilaiKonversi = (int) $validated['nilai_konversi'][$index];
                    $idBatch = $validated['id_batch'][$index] ?? null;

                    // retur penerimaan
                    if ($isFromPenerimaan) {

                        $idDetailPenerimaan = $validated['id_detail_penerimaan'][$index] ?? null;

                        if (!$idDetailPenerimaan) {
                            throw ValidationException::withMessages(["id_detail_penerimaan.{$index}" => 'Detail penerimaan wajib dipilih.']);
                        }

                        /*Ambil detail penerimaan beserta batch.*/

                        $detailPenerimaan =
                            DetailPenerimaan::with('batch')->where('id_detail_penerimaan', $idDetailPenerimaan)->where('id_penerimaan', $idPenerimaan)->lockForUpdate()->first();

                        if (
                            !$detailPenerimaan || !$detailPenerimaan->batch
                        ) {
                            throw ValidationException::withMessages([
                                "id_detail_penerimaan.{$index}" =>
                                'Detail penerimaan tidak valid.'
                            ]);
                        }

                        /*Batch yang dikirim samadengan batch pada penerimaan*/

                        if ($idBatch && (int) $idBatch !== (int) $detailPenerimaan->id_batch) {
                            throw ValidationException::withMessages([
                                "id_batch.{$index}" => 'Batch retur tidak sesuai dengan batch penerimaan.'
                            ]);
                        }

                        /*Pastikan barang sesuai dengan batch*/
                        if ((int) $detailPenerimaan->batch->id_barang !== (int) $idBarang) {
                            throw ValidationException::withMessages([
                                "id_barang.{$index}" =>
                                'Barang tidak sesuai dengan batch penerimaan.'
                            ]);
                        }

                        /*Jumlah retur tidak boleh melebihi jumlah yang ditolak */
                        $jumlahDitolak = (int) $detailPenerimaan->jumlah_ditolak;

                        if ($jumlahRetur > $jumlahDitolak) {
                            throw ValidationException::withMessages([
                                "jumlah_retur.{$index}" =>
                                "Jumlah retur tidak boleh melebihi jumlah ditolak ({$jumlahDitolak})."
                            ]);
                        }

                        /*Batch berasal dari penerimaan.*/
                        $idBatch = $detailPenerimaan->id_batch;
                    }

                    // retur penolakan
                    if ($isFromPenolakan) {
                        $idDetailPenolakan = $validated['id_detail_penolakan'][$index] ?? null;

                        if (!$idDetailPenolakan) {
                            throw ValidationException::withMessages(["id_detail_penolakan.{$index}" => 'Detail penolakan wajib dipilih.']);
                        }

                        $detailPenolakan = DetailPenolakan::with('batch')->where('id_detail_penolakan', $idDetailPenolakan)->where('id_penolakan', $idPenolakan)->lockForUpdate()->first();

                        if (
                            !$detailPenolakan || !$detailPenolakan->batch
                        ) {
                            throw ValidationException::withMessages(["id_detail_penolakan.{$index}" => 'Detail penolakan tidak valid.']);
                        }

                        /*Pastikan barang sesuai batch*/
                        if ((int) $detailPenolakan->batch->id_barang !== (int) $idBarang) {
                            throw ValidationException::withMessages([
                                "id_barang.{$index}" => 'Bahan baku tidak sesuai dengan detail penolakan.'
                            ]);
                        }

                        // jumlah retur tidak boleh dari jumlah tolak
                        $jumlahDitolak = (int) $detailPenolakan->jumlah_ditolak;

                        if ($jumlahRetur > $jumlahDitolak) {
                            throw ValidationException::withMessages([
                                "jumlah_retur.{$index}" =>
                                "Jumlah retur tidak boleh melebihi jumlah ditolak ({$jumlahDitolak})."
                            ]);
                        }

                        $idBatch = $detailPenolakan->id_batch;
                    }

                    if (!$idBatch) {
                        throw ValidationException::withMessages(["id_batch.{$index}" => 'Batch retur tidak ditemukan.']);
                    }

                    // simpan retur
                    DetailRetur::create([
                        'id_retur' => $retur->id_retur,
                        'id_batch' => $idBatch,
                        'id_satuan' => $idSatuan,
                        'jumlah_retur' => $jumlahRetur,
                        'nilai_konversi' => $nilaiKonversi,
                        'deskripsi' => $validated['deskripsi'][$index] ?? null,
                    ]);
                }
            });

            return redirect()->route('retur.index')->with('success', 'Retur berhasil disimpan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Error saat create retur: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan retur.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_retur)
    {
        $retur = Retur::with(['penolakan', 'penerimaan', 'pembelian.supplier', 'detailRetur.batch.barang', 'detailRetur.satuan'])->findOrFail($id_retur);
        $folderRole = $this->getFolderRole();

        return view("{$folderRole}.retur.show", compact('retur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_retur)
    {
        $retur = Retur::with(['penolakan', 'penerimaan', 'pembelian.supplier', 'detailRetur.batch.barang', 'detailRetur.satuan'])->findOrFail($id_retur);
        $penolakan = Penolakan::where('status', 'Retur')
            ->where(function ($query) use ($retur) {
                $query->whereDoesntHave('retur')
                    ->orWhere('id_penolakan', $retur->id_penolakan);
            })
            ->get();

        $detail_retur = $retur->detailRetur->map(function ($detail) use ($retur) {
            $idDetailPenolakan = null;
            $idDetailPenerimaan = null;
            $jumlahDitolak = 0;

            // Jika retur berasal dari penolakan, ambil detail penolakan terkait
            if ($retur->id_penolakan) {
                $detailPenolakan = DetailPenolakan::where('id_penolakan', $retur->id_penolakan)
                    ->where('id_batch', $detail->id_batch)
                    ->first();

                $idDetailPenolakan = $detailPenolakan?->id_detail_penolakan;

                $jumlahDitolak = $detailPenolakan?->jumlah_ditolak ?? 0;
            }

            // jika retur berasal dari penerimaan, ambil detail penerimaan terkait
            if ($retur->id_penerimaan) {
                $detailPenerimaan = DetailPenerimaan::where('id_penerimaan', $retur->id_penerimaan)->where('id_batch', $detail->id_batch)->first();

                $idDetailPenerimaan =
                    $detailPenerimaan?->id_detail_penerimaan;

                $jumlahDitolak =
                    $detailPenerimaan?->jumlah_ditolak ?? 0;
            }

            return [
                'id_detail_penolakan' => $idDetailPenolakan,
                'id_batch' => $detail->id_batch,
                'id_barang' => $detail->batch?->id_barang,
                'nama_barang' => $detail->batch?->barang?->nama_barang ?? '',
                'batch_barang' => $detail->batch?->kode_lot_supplier ?? '',
                'expired_date' => $detail->batch?->expired_date,
                'jumlah_ditolak' => $jumlahDitolak,
                'id_satuan' => $detail->id_satuan,
                'kode_satuan' => $detail->satuan?->kode_satuan ?? '',
                'nilai_konversi' => $detail->nilai_konversi,
                'jumlah_retur' => $detail->jumlah_retur,
                'deskripsi' => $detail->deskripsi ?? '',
                'id_detail_penerimaan' => $idDetailPenerimaan,
            ];
        })->values();

        return view('bagian-keuangan.retur.edit', compact('retur', 'penolakan', 'detail_retur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreReturRequest $request, string $id_retur)
    {
        $validated = $request->validated();
        $retur = Retur::with(['detailRetur.batch'])->findOrFail($id_retur);

        if ($retur->status === 'Diretur') {
            return redirect()->route('retur.index')->with('error', 'Retur yang sudah diterima tidak dapat diubah.');
        }

        try {
            DB::transaction(function () use ($validated, $retur) {

                $isOldFromPenolakan =
                    !empty($retur->id_penolakan);

                $isOldFromPenerimaan =
                    !empty($retur->id_penerimaan);

                // Jika retur lama berasal dari penerimaan, kembalikan stok
                if ($isOldFromPenerimaan) {

                    foreach ($retur->detailRetur as $oldDetail) {

                        $batchLama = BatchBarang::where(
                            'id_batch',
                            $oldDetail->id_batch
                        )
                            ->lockForUpdate()
                            ->first();

                        if (!$batchLama) {
                            continue;
                        }

                        $konversiLama = KonversiBarang::where(
                            'id_barang',
                            $batchLama->id_barang
                        )
                            ->where(
                                'id_satuan',
                                $oldDetail->id_satuan
                            )
                            ->first();

                        if (!$konversiLama) {
                            throw ValidationException::withMessages([
                                'id_satuan' =>
                                'Konversi satuan pada data retur lama tidak ditemukan.',
                            ]);
                        }

                        $jumlahDasarLama = (int) $oldDetail->jumlah_retur * (int) $konversiLama->nilai_konversi;

                        $batchLama->increment(
                            'sisa_persediaan',
                            $jumlahDasarLama
                        );
                    }
                }

                // ambil id_penolakan dan id_penerimaan dari request
                $newPenolakanId = $validated['id_penolakan'] ?? null;

                $newPenerimaanId = $validated['id_penerimaan'] ?? null;

                // pastikan hanya satu sumber penolakan yang digunakan
                if ($newPenolakanId && $newPenerimaanId) {
                    throw ValidationException::withMessages(['id_penolakan' => 'Retur hanya boleh memiliki satu sumber transaksi.',]);
                }

                if (!$newPenolakanId && !$newPenerimaanId) {
                    throw ValidationException::withMessages(['id_penolakan' => 'Sumber retur harus dipilih.',]);
                }

                // cek apakah penolakan sudah digunakan oleh retur lain
                if ($newPenolakanId) {
                    $alreadyUsedByOtherRetur =
                        Retur::where('id_penolakan', $newPenolakanId)->where('id_retur', '!=', $retur->id_retur)->exists();

                    if ($alreadyUsedByOtherRetur) {

                        throw ValidationException::withMessages([
                            'id_penolakan' =>
                            'Kode penolakan ini sudah digunakan oleh retur lain.',
                        ]);
                    }
                }

                // cek apakah penerimaan sudah digunakan oleh retur lain
                if ($newPenerimaanId) {
                    $alreadyUsedByOtherRetur =
                        Retur::where('id_penerimaan', $newPenerimaanId)->where('id_retur', '!=', $retur->id_retur)->exists();

                    if ($alreadyUsedByOtherRetur) {
                        throw ValidationException::withMessages([
                            'id_penerimaan' => 'Penerimaan ini sudah digunakan oleh retur lain.',
                        ]);
                    }
                }

                // update header retur
                $retur->update([
                    'no_retur' => $validated['no_retur'],
                    'tanggal_retur' => $validated['tanggal_retur'],
                    'id_penolakan' => $newPenolakanId,
                    'id_penerimaan' => $newPenerimaanId,
                    'id_pembelian' => $validated['id_pembelian'],
                    'id_supplier' => $validated['id_supplier'],
                    'deskripsi' => $validated['deskripsi'] ?? null,
                    'status' => 'Pending',
                ]);

                // hapus detail lama
                DetailRetur::where('id_retur', $retur->id_retur)->delete();

                // cari apakah retur berasal dari penolakan atau penerimaan
                $isFromPenolakan = !empty($newPenolakanId);

                $isFromPenerimaan = !empty($newPenerimaanId);

                foreach ($validated['id_barang'] as $index => $id_barang) {

                    $jumlahRetur = (int) $validated['jumlah_retur'][$index];
                    $idSatuan = (int) $validated['id_satuan'][$index];
                    $nilaiKonversi = (int) $validated['nilai_konversi'][$index];
                    $idBatch = (int) $validated['id_batch'][$index];

                    // jumlah retur valid
                    if ($jumlahRetur <= 0) {

                        throw ValidationException::withMessages([
                            "jumlah_retur.{$index}" =>
                            'Jumlah retur harus lebih dari 0.',
                        ]);
                    }

                    // batch harus ada
                    $batch = BatchBarang::where(
                        'id_batch',
                        $idBatch
                    )
                        ->lockForUpdate()
                        ->first();

                    if (!$batch) {

                        throw ValidationException::withMessages([
                            "id_batch.{$index}" =>
                            'Batch pada detail retur tidak ditemukan.',
                        ]);
                    }

                    // validasi batch sesuai dengan barang
                    if ((int) $batch->id_barang !== (int) $id_barang) {

                        throw ValidationException::withMessages([
                            "id_barang.{$index}" =>
                            'Barang pada detail retur tidak sesuai dengan batch.',
                        ]);
                    }

                    // retur dari penolakan produksi
                    if ($isFromPenolakan) {
                        $idDetailPenolakan = $validated['id_detail_penolakan'][$index] ?? null;
                        $detailPenolakan = DetailPenolakan::where('id_detail_penolakan', $idDetailPenolakan)->where('id_penolakan', $newPenolakanId)->where('id_batch', $idBatch)->first();

                        if (!$detailPenolakan) {
                            throw ValidationException::withMessages([
                                "id_detail_penolakan.{$index}" =>
                                'Detail penolakan pada baris ini tidak valid.',
                            ]);
                        }

                        if ($jumlahRetur > (int) $detailPenolakan->jumlah_ditolak) {
                            throw ValidationException::withMessages(["jumlah_retur.{$index}" => 'Jumlah retur tidak boleh melebihi jumlah yang ditolak.',]);
                        }

                        // detail retur dari penolakan tidak mengurangi stok karena barang sudah ditolak sebelum masuk stok
                        DetailRetur::create([
                            'id_retur' => $retur->id_retur,
                            'id_batch' => $idBatch,
                            'id_satuan' => $idSatuan,
                            'jumlah_retur' => $jumlahRetur,
                            'nilai_konversi' => $nilaiKonversi,
                            'deskripsi' => $validated['deskripsi'][$index] ?? null,
                        ]);

                        continue;
                    }

                    // retur dari penerimaan
                    if ($isFromPenerimaan) {
                        $idDetailPenerimaan = $validated['id_detail_penerimaan'][$index] ?? null;

                        $detailPenerimaan =
                            DetailPenerimaan::where('id_detail_penerimaan', $idDetailPenerimaan)->where('id_penerimaan', $newPenerimaanId)->where('id_batch', $idBatch)->first();

                        if (!$detailPenerimaan) {
                            throw ValidationException::withMessages([
                                "id_detail_penerimaan.{$index}" =>
                                'Detail penerimaan pada baris ini tidak valid.',
                            ]);
                        }

                        $jumlahDitolak = (int) $detailPenerimaan->jumlah_ditolak;
                        if ($jumlahDitolak <= 0) {

                            throw ValidationException::withMessages([
                                "jumlah_retur.{$index}" =>
                                'Detail penerimaan tersebut tidak memiliki jumlah yang ditolak.',
                            ]);
                        }

                        // jumlah retur tidak boleh melebihi jumlah yang ditolak
                        if ($jumlahRetur > $jumlahDitolak) {

                            throw ValidationException::withMessages([
                                "jumlah_retur.{$index}" =>
                                'Jumlah retur tidak boleh melebihi jumlah yang ditolak.',
                            ]);
                        }

                        // hitung stok yang dikurangi
                        $jumlahDasar = $jumlahRetur * $nilaiKonversi;
                        if (
                            $batch->sisa_persediaan
                            < $jumlahDasar
                        ) {

                            throw ValidationException::withMessages([
                                "jumlah_retur.{$index}" => "Sisa persediaan batch '{$batch->kode_batch}' tidak mencukupi untuk retur ini.",
                            ]);
                        }

                        DetailRetur::create([
                            'id_retur' => $retur->id_retur,
                            'id_batch' => $idBatch,
                            'id_satuan' => $idSatuan,
                            'jumlah_retur' => $jumlahRetur,
                            'nilai_konversi' => $nilaiKonversi,
                            'deskripsi' => $validated['deskripsi'][$index] ?? null,
                        ]);
                        $batch->decrement(
                            'sisa_persediaan',
                            $jumlahDasar
                        );
                    }
                }
            });

            return redirect()->route('retur.index')->with('success', 'Retur berhasil diperbarui!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {

            Log::error(
                'Error saat update retur: ' . $e->getMessage()
            );

            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui retur.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_retur)
    {
        $retur = Retur::with('detailRetur')->findOrFail($id_retur);

        if ($retur->status === 'Diretur') {
            return redirect()->route('retur.index')->with('error', 'Retur yang sudah diterima tidak dapat dihapus.');
        }

        DB::transaction(function () use ($retur) {
            if ($retur->id_penerimaan) {

                foreach ($retur->detailRetur as $detail) {
                    $batchBarang = BatchBarang::where('id_batch', $detail->id_batch)->lockForUpdate()->first();
                    if (!$batchBarang) {
                        continue;
                    }

                    $jumlahDasar = (int) $detail->jumlah_retur * (int) $detail->nilai_konversi;
                    $batchBarang->increment(
                        'sisa_persediaan',
                        $jumlahDasar
                    );
                }
            }

            DetailRetur::where('id_retur', $retur->id_retur)->delete();
            $retur->delete();
        });

        return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus!');
    }

    public function accept(string $id_retur)
    {
        Retur::where('id_retur', $id_retur)->update(['status' => 'Diretur']);
        return redirect()->route('retur.index')->with('success', 'Retur berhasil diterima!');
    }

    public function reject(string $id_retur)
    {
        Retur::where('id_retur', $id_retur)->update(['status' => 'Ditolak']);
        return redirect()->route('retur.index')->with('success', 'Retur ditolak!');
    }

    public function cetakPdf(string $id_retur)
    {
        $retur = Retur::with(['penolakan', 'pembelian.supplier', 'detailRetur.batch.barang', 'detailRetur.satuan'])->findOrFail($id_retur);
        $folderRole = $this->getFolderRole();

        $pdf = Pdf::loadView("{$folderRole}.retur.pdf", compact('retur'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Retur_' .  $retur->no_retur . '.pdf');
    }

    public function autoCompleteReturPembelian(Request $request)
    {
        $search = $request->input('term');

        $pembelian = Pembelian::with('supplier')
            ->where('no_nota', 'LIKE', '%' . $search . '%')
            ->limit(5)
            ->get();

        $result = $pembelian->map(function ($item) {
            return [
                'label' => $item->no_nota,
                'value' => $item->no_nota,
                'id_pembelian' => $item->id_pembelian,
                'id_supplier' => $item->id_supplier,
                'nama_supplier' =>
                $item->supplier?->nama_supplier ?? '',
            ];
        })->values();

        return response()->json($result);
    }

    public function getPenolakanDetails(string $id_penolakan)
    {
        $details = DetailPenolakan::with([
            'batch.barang',
            'batch.detailPenerimaan.penerimaan.pembelian.detailPembelian.satuan',
            'batch.detailPenerimaan.penerimaan.pembelian.supplier',
        ])->where('id_penolakan', $id_penolakan)->get();

        $result = $details->map(function ($detail) {

            $batch = $detail->batch;

            if (!$batch) {
                return [
                    'id_detail_penolakan' => $detail->id_detail_penolakan,
                    'id_batch' => null,
                    'id_barang' => null,
                    'nama_barang' => '',
                    'batch_barang' => '',
                    'expired_date' => '',
                    'jumlah_ditolak' => $detail->jumlah_ditolak,
                    'id_satuan' => null,
                    'kode_satuan' => '',
                    'nilai_konversi' => 0,
                    'deskripsi' => $detail->deskripsi ?? '',
                ];
            }

            /*Ambil detail penerimaan dari batch*/

            $detailPenerimaan = $batch->detailPenerimaan->first();

            if (!$detailPenerimaan) {
                return [
                    'id_detail_penolakan' => $detail->id_detail_penolakan,
                    'id_batch' => $batch->id_batch,
                    'id_barang' => $batch->id_barang,
                    'nama_barang' => $batch->barang?->nama_barang ?? '',
                    'batch_barang' => $batch->kode_lot_supplier ?? '',
                    'expired_date' => $batch->expired_date ?? '',
                    'jumlah_ditolak' => $detail->jumlah_ditolak,
                    'id_satuan' => null,
                    'kode_satuan' => '',
                    'nilai_konversi' => 0,
                    'deskripsi' => $detail->deskripsi ?? '',
                ];
            }

            /*Ambil pembelian dari penerimaan*/
            $pembelian = $detailPenerimaan->penerimaan?->pembelian;

            /*Cari detail pembelian sesuai barang*/
            $detailPembelian = $pembelian?->detailPembelian->firstWhere('id_barang', $batch->id_barang);

            return [
                'id_detail_penolakan' => $detail->id_detail_penolakan,
                'id_batch' => $batch->id_batch,
                'id_barang' => $batch->id_barang,
                'nama_barang' => $batch->barang?->nama_barang ?? '',
                'batch_barang' => $batch->kode_lot_supplier ?? '',
                'expired_date' => $batch->expired_date ?? '',
                'jumlah_ditolak' => $detail->jumlah_ditolak,
                'id_satuan' => $detailPembelian?->id_satuan,
                'kode_satuan' => $detailPembelian?->satuan?->kode_satuan ?? '',

                /*Nilai konversi berasal dari penerimaan*/
                'nilai_konversi' => $detailPenerimaan->rasio_konversi,
                'deskripsi' => $detail->deskripsi ?? '',
                'id_pembelian' => $pembelian?->id_pembelian,
                'no_nota' => $pembelian?->no_nota ?? '',
                'id_supplier' => $pembelian?->id_supplier,
                'nama_supplier' => $pembelian?->supplier?->nama_supplier ?? '',
            ];
        })->values();

        return response()->json($result);
    }

    public function getPenerimaanDetails(string $id_penerimaan)
    {
        $penerimaan = Penerimaan::with([
            'pembelian.detailPembelian.satuan',
        ])->findOrFail($id_penerimaan);

        $details = DetailPenerimaan::with([
            'batch.barang'
        ])->where('id_penerimaan', $id_penerimaan)->where('jumlah_ditolak', '>', 0)->get();

        $result = $details->map(function ($detail) use ($penerimaan) {

            $idBarang = $detail->batch?->id_barang;

            /*Cari detail pembelian berdasarkan barang*/
            $detailPembelian = $penerimaan->pembelian?->detailPembelian->firstWhere('id_barang', $idBarang);

            return [
                'id_detail_penerimaan' => $detail->id_detail_penerimaan,
                'id_batch' => $detail->id_batch,
                'id_barang' => $idBarang,
                'nama_barang' => $detail->batch?->barang?->nama_barang ?? '',
                'batch_barang' => $detail->batch?->kode_lot_supplier ?? '',
                'expired_date' => $detail->batch?->expired_date ?? '',
                'jumlah_ditolak' => $detail->jumlah_ditolak,
                'id_satuan' => $detailPembelian?->id_satuan,
                'kode_satuan' => $detailPembelian?->satuan?->kode_satuan ?? '',
                'nilai_konversi' => $detail->rasio_konversi,
                'deskripsi' => $detail->deskripsi ?? '',
            ];
        })->values();

        return response()->json($result);
    }

    public function autocompletePenerimaan(Request $request)
    {
        $search = $request->input('term');

        $penerimaan = Penerimaan::with([
            'pembelian.supplier',
        ])
            ->where('no_registrasi', 'LIKE', '%' . $search . '%')
            ->limit(5)
            ->get();

        $result = $penerimaan->map(function ($item) {

            return [
                'label' => $item->no_registrasi,
                'value' => $item->no_registrasi,
                'id_penerimaan' => $item->id_penerimaan,
                'id_pembelian' => $item->id_pembelian,
                'no_nota' => $item->pembelian?->no_nota ?? '',
                'id_supplier' => $item->pembelian?->id_supplier,
                'supplier' => $item->pembelian?->supplier?->nama_supplier ?? '',
            ];
        })->values();

        return response()->json($result);
    }
}
