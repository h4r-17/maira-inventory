<?php

namespace App\Http\Controllers;

use App\Models\BatchBarang;
use Illuminate\Http\Request;

class BatchBarangController extends Controller
{
    public function index()
    {
        $batchBarang = BatchBarang::with('barang')->orderBy('id_batch')->get();

        return view('admin-gudang.kode-batch.index', compact('batchBarang'));
    }
}
