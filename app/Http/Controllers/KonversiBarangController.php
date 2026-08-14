<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKonversiBarangRequest;
use App\Models\Barang;
use App\Models\KonversiBarang;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KonversiBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $konversiBarang = KonversiBarang::with('barang', 'satuan')->get();
        return view('admin-gudang.konversi-barang.index', compact('konversiBarang'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data_barang = Barang::select('id_barang', 'nama_barang')->get();
        $data_satuan = Satuan::select('id_satuan', 'kode_satuan')->get();

        return view('admin-gudang.konversi-barang.add', compact('data_barang', 'data_satuan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKonversiBarangRequest $request)
    {
        $validated = $request->validated();
        KonversiBarang::create([
            'id_barang' => $validated['id_barang'],
            'id_satuan' => $validated['id_satuan'], // Ambil nilai dari input 'id_satuan' form, masukkan ke kolom database 'id_satuan'
            'nilai_konversi' => $validated['nilai_konversi'],
        ]);

        return redirect()->route('konversi-barang.index')->with('success', 'Konversi bahan baku berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_konversi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_konversi)
    {
        $konversiBarang = KonversiBarang::with('barang', 'satuan')->findOrFail($id_konversi);
        $data_barang = Barang::select('id_barang', 'nama_barang')->get();
        $data_satuan = Satuan::select('id_satuan', 'kode_satuan')->get();

        return view('admin-gudang.konversi-barang.edit', compact('konversiBarang', 'data_barang', 'data_satuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreKonversiBarangRequest $request, string $id_konversi)
    {
        $validated = $request->validated();

        KonversiBarang::findOrFail($id_konversi)->update([
            'id_barang' => $validated['id_barang'],
            'id_satuan' => $validated['id_satuan'],
            'nilai_konversi' => $validated['nilai_konversi'],
        ]);

        return redirect()->route('konversi-barang.index')->with('success', 'Konversi bahan baku berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_konversi)
    {
        KonversiBarang::findOrFail($id_konversi)->delete();

        return redirect()->route('konversi-barang.index')->with('success', 'Konversi bahan baku berhasil dihapus!');
    }

    public function autocomplete(Request $request) // untuk auto complete barang
    {
        $barang = DB::table('barang')->select('id_barang', 'nama_barang')->where('nama_barang', 'LIKE', '%' . $request->term . '%')->limit(3)->get();
        $result = [];
        foreach ($barang as $b) {
            $result[] = [
                'label' => $b->nama_barang,
                'value' => $b->nama_barang,
                'id'    => $b->id_barang,
            ];
        }

        return response()->json($result);
    }
}
