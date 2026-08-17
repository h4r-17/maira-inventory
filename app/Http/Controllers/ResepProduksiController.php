<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResepProduksiRequest;
use App\Models\Barang;
use App\Models\BarangJadi;
use App\Models\ResepProduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResepProduksiController extends Controller
{
    public function index()
    {
        $resep = DB::table('resep_produksi')->join('barang_jadi', 'resep_produksi.id_produk', '=', 'barang_jadi.id_produk')
            ->select('resep_produksi.id_produk', 'barang_jadi.nama_produk')
            ->groupBy('resep_produksi.id_produk', 'barang_jadi.nama_produk')
            ->orderBy('barang_jadi.nama_produk')
            ->get();

        return view('admin-gudang.resep-produksi.index', compact('resep'));
    }

    public function show(string $id_produk)
    {
        $data = DB::table('resep_produksi')
            ->join('barang_jadi', 'resep_produksi.id_produk', '=', 'barang_jadi.id_produk')
            ->join('barang', 'resep_produksi.id_barang', '=', 'barang.id_barang')
            ->join('satuan', 'barang.id_satuan', '=', 'satuan.id_satuan')
            ->select(
                'resep_produksi.id_produk',
                'resep_produksi.id_barang',
                'resep_produksi.standar_kuantitas',
                'barang_jadi.nama_produk',
                'barang.nama_barang',
                'satuan.kode_satuan'
            )
            ->where('resep_produksi.id_produk', $id_produk)
            ->orderBy('barang.nama_barang', 'asc') // urutkan nama bahan baku dari A-Z
            ->get();

        // jika gada datanya maka tampilkan 404
        abort_if($data->isEmpty(), 404);

        // ambil baris pertama nama produk dari data resep produksi
        $produk = $data->first();

        return view('admin-gudang.resep-produksi.show', compact('data', 'produk'));
    }

    public function create()
    {
        $data_produk = BarangJadi::select('id_produk', 'nama_produk')->whereDoesntHave('resepProduksi')->get();
        $data_barang = Barang::select('id_barang', 'nama_barang')->get();

        return view('admin-gudang.resep-produksi.add', compact('data_produk', 'data_barang'));
    }

    public function store(StoreResepProduksiRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            // wadah array karena request nya berbentuk array
            $insertData = [];

            // masukin data ke array pake foreach karena berbentuk array
            foreach ($validated['id_barang'] as $key => $id_barang) {
                $insertData[] = [
                    'id_produk' => $validated['id_produk'],
                    'id_barang' => $id_barang,
                    'standar_kuantitas' => $validated['standar_kuantitas'][$key],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            ResepProduksi::insert($insertData);
        });

        return redirect()->route('resep-produksi.index')->with('success', 'Resep produksi berhasil ditambahkan!');
    }

    public function edit(string $id_produk)
    {
        // sama seperti show, tapi ditambahin data produk dan data barang untuk select option
        $data = DB::table('resep_produksi')
            ->join('barang_jadi', 'resep_produksi.id_produk', '=', 'barang_jadi.id_produk')
            ->join('barang', 'resep_produksi.id_barang', '=', 'barang.id_barang')
            ->join('satuan', 'barang.id_satuan', '=', 'satuan.id_satuan')
            ->select(
                'resep_produksi.id_produk',
                'resep_produksi.id_barang',
                'resep_produksi.standar_kuantitas',
                'barang_jadi.nama_produk',
                'barang.nama_barang',
                'satuan.kode_satuan'
            )
            ->where('resep_produksi.id_produk', $id_produk)
            ->orderBy('barang.nama_barang', 'asc')
            ->get();

        abort_if($data->isEmpty(), 404);

        $produk = $data->first();

        $data_produk = DB::table('barang_jadi')->get();
        $data_barang = DB::table('barang')->get();

        return view('admin-gudang.resep-produksi.edit', compact('data', 'produk', 'data_produk', 'data_barang'));
    }

    public function update(StoreResepProduksiRequest $request, string $id_produk)
    {
        $validated = $request->validated();

        // memastikan id Produk yang diubah sesuai dengan URL
        if ($validated['id_produk'] !== $id_produk) {
            throw ValidationException::withMessages([
                'id_produk' => 'Produk yang diedit tidak sesuai.',
            ]);
        }

        DB::transaction(function () use ($validated, $id_produk) {

            // hapus semua resep produksi yang ada untuk produk ini
            ResepProduksi::where('id_produk', $id_produk)->delete();

            $insertData = [];

            foreach ($validated['id_barang'] as $key => $id_barang) {
                $insertData[] = [
                    'id_produk' => $id_produk, // gunakan $id_produk dari parameter URL
                    'id_barang' => $id_barang,
                    'standar_kuantitas' => $validated['standar_kuantitas'][$key],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            ResepProduksi::insert($insertData);
        });

        return redirect()->route('resep-produksi.index')->with('success', 'Resep produksi berhasil diperbarui!');
    }

    public function destroy(string $id_produk)
    {
        ResepProduksi::where('id_produk', $id_produk)->delete();

        return redirect()->route('resep-produksi.index')->with('success', 'Resep produksi berhasil dihapus!');
    }
}
