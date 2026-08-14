<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBarangJadiRequest;
use App\Models\BarangJadi;
use App\Models\Satuan;

class BarangJadiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangJadi = BarangJadi::with('satuan:id_satuan,kode_satuan')->get();

        return view("admin-gudang.barang-jadi.index", compact('barangJadi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data_satuan = Satuan::select('id_satuan', 'kode_satuan')->get(); // ngambil data satuan dari tabel satuan ajah
        $kodeProduk = BarangJadi::generateKodeProduk();

        return view('admin-gudang.barang-jadi.add', compact('data_satuan', 'kodeProduk'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBarangJadiRequest $request)
    {
        $validated = $request->validated();
        BarangJadi::create($validated);

        return redirect()->route('barang-jadi.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_produk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_produk)
    {
        $data_satuan = Satuan::select('id_satuan', 'kode_satuan')->get();
        $barangJadi = BarangJadi::with('satuan')->where('id_produk', $id_produk)->firstOrFail();

        return view('admin-gudang.barang-jadi.edit', compact('barangJadi', 'data_satuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBarangJadiRequest $request, string $id_produk)
    {
        $validated = $request->validated();
        BarangJadi::findOrFail($id_produk)->update($validated);

        return redirect()->route('barang-jadi.index')->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_produk)
    {
        $barangJadi = BarangJadi::findOrFail($id_produk);
        $barangJadi->delete();

        return redirect()->route('barang-jadi.index')->with('success', 'Produk berhasil dihapus!');
    }
}
