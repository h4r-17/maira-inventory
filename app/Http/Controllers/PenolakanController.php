<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenolakanRequest;
use App\Models\BatchBarang;
use App\Models\DetailPenolakan;
use App\Models\Penolakan;
use App\Models\Produksi;
use App\Models\ResepProduksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PenolakanController extends Controller
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
        $penolakan = Penolakan::with('produksi')->get();
        $folderRole = $this->getFolderRole();


        return view("{$folderRole}.penolakan.index", compact('penolakan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $penolakan = Penolakan::with('produksi')->get();
        $kodePenolakan = Penolakan::generateKodePenolakan();

        return view('admin-gudang.penolakan.add', compact('kodePenolakan', 'penolakan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePenolakanRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {

            /*1. Validasi produksi*/
            $produksi = Produksi::find($validated['id_produksi']);
            if (!$produksi) {
                throw ValidationException::withMessages([
                    'id_produksi' => 'Data produksi tidak ditemukan.'
                ]);
            }

            /*2. Ambil bahan baku yang terdapat pada resep produksi*/
            $idBarangResep = ResepProduksi::where(
                'id_produk',
                $produksi->id_produk
            )->pluck('id_barang');

            /*3. Validasi dan kunci seluruh batch yang dipilih*/
            $batchBarangItems = [];

            foreach ($validated['id_batch'] as $index => $id_batch) {
                $jumlahDitolak = (int) $validated['jumlah_ditolak'][$index];

                if ($jumlahDitolak <= 0) {
                    throw ValidationException::withMessages([
                        "jumlah_ditolak.{$index}" =>
                        'Jumlah ditolak harus lebih dari 0.'
                    ]);
                }

                $batchBarang = BatchBarang::where(
                    'id_batch',
                    $id_batch
                )
                    ->lockForUpdate()
                    ->first();

                if (!$batchBarang) {
                    throw ValidationException::withMessages([
                        "id_batch.{$index}" =>
                        'Batch yang dipilih tidak ditemukan.'
                    ]);
                }

                /*Pastikan batch merupakan bahan baku yang digunakan oleh produk pada produksi tersebut*/

                if (!$idBarangResep->contains($batchBarang->id_barang)) {
                    throw ValidationException::withMessages([
                        "id_batch.{$index}" =>
                        "Batch '{$batchBarang->kode_batch}' " .
                            "bukan bahan baku yang digunakan pada produksi tersebut."
                    ]);
                }

                /*Pastikan stok mencukupi*/

                if ($batchBarang->sisa_persediaan < $jumlahDitolak) {
                    throw ValidationException::withMessages([
                        "jumlah_ditolak.{$index}" =>
                        "Sisa persediaan batch '{$batchBarang->kode_batch}' " .
                            "tidak mencukupi! " .
                            "(Sisa persediaan: {$batchBarang->sisa_persediaan}, " .
                            "jumlah yang diminta: {$jumlahDitolak})."
                    ]);
                }

                $batchBarangItems[$index] = $batchBarang;
            }

            /*4. Simpan header penolakan*/

            $penolakan = Penolakan::create([
                'no_penolakan' => $validated['no_penolakan'],
                'tanggal_penolakan' => $validated['tanggal_penolakan'],
                'id_produksi' => $validated['id_produksi'],
                'status' => $validated['status'],
            ]);

            /*5. Simpan detail dan kurangi stok*/

            foreach ($validated['id_batch'] as $index => $id_batch) {
                $batchBarang = $batchBarangItems[$index];
                $jumlahDitolak = (int) $validated['jumlah_ditolak'][$index];

                DetailPenolakan::create([
                    'id_penolakan' => $penolakan->id_penolakan,
                    'id_batch' => $batchBarang->id_batch,
                    'jumlah_ditolak' => $jumlahDitolak,
                    'alasan_penolakan' =>
                    $validated['alasan_penolakan'][$index],
                    'deskripsi' =>
                    $validated['deskripsi'][$index] ?? null,
                ]);


                /*Bahan sudah dipisahkan sebagai bahan ditolak, sehingga tidak lagi tersedia untuk produksi*/
                $batchBarang->decrement(
                    'sisa_persediaan',
                    $jumlahDitolak
                );
            }
        });

        return redirect()->route('penolakan.index')->with('success', 'Penolakan berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_penolakan)
    {
        $penolakan = Penolakan::with('produksi', 'detailPenolakan.batch.barang')->findOrFail($id_penolakan);

        return view('admin-gudang.penolakan.show', compact('penolakan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_penolakan)
    {
        $penolakan = Penolakan::with('produksi', 'detailPenolakan.batch.barang')->findOrFail($id_penolakan);

        return view('admin-gudang.penolakan.edit', compact('penolakan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePenolakanRequest $request, string $id_penolakan)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $id_penolakan) {

            /*1. Ambil data penolakan lama*/
            $penolakan = Penolakan::with('detailPenolakan')
                ->findOrFail($id_penolakan);


            /*2. Validasi produksi baru*/
            $produksi = Produksi::find($validated['id_produksi']);

            if (!$produksi) {
                throw ValidationException::withMessages([
                    'id_produksi' => 'Data produksi tidak ditemukan.'
                ]);
            }

            /*3. Ambil bahan baku yang digunakan produk
        */

            $idBarangResep = ResepProduksi::where(
                'id_produk',
                $produksi->id_produk
            )->pluck('id_barang');


            /*Kembalikan stok dari detail penolakan lama*/
            foreach ($penolakan->detailPenolakan as $oldDetail) {
                $batchLama = BatchBarang::where(
                    'id_batch',
                    $oldDetail->id_batch
                )->lockForUpdate()->first();

                if ($batchLama) {
                    $batchLama->increment(
                        'sisa_persediaan',
                        $oldDetail->jumlah_ditolak
                    );
                }
            }


            /*5. Validasi batch baru*/
            $batchBarangItems = [];
            foreach ($validated['id_batch'] as $index => $id_batch) {
                $jumlahDitolak = (int) $validated['jumlah_ditolak'][$index];
                if ($jumlahDitolak <= 0) {
                    throw ValidationException::withMessages([
                        "jumlah_ditolak.{$index}" =>
                        'Jumlah ditolak harus lebih dari 0.'
                    ]);
                }

                /*Cari batch berdasarkan id_batch*/
                $batchBarang = BatchBarang::where(
                    'id_batch',
                    $id_batch
                )->lockForUpdate()->first();

                if (!$batchBarang) {
                    throw ValidationException::withMessages([
                        "id_batch.{$index}" =>
                        'Batch yang dipilih tidak ditemukan.'
                    ]);
                }

                /*Pastikan batch merupakan bahan resep produksi*/
                if (!$idBarangResep->contains($batchBarang->id_barang)) {
                    throw ValidationException::withMessages([
                        "id_batch.{$index}" =>
                        "Batch '{$batchBarang->kode_batch}' " .
                            "bukan bahan baku yang digunakan pada produksi tersebut."
                    ]);
                }

                /*Pastikan id_barang sesuai dengan batch*/
                if (
                    isset($validated['id_barang'][$index]) &&
                    $batchBarang->id_barang != $validated['id_barang'][$index]
                ) {
                    throw ValidationException::withMessages([
                        "id_batch.{$index}" =>
                        "Batch '{$batchBarang->kode_batch}' " .
                            "tidak sesuai dengan bahan baku yang dipilih."
                    ]);
                }

                /*Cek stok*/
                if ($batchBarang->sisa_persediaan < $jumlahDitolak) {
                    throw ValidationException::withMessages([
                        "jumlah_ditolak.{$index}" => "Sisa persediaan batch '{$batchBarang->kode_batch}' " . "tidak mencukupi! " . "(Sisa persediaan: {$batchBarang->sisa_persediaan}, " . "jumlah yang diminta: {$jumlahDitolak})."
                    ]);
                }
                $batchBarangItems[$index] = $batchBarang;
            }

            /*6. Update header penolakan
        */
            $penolakan->update([
                'no_penolakan' => $validated['no_penolakan'],
                'tanggal_penolakan' => $validated['tanggal_penolakan'],
                'id_produksi' => $validated['id_produksi'],
                'status' => $validated['status'],
            ]);

            /*7. Hapus detail lama*/
            DetailPenolakan::where(
                'id_penolakan',
                $penolakan->id_penolakan
            )->delete();

            /*8. Simpan detail baru dan kurangi stok*/
            foreach ($validated['id_batch'] as $index => $id_batch) {
                $batchBarang = $batchBarangItems[$index];
                $jumlahDitolak = (int) $validated['jumlah_ditolak'][$index];

                DetailPenolakan::create([
                    'id_penolakan' => $penolakan->id_penolakan,
                    'id_batch' => $batchBarang->id_batch,
                    'jumlah_ditolak' => $jumlahDitolak,
                    'alasan_penolakan' =>
                    $validated['alasan_penolakan'][$index],
                    'deskripsi' =>
                    $validated['deskripsi'][$index] ?? null,
                ]);

                $batchBarang->decrement('sisa_persediaan', $jumlahDitolak);
            }
        });

        return redirect()->route('penolakan.index')->with('success', 'Penolakan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_penolakan)
    {
        $penolakan = Penolakan::with('detailPenolakan')
            ->findOrFail($id_penolakan);

        DB::transaction(function () use ($penolakan) {
            foreach ($penolakan->detailPenolakan as $detail) {
                $batch = BatchBarang::where(
                    'id_batch',
                    $detail->id_batch
                )->lockForUpdate()->first();
                if ($batch) {
                    $batch->increment(
                        'sisa_persediaan',
                        $detail->jumlah_ditolak
                    );
                }
            }

            DetailPenolakan::where(
                'id_penolakan',
                $penolakan->id_penolakan
            )->delete();

            $penolakan->delete();
        });

        return redirect()->route('penolakan.index')->with(
            'success',
            'Penolakan berhasil dihapus!'
        );
    }

    public function autoCompleteBatchProduk(Request $request) // untuk auto complete batch produksi
    {
        $produksi = DB::table('produksi')->select('id_produksi', 'batch_produk', 'tanggal_produksi')->where('batch_produk', 'LIKE', '%' . $request->term . '%')->limit(3)->get();
        $result = [];
        foreach ($produksi as $b) {
            $result[] = [
                'label' => $b->batch_produk,
                'value' => $b->batch_produk,
                'id'    => $b->id_produksi,
                'tanggal_produksi' => $b->tanggal_produksi,
            ];
        }

        return response()->json($result);
    }

    public function cetakPdf(string $id_penolakan)
    {
        $penolakan = Penolakan::with('detailPenolakan.batch')->findOrFail($id_penolakan);
        $pdf = Pdf::loadView('admin-gudang.penolakan.pdf', compact('penolakan'))
            ->setPaper('a4', 'potrait');

        return $pdf->stream('Penolakan_' . $penolakan->kode_penolakan . '.pdf');
    }

    public function autoCompleteBatchPenolakan(Request $request)
    {
        $term = $request->term;
        $idProduksi = $request->id_produksi;

        if (!$idProduksi) {
            return response()->json([]);
        }

        $produksi = Produksi::find($idProduksi);

        if (!$produksi) {
            return response()->json([]);
        }

        // Ambil bahan baku yang dibutuhkan oleh produk tersebut
        $idBarangResep = ResepProduksi::where('id_produk', $produksi->id_produk)
            ->pluck('id_barang');

        $batch = BatchBarang::with('barang')
            ->whereIn('id_barang', $idBarangResep)
            ->where('sisa_persediaan', '>', 0)
            ->where(function ($query) use ($term) {
                $query->where('kode_batch', 'LIKE', '%' . $term . '%')
                    ->orWhere('kode_lot_supplier', 'LIKE', '%' . $term . '%');
            })
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        $result = [];

        foreach ($batch as $b) {
            $result[] = [
                'label' => $b->kode_batch . ' - ' . $b->barang->nama_barang,
                'value' => $b->kode_batch,
                'id_batch' => $b->id_batch,
                'id_barang' => $b->id_barang,
                'nama_barang' => $b->barang->nama_barang,
                'sisa_persediaan' => $b->sisa_persediaan,
            ];
        }

        return response()->json($result);
    }
}
