<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBarangRequest;
use App\Models\Barang;
use App\Models\Satuan;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barang = Barang::with('satuan:id_satuan,kode_satuan')->withSum('batchBarang', 'sisa_persediaan')->get();

        return view("admin-gudang.barang.index", compact('barang'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data_satuan = Satuan::select('id_satuan', 'kode_satuan')->get(); // ngambil data satuan dari tabel satuan ajah
        $kode_barang = Barang::generateKodeBarang();

        return view('admin-gudang.barang.add', compact('data_satuan', 'kode_barang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBarangRequest $request)
    {
        $validated = $request->validated();
        Barang::create($validated);

        return redirect()->route('barang.index')->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_barang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_barang)
    {
        $data_satuan = Satuan::select('id_satuan', 'kode_satuan')->get(); // ngambil data satuan dari tabel satuan ajah
        $barang = Barang::with('satuan')->where('id_barang', $id_barang)->firstOrFail(); // ngambil data barang dari tabel barang bareng dengan data satuannya

        return view('admin-gudang.barang.edit', compact('barang', 'data_satuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBarangRequest $request, string $id_barang)
    {
        $validated = $request->validated();
        Barang::findOrFail($id_barang)->update($validated);

        return redirect()->route('barang.index')->with('success', 'Bahan baku berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_barang)
    {
        Barang::findOrFail($id_barang)->delete();

        return redirect()->route('barang.index')->with('success', 'Bahan baku berhasil dihapus!');
    }
}
