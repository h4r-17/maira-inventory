<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembelianRequest;
use App\Models\DetailPembelian;
use App\Models\Pembelian;
use App\Models\Pengajuan;
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


class PembelianController extends Controller
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
        $pembelian = Pembelian::with('supplier:id_supplier,nama_supplier')->latest()->get();
        $folderRole = $this->getFolderRole();

        return view("{$folderRole}.pembelian.index", compact('pembelian'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kodeNota = Pembelian::generateNoNota();
        $data_supplier = Supplier::select('id_supplier', 'nama_supplier')->get();
        $data_satuan = Satuan::select('id_satuan', 'kode_satuan')->whereNotIn('kode_satuan', ['PCS', 'GRAM'])->get();
        $data_pengajuan = Pengajuan::select('id_pengajuan', 'no_pengajuan')->get();

        return view('bagian-keuangan.pembelian.add', compact('kodeNota', 'data_supplier', 'data_satuan', 'data_pengajuan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePembelianRequest $request)
    {
        $validated = $request->validated();

        if (count($validated['id_barang'] ?? []) !== count(array_unique($validated['id_barang'] ?? []))) {
            throw ValidationException::withMessages([
                'id_barang' => 'Bahan baku tidak boleh sama dalam satu pengajuan.',
            ]);
        }

        try {
            $supplier = Supplier::findOrFail($validated['id_supplier']);
            $tarifPajak = Str::contains(strtoupper($supplier->nama_supplier), ['PT', 'CV']) ? 0.11 : 0; // cek nama mengandung PT atau CV

            DB::transaction(function () use ($validated, $tarifPajak) {

                $pembelian = Pembelian::create([
                    'no_nota' => $validated['no_nota'],
                    'tanggal_pembelian' => $validated['tanggal_pembelian'],
                    'id_pengajuan' => $validated['id_pengajuan'],
                    'id_supplier' => $validated['id_supplier'],
                    'cara_bayar' => $validated['cara_bayar'],
                ]);

                foreach ($validated['id_barang'] as $index => $barang) {

                    $harga = $validated['harga'][$index] ?? 0;
                    $kuantitas = $validated['kuantitas'][$index] ?? 0;
                    $subtotal = $harga * $kuantitas;
                    $nominalPajak = $subtotal * $tarifPajak; // jika kena pajak, hitung 11% dari subtotal. jika tidak, ga kena pajak

                    DetailPembelian::create([
                        'id_pembelian' => $pembelian->id_pembelian,
                        'id_barang' => $barang,
                        'deskripsi' => $validated['deskripsi'][$index] ?? null,
                        'kuantitas' => $validated['kuantitas'][$index] ?? 0,
                        'id_satuan' => $validated['id_satuan'][$index] ?? null,
                        'harga' => $harga,
                        'pajak' => $nominalPajak,
                        'diskon' => $validated['diskon'][$index] ?? null,
                    ]);
                }
            });

            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil ditambahkan!');
        } catch (Throwable $e) {
            Log::error('Error saat create pembelian: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan, silakan coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_pembelian)
    {
        $pembelian = Pembelian::with(['supplier', 'detailPembelian.barang', 'detailPembelian.satuan'])
            ->findOrFail($id_pembelian);
        $folderRole = $this->getFolderRole();

        return view("{$folderRole}.pembelian.show", compact('pembelian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_pembelian)
    {
        $pembelian = Pembelian::with(['detailPembelian.barang', 'detailPembelian.satuan'])
            ->findOrFail($id_pembelian);
        $data_supplier = Supplier::select('id_supplier', 'nama_supplier')->get();
        $data_satuan = Satuan::select('id_satuan', 'kode_satuan')->whereNotIn('kode_satuan', ['PCS', 'GRAM'])->get();
        $data_pengajuan = DB::table('pengajuan')->get();

        return view('bagian-keuangan.pembelian.edit', compact('pembelian', 'data_supplier', 'data_satuan', 'data_pengajuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePembelianRequest $request, string $id_pembelian)
    {
        $validated = $request->validated();

        if (count($validated['id_barang'] ?? []) !== count(array_unique($validated['id_barang'] ?? []))) {
            throw ValidationException::withMessages([
                'id_barang' => 'Bahan baku tidak boleh sama dalam satu pembelian.',
            ]);
        }

        try {
            $supplier = Supplier::findOrFail($validated['id_supplier']);
            $tarifPajak = Str::contains(strtoupper($supplier->nama_supplier), ['PT', 'CV']) ? 0.11 : 0; // cek nama mengandung PT atau CV

            DB::transaction(function () use ($validated, $id_pembelian, $tarifPajak) {
                Pembelian::where('id_pembelian', $id_pembelian)->update([
                    'no_nota' => $validated['no_nota'],
                    'tanggal_pembelian' => $validated['tanggal_pembelian'],
                    'id_pengajuan' => $validated['id_pengajuan'],
                    'id_supplier' => $validated['id_supplier'],
                    'cara_bayar' => $validated['cara_bayar'],
                ]);

                // hapus detail pengajuan yang tidak ada di request
                DetailPembelian::where('id_pembelian', $id_pembelian)->whereNotIn('id_barang', $validated['id_barang'])->delete();

                foreach ($validated['id_barang'] as $index => $barang) {
                    $harga = $validated['harga'][$index] ?? 0;
                    $kuantitas = $validated['kuantitas'][$index] ?? 0;

                    $subtotal = $harga * $kuantitas;
                    $nominalPajak = $subtotal * $tarifPajak; // jika kena pajak, hitung 11% dari subtotal. jika tidak, ga kena pajak

                    DetailPembelian::updateOrCreate([
                        'id_pembelian' => $id_pembelian,
                        'id_barang' => $barang,
                    ], [
                        'deskripsi' => $validated['deskripsi'][$index] ?? null,
                        'kuantitas' => $validated['kuantitas'][$index] ?? 0,
                        'id_satuan' => $validated['id_satuan'][$index] ?? null,
                        'harga' => $harga,
                        'pajak' => $nominalPajak,
                        'diskon' => $validated['diskon'][$index] ?? null,
                    ]);
                }
            });

            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil diperbarui!');
        } catch (Throwable $e) {
            Log::error('Error saat update pembelian ID ' . $id_pembelian . ': ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan, silakan coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_pembelian)
    {
        try {
            Pembelian::findOrFail($id_pembelian)->delete();

            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dihapus!');
        } catch (Throwable $e) {
            Log::error('Error saat hapus pembelian ID ' . $id_pembelian . ': ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan, silakan coba lagi');
        }
    }

    public function autoCompletePengajuan(Request $request)
    {
        $pengajuan = Pengajuan::with(['detailPengajuan.barang', 'detailPengajuan.satuan'])
            ->where('no_pengajuan', 'LIKE', '%' . $request->term . '%')
            ->limit(3)
            ->latest()
            ->get();

        $result = $pengajuan->map(function ($p) {
            return [
                'label' => $p->no_pengajuan,
                'value' => $p->no_pengajuan,
                'id' => $p->id_pengajuan,
                'details' => $p->detailPengajuan->map(function ($detail) {
                    return [
                        'id_barang' => $detail->id_barang,
                        'nama_barang' => $detail->barang?->nama_barang,
                        'kuantitas' => $detail->kuantitas,
                        'id_satuan' => $detail->id_satuan,
                        'kode_satuan' => $detail->satuan?->kode_satuan,
                        'harga' => $detail->harga,
                        'deskripsi' => $detail->deskripsi,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json($result);
    }

    public function cetakPdf(string $id_pembelian)
    {
        $pembelian = Pembelian::with(['supplier', 'pengajuan', 'detailPembelian.barang', 'detailPembelian.satuan'])->findOrFail($id_pembelian);
        $folderRole = $this->getFolderRole();
        $pdf = Pdf::loadView("{$folderRole}.pembelian.pdf", compact('pembelian'))->setPaper('a4', 'portrait');

        return $pdf->stream($pembelian->no_nota . '.pdf');
    }
}
