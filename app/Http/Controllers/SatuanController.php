<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSatuanRequest;
use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $satuan = Satuan::get();
        return view('admin-gudang.satuan.index', compact('satuan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin-gudang.satuan.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSatuanRequest $request)
    {
        $validated = $request->validated();
        Satuan::create($validated);

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_satuan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_satuan)
    {
        $satuan = Satuan::findOrFail($id_satuan);

        return view('admin-gudang.satuan.edit', compact('satuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSatuanRequest $request, string $id_satuan)
    {
        $validated = $request->validated();
        Satuan::findOrFail($id_satuan)->update($validated);

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_satuan)
    {
        Satuan::findOrFail($id_satuan)->delete();
        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil dihapus!');
    }
}
