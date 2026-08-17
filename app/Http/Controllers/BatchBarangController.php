<?php

namespace App\Http\Controllers;

use App\Models\BatchBarang;
use Illuminate\Http\Request;

class BatchBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = BatchBarang::with('barang');

        if ($request->filled('bahan_baku')) {
            $query->where('id_barang', $request->bahan_baku);
        }

        if ($request->filled('sort_tanggal')) {
            if ($request->sort_tanggal == 'terbaru') {
                $query->orderBy('expired_date', 'desc');
            } elseif ($request->sort_tanggal == 'terlama') {
                $query->orderBy('expired_date', 'asc');
            }
        } else {
            $query->orderBy('id_batch', 'desc');
        }

        $batchBarang = $query->paginate(10)->withQueryString();
        $barangs = \App\Models\Barang::orderBy('nama_barang', 'asc')->get();

        return view('admin-gudang.kode-batch.index', compact('batchBarang', 'barangs'));
    }
}
