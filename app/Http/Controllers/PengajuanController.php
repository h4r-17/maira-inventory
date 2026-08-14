<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanRequest;
use App\Models\DetailPengajuan;
use App\Models\Pengajuan;
use App\Models\Satuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class PengajuanController extends Controller
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
        $pengajuan = Pengajuan::get();
        $folderRole = $this->getFolderRole();

        return view("{$folderRole}.pengajuan.index", compact('pengajuan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data_satuan = Satuan::select('id_satuan', 'kode_satuan')->whereNotIn('kode_satuan', ['PCS', 'GRAM'])->get();
        $kodePengajuan = Pengajuan::generateKode();

        return view('admin-gudang.pengajuan.add', compact('data_satuan', 'kodePengajuan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengajuanRequest $request)
    {
        $validated = $request->validated();

        if (count($validated['id_barang'] ?? []) !== count(array_unique($validated['id_barang'] ?? []))) {
            throw ValidationException::withMessages([
                'id_barang' => 'Bahan baku tidak boleh sama dalam satu pengajuan.',
            ]);
        }

        try {
            DB::transaction(function () use ($validated) {

                $pengajuan = Pengajuan::create([
                    'no_pengajuan'    => $validated['no_pengajuan'],
                    'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                    'status'            => 'Pending',
                ]);

                foreach ($validated['id_barang'] as $index => $barang) {

                    DetailPengajuan::create([
                        'id_pengajuan' => $pengajuan->id_pengajuan,
                        'id_barang'    => $barang,
                        'kuantitas'    => $validated['kuantitas'][$index] ?? 0,
                        'id_satuan'    => $validated['id_satuan'][$index] ?? null,
                        'harga'        => $validated['harga'][$index] ?? null,
                        'deskripsi'    => $validated['deskripsi'][$index] ?? null,
                    ]);
                }
            });

            return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil ditambahkan!');
        } catch (Throwable $e) {
            // catet log error ke file log
            Log::error('Error saat create pengajuan: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_pengajuan)
    {
        $pengajuan = Pengajuan::with(['detailPengajuan.barang', 'detailPengajuan.satuan'])
            ->findOrFail($id_pengajuan);
        $folderRole = $this->getFolderRole();

        return view("{$folderRole}.pengajuan.show", compact('pengajuan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_pengajuan)
    {
        $pengajuan = Pengajuan::with(['detailPengajuan.barang', 'detailPengajuan.satuan'])
            ->findOrFail($id_pengajuan);
        $data_satuan = Satuan::select('id_satuan', 'kode_satuan')->whereNotIn('kode_satuan', ['PCS', 'GRAM'])->get();

        return view('admin-gudang.pengajuan.edit', compact('pengajuan', 'data_satuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePengajuanRequest $request, string $id_pengajuan)
    {
        $validated = $request->validated();

        if (count($validated['id_barang'] ?? []) !== count(array_unique($validated['id_barang'] ?? []))) {
            throw ValidationException::withMessages([
                'id_barang' => 'Bahan baku tidak boleh sama dalam satu pengajuan.',
            ]);
        }

        try {
            DB::transaction(function () use ($validated, $id_pengajuan) {
                Pengajuan::where('id_pengajuan', $id_pengajuan)->update([
                    'no_pengajuan'    => $validated['no_pengajuan'],
                    'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                    'status'            => 'Pending',
                ]);

                // hapus detail pengajuan yang tidak ada di request
                DetailPengajuan::where('id_pengajuan', $id_pengajuan)
                    ->whereNotIn('id_barang', $validated['id_barang'])
                    ->delete();

                foreach ($validated['id_barang'] as $index => $barang) {
                    DetailPengajuan::updateOrCreate([
                        'id_pengajuan' => $id_pengajuan,
                        'id_barang'    => $barang,
                    ], [
                        'kuantitas'    => $validated['kuantitas'][$index] ?? 0,
                        'id_satuan'    => $validated['id_satuan'][$index] ?? null,
                        'harga'        => $validated['harga'][$index] ?? null,
                        'deskripsi'    => $validated['deskripsi'][$index] ?? null,
                    ]);
                }
            });

            return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil diperbarui!');
        } catch (Throwable $e) {
            // catet log error ke file log
            Log::error('Error saat update pengajuan ID ' . $id_pengajuan . ': ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_pengajuan)
    {
        try {
            DB::transaction(function () use ($id_pengajuan) {
                $pengajuan = Pengajuan::findOrFail($id_pengajuan);
                // Hapus detail pengajuan terlebih dahulu
                DetailPengajuan::where('id_pengajuan', $id_pengajuan)->delete();

                $pengajuan->delete();
            });

            return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dihapus!');
        } catch (Throwable $e) {
            // catet log error ke file log
            Log::error('Error saat hapus pengajuan ID ' . $id_pengajuan . ': ' . $e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan, silakan coba lagi.');
        }
    }

    public function accept(string $id_pengajuan)
    {
        Pengajuan::where('id_pengajuan', $id_pengajuan)->update(['status' => 'Disetujui']);
        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil disetujui!');
    }

    public function reject(string $id_pengajuan)
    {
        Pengajuan::where('id_pengajuan', $id_pengajuan)->update(['status' => 'Ditolak']);
        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil ditolak!');
    }

    public function pending(string $id_pengajuan)
    {
        Pengajuan::where('id_pengajuan', $id_pengajuan)->update(['status' => 'Pending']);
        return redirect()->route('pengajuan.index')->with('success', 'Status pengajuan berhasil diubah!');
    }

    public function cetakPdf(string $id_pengajuan)
    {
        $pengajuan = Pengajuan::with(['detailPengajuan.barang', 'detailPengajuan.satuan'])
            ->findOrFail($id_pengajuan);

        $total_harga = $pengajuan->detailPengajuan?->sum(function ($item) {
            return $item->harga !== null ? $item->harga * $item->kuantitas : 0;
        }) ?? 0;

        $folderRole = $this->getFolderRole();

        $pdf = Pdf::loadView("{$folderRole}.pengajuan.pdf", compact('pengajuan', 'total_harga'))
            ->setPaper('a4', 'portrait'); // load view dan set ukuran kertas

        return $pdf->stream('Pengajuan_' . $pengajuan->no_pengajuan . '.pdf'); // menampilkan pdf di browser
    }
}
