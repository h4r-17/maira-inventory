<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProduksiRequest;
use App\Models\BatchBarang;
use App\Models\DetailProduksi;
use App\Models\Produksi;
use App\Models\ResepProduksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProduksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produksi = Produksi::with('barangJadi')->latest()->get();

        return view('admin-gudang.produksi.index', compact('produksi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin-gudang.produksi.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProduksiRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {

            /*1. Ambil semua batch yang akan digunakan berdasarkan FIFO*/
            $batchBarangCache = [];
            foreach ($validated['id_barang'] as $index => $id_barang) {
                $jumlahKeluar = (int) $validated['jumlah_keluar'][$index];
                if ($jumlahKeluar <= 0) {
                    throw ValidationException::withMessages([
                        "jumlah_keluar.{$index}" =>
                        "Jumlah keluar harus lebih dari 0."
                    ]);
                }

                /* Ambil batch untuk barang tersebut hanya satu kali. Batch diurutkan dari yang paling lama masuk.*/
                if (!isset($batchBarangCache[$id_barang])) {
                    $batchBarangCache[$id_barang] = BatchBarang::where(
                        'id_barang',
                        $id_barang
                    )
                        ->where('sisa_persediaan', '>', 0)
                        ->orderBy('created_at', 'asc')
                        ->orderBy('id_batch', 'asc')
                        ->lockForUpdate()
                        ->get();

                    if ($batchBarangCache[$id_barang]->isEmpty()) {
                        throw ValidationException::withMessages([
                            "id_barang.{$index}" =>
                            "Tidak terdapat persediaan untuk bahan baku tersebut."
                        ]);
                    }
                }
                /*Cek apakah total persediaan seluruh batch mencukupi*/
                $totalPersediaan = $batchBarangCache[$id_barang]
                    ->sum('sisa_persediaan');
                if ($totalPersediaan < $jumlahKeluar) {
                    throw ValidationException::withMessages([
                        "jumlah_keluar.{$index}" =>
                        "Persediaan bahan baku tidak mencukupi. " .
                            "Total persediaan: {$totalPersediaan}, " .
                            "jumlah yang dibutuhkan: {$jumlahKeluar}."
                    ]);
                }
            }
            /*2. Simpan header produksi*/
            $produksi = Produksi::create([
                'batch_produk' => $validated['batch_produk'],
                'tanggal_produksi' => $validated['tanggal_produksi'],
                'id_produk' => $validated['id_produk'],
                'hasil_produksi' => $validated['hasil_produksi'],
                'hasil_qc' => $validated['hasil_qc'],
                'produk_expired' => $validated['produk_expired'],
                'tujuan_produksi' => $validated['tujuan_produksi'],
            ]);

            /*3. Proses setiap bahan menggunakan FIFO*/
            foreach ($validated['id_barang'] as $index => $id_barang) {
                $jumlahDibutuhkan = (int) $validated['jumlah_keluar'][$index];
                $deskripsi = $validated['deskripsi'][$index] ?? null;
                $batches = $batchBarangCache[$id_barang];
                while ($jumlahDibutuhkan > 0) {

                    /*Cari batch pertama yang masih memiliki persediaan.*/
                    $batchBarang = $batches->first(function ($batch) {
                        return $batch->sisa_persediaan > 0;
                    });

                    if (!$batchBarang) {
                        throw ValidationException::withMessages([
                            "jumlah_keluar.{$index}" =>
                            "Persediaan bahan baku tidak mencukupi."
                        ]);
                    }

                    /*Tentukan jumlah yang diambil dari batch.*/
                    $jumlahAmbil = min(
                        $batchBarang->sisa_persediaan,
                        $jumlahDibutuhkan
                    );

                    /*Simpan detail produksi. Satu bahan dapat menghasilkan beberapa detail jika harus mengambil dari beberapa batch.*/
                    DetailProduksi::create([
                        'id_produksi' => $produksi->id_produksi,
                        'id_batch' => $batchBarang->id_batch,
                        'jumlah_keluar' => $jumlahAmbil,
                        'deskripsi' => $deskripsi,
                    ]);

                    /*Kurangi stok batch.*/
                    $batchBarang->decrement(
                        'sisa_persediaan',
                        $jumlahAmbil
                    );

                    /*Update nilai stok pada object agar iterasi berikutnya mengetahui sisa stok terbaru.*/
                    $batchBarang->sisa_persediaan -= $jumlahAmbil;

                    /*Kurangi kebutuhan bahan.*/
                    $jumlahDibutuhkan -= $jumlahAmbil;
                }
            }
        });

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_produksi)
    {
        $produksi = Produksi::with(['detailProduksi.batch.barang.satuan', 'barangJadi'])->findOrFail($id_produksi);

        return view('admin-gudang.produksi.show', compact('produksi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_produksi)
    {
        $produksi = Produksi::with(['detailProduksi.batch.barang', 'barangJadi'])->findOrFail($id_produksi);
        $resep = ResepProduksi::where('id_produk', $produksi->id_produk)->get()->keyBy('id_barang');

        return view('admin-gudang.produksi.edit', compact('produksi', 'resep'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProduksiRequest $request, string $id_produksi)
    {
        $validated = $request->validated();
        $produksi = Produksi::with('detailProduksi')->findOrFail($id_produksi);

        DB::transaction(function () use ($validated, $produksi) {

            /*1. Kembalikan stok dari detail produksi lama*/
            foreach ($produksi->detailProduksi as $detail) {
                $batchBarangLama = BatchBarang::where(
                    'id_batch',
                    $detail->id_batch
                )->lockForUpdate()->first();

                if ($batchBarangLama) {
                    $batchBarangLama->increment(
                        'sisa_persediaan',
                        $detail->jumlah_keluar
                    );
                }
            }

            /*2. Hapus detail produksi lama*/
            $produksi->detailProduksi()->delete();

            /*3. Update header produksi*/
            $produksi->update([
                'batch_produk' => $validated['batch_produk'],
                'tanggal_produksi' => $validated['tanggal_produksi'],
                'id_produk' => $validated['id_produk'],
                'hasil_produksi' => $validated['hasil_produksi'],
                'hasil_qc' => $validated['hasil_qc'],
                'produk_expired' => $validated['produk_expired'],
                'tujuan_produksi' => $validated['tujuan_produksi'],
            ]);


            /*4. Cache batch berdasarkan barang. Batch diurutkan berdasarkan FIFO: created_at paling lama → digunakan terlebih dahulu.*/
            $batchBarangCache = [];

            /*5. Proses seluruh bahan baku*/
            foreach ($validated['id_barang'] as $index => $id_barang) {
                $jumlahDibutuhkan = (int) $validated['jumlah_keluar'][$index];
                $deskripsi = $validated['deskripsi'][$index] ?? null;

                if ($jumlahDibutuhkan <= 0) {
                    throw ValidationException::withMessages([
                        "jumlah_keluar.{$index}" =>
                        "Jumlah keluar harus lebih dari 0."
                    ]);
                }

                /*Ambil batch FIFO untuk barang tersebut*/
                if (!isset($batchBarangCache[$id_barang])) {
                    $batchBarangCache[$id_barang] = BatchBarang::where(
                        'id_barang',
                        $id_barang
                    )
                        ->where('sisa_persediaan', '>', 0)
                        ->orderBy('created_at', 'asc')
                        ->orderBy('id_batch', 'asc')
                        ->lockForUpdate()
                        ->get();

                    if ($batchBarangCache[$id_barang]->isEmpty()) {
                        throw ValidationException::withMessages([
                            "id_barang.{$index}" =>
                            "Tidak terdapat persediaan untuk bahan baku tersebut."
                        ]);
                    }
                }

                $batches = $batchBarangCache[$id_barang];

                /*Cek total persediaan*/
                $totalPersediaan = $batches->sum('sisa_persediaan');

                if ($totalPersediaan < $jumlahDibutuhkan) {
                    throw ValidationException::withMessages([
                        "jumlah_keluar.{$index}" =>
                        "Persediaan bahan baku tidak mencukupi. " .
                            "Total persediaan: {$totalPersediaan}, " .
                            "jumlah yang dibutuhkan: {$jumlahDibutuhkan}."
                    ]);
                }

                /*6. Jalankan FIFO*/
                while ($jumlahDibutuhkan > 0) {
                    $batchBarang = $batches->first(function ($batch) {
                        return $batch->sisa_persediaan > 0;
                    });

                    if (!$batchBarang) {
                        throw ValidationException::withMessages([
                            "jumlah_keluar.{$index}" =>
                            "Persediaan bahan baku tidak mencukupi."
                        ]);
                    }

                    /*Tentukan jumlah yang diambil dari batch*/
                    $jumlahAmbil = min(
                        $batchBarang->sisa_persediaan,
                        $jumlahDibutuhkan
                    );

                    /*Simpan detail produksi*/
                    DetailProduksi::create([
                        'id_produksi' => $produksi->id_produksi,
                        'id_batch' => $batchBarang->id_batch,
                        'jumlah_keluar' => $jumlahAmbil,
                        'deskripsi' => $deskripsi,
                    ]);


                    /*Kurangi stok batch*/
                    $batchBarang->decrement(
                        'sisa_persediaan',
                        $jumlahAmbil
                    );

                    /*Update nilai object di dalam collection*/
                    $batchBarang->sisa_persediaan -= $jumlahAmbil;

                    /*Kurangi kebutuhan*/
                    $jumlahDibutuhkan -= $jumlahAmbil;
                }
            }
        });

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_produksi)
    {
        $produksi = Produksi::with('detailProduksi')->findOrFail($id_produksi);

        DB::transaction(function () use ($produksi) {
            foreach ($produksi->detailProduksi as $detail) {

                $batchBarang = BatchBarang::where(
                    'id_batch',
                    $detail->id_batch
                )->lockForUpdate()->first();

                if ($batchBarang) {
                    $batchBarang->increment(
                        'sisa_persediaan',
                        $detail->jumlah_keluar
                    );
                }
            }
            $produksi->delete();
        });

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil dihapus!');
    }

    public function autocompleteProduk(Request $request) // untuk auto complete produk
    {
        $produk = DB::table('barang_jadi')->select('id_produk', 'nama_produk', 'kode_produk')->where('nama_produk', 'LIKE', '%' . $request->term . '%')->limit(3)->get();
        $result = [];
        foreach ($produk as $p) {
            $result[] = [
                'label' => $p->nama_produk,
                'value' => $p->nama_produk,
                'id'    => $p->id_produk,
                'batch' => $p->kode_produk,
            ];
        }

        return response()->json($result);
    }

    public function cetakPdf(string $id_produksi)
    {
        $produksi = Produksi::with(['detailProduksi'])->findOrFail($id_produksi);
        $detailProduksi = $produksi->detailProduksi; // Mengambil detail produksi
        $pdf = Pdf::loadView('admin-gudang.produksi.pdf', compact('produksi', 'detailProduksi'))->setPaper('a4', 'portrait');

        return $pdf->stream('Produksi_' . $produksi->batch_produk . '.pdf');
    }

    public function ResepProduksi(string $id_produk)
    {
        $resepProduksi = DB::table('resep_produksi')
            ->join('barang', 'resep_produksi.id_barang', '=', 'barang.id_barang')
            ->join('satuan', 'barang.id_satuan', '=', 'satuan.id_satuan')
            ->select(
                'resep_produksi.id_resep',
                'resep_produksi.id_produk',
                'resep_produksi.id_barang',
                'resep_produksi.standar_kuantitas',
                'barang.nama_barang',
                'satuan.id_satuan',
                'satuan.kode_satuan',
                DB::raw('(SELECT COALESCE(SUM(sisa_persediaan), 0) FROM batch_barang WHERE batch_barang.id_barang = resep_produksi.id_barang) as total_stok')
            )
            ->where('resep_produksi.id_produk', $id_produk)
            ->orderBy('barang.nama_barang')
            ->get();

        return response()->json($resepProduksi);
    }
}
