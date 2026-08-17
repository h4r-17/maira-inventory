<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $barangs = Barang::select('id_barang', 'nama_barang', 'kode_barang')->get();

        $id_barang = $request->id_barang;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $mutasi = [];
        $saldoAwal = 0;

        if ($id_barang && $start_date && $end_date) {
            $data = $this->getMutasiData($id_barang, $start_date, $end_date);
            $mutasi = $data['mutasi'];
            $saldoAwal = $data['saldo_awal'];
        }

        return view('mutasi.index', compact('barangs', 'mutasi', 'saldoAwal', 'id_barang', 'start_date', 'end_date'));
    }

    public function pdf(Request $request)
    {
        $id_barang = $request->id_barang;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if (!$id_barang || !$start_date || !$end_date) {
            return redirect()->back()->with('error', 'Pilih bahan baku dan periode tanggal.');
        }

        $barang = Barang::findOrFail($id_barang);
        $data = $this->getMutasiData($id_barang, $start_date, $end_date);

        $mutasi = $data['mutasi'];
        $saldoAwal = $data['saldo_awal'];

        $pdf = Pdf::loadView('mutasi.pdf', compact('barang', 'mutasi', 'saldoAwal', 'start_date', 'end_date'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Kartu_Stok_' . $barang->nama_barang . '_' . $start_date . '_' . $end_date . '.pdf');
    }

    private function getMutasiData($id_barang, $start_date, $end_date)
    {
        $startDate = Carbon::parse($start_date)->startOfDay();
        $endDate = Carbon::parse($end_date)->endOfDay();

        // --- Calculate Saldo Awal (Opening Balance) ---
        // Masuk before start date
        $masukAwal = DB::table('detail_penerimaan')
            ->join('penerimaan', 'detail_penerimaan.id_penerimaan', '=', 'penerimaan.id_penerimaan')
            ->join('batch_barang', 'detail_penerimaan.id_batch', '=', 'batch_barang.id_batch')
            ->where('batch_barang.id_barang', $id_barang)
            ->where('penerimaan.tanggal_masuk', '<', $startDate)
            ->sum(DB::raw('detail_penerimaan.jumlah_masuk * COALESCE(detail_penerimaan.rasio_konversi, 1)'));

        // Keluar before start date (Produksi)
        $keluarProduksiAwal = DB::table('detail_produksi')
            ->join('produksi', 'detail_produksi.id_produksi', '=', 'produksi.id_produksi')
            ->join('batch_barang', 'detail_produksi.id_batch', '=', 'batch_barang.id_batch')
            ->where('batch_barang.id_barang', $id_barang)
            ->where('produksi.tanggal_produksi', '<', $startDate)
            ->sum('detail_produksi.jumlah_keluar');

        // Keluar before start date (Penolakan)
        $keluarPenolakanAwal = DB::table('detail_penolakan')
            ->join('penolakan', 'detail_penolakan.id_penolakan', '=', 'penolakan.id_penolakan')
            ->join('batch_barang', 'detail_penolakan.id_batch', '=', 'batch_barang.id_batch')
            ->where('batch_barang.id_barang', $id_barang)
            ->where('penolakan.tanggal_penolakan', '<', $startDate)
            ->sum('detail_penolakan.jumlah_ditolak');

        $saldoAwal = $masukAwal - ($keluarProduksiAwal + $keluarPenolakanAwal);

        // --- Get Transactions in Date Range ---
        // Penerimaan
        $penerimaan = DB::table('detail_penerimaan')
            ->select(
                'penerimaan.tanggal_masuk as tanggal',
                'penerimaan.no_registrasi as no_dokumen',
                DB::raw("'Penerimaan' as tipe"),
                'detail_penerimaan.deskripsi as keterangan',
                'batch_barang.kode_lot_supplier as kode_lot_supplier',
                'batch_barang.kode_batch as kode_batch',
                'batch_barang.expired_date as expired_date',
                DB::raw('(detail_penerimaan.jumlah_masuk * COALESCE(detail_penerimaan.rasio_konversi, 1)) as masuk'),
                DB::raw("0 as keluar")
            )
            ->join('penerimaan', 'detail_penerimaan.id_penerimaan', '=', 'penerimaan.id_penerimaan')
            ->join('batch_barang', 'detail_penerimaan.id_batch', '=', 'batch_barang.id_batch')
            ->where('batch_barang.id_barang', $id_barang)
            ->whereBetween('penerimaan.tanggal_masuk', [$startDate, $endDate]);

        // Produksi
        $produksi = DB::table('detail_produksi')
            ->select(
                'produksi.tanggal_produksi as tanggal',
                'produksi.batch_produk as no_dokumen',
                DB::raw("'Produksi' as tipe"),
                'detail_produksi.deskripsi as keterangan',
                'batch_barang.kode_lot_supplier as kode_lot_supplier',
                'batch_barang.kode_batch as kode_batch',
                'batch_barang.expired_date as expired_date',
                DB::raw("0 as masuk"),
                'detail_produksi.jumlah_keluar as keluar'
            )
            ->join('produksi', 'detail_produksi.id_produksi', '=', 'produksi.id_produksi')
            ->join('batch_barang', 'detail_produksi.id_batch', '=', 'batch_barang.id_batch')
            ->where('batch_barang.id_barang', $id_barang)
            ->whereBetween('produksi.tanggal_produksi', [$startDate, $endDate]);

        // Retur
        $retur = DB::table('detail_retur')
            ->select(
                'retur.tanggal_retur as tanggal',
                'retur.no_retur as no_dokumen',
                DB::raw("'Retur' as tipe"),
                'detail_retur.deskripsi as keterangan',
                'batch_barang.kode_lot_supplier as kode_lot_supplier',
                'batch_barang.kode_batch as kode_batch',
                'batch_barang.expired_date as expired_date',
                DB::raw("0 as masuk"),
                DB::raw('NULL as keluar')
            )
            ->join('retur', 'detail_retur.id_retur', '=', 'retur.id_retur')
            ->join('batch_barang', 'detail_retur.id_batch', '=', 'batch_barang.id_batch')
            ->where('batch_barang.id_barang', $id_barang)
            ->whereBetween('retur.tanggal_retur', [$startDate, $endDate]);

        // Penolakan
        $penolakan = DB::table('detail_penolakan')
            ->select(
                'penolakan.tanggal_penolakan as tanggal',
                'penolakan.no_penolakan as no_dokumen',
                DB::raw("'Penolakan' as tipe"),
                'detail_penolakan.deskripsi as keterangan',
                'batch_barang.kode_lot_supplier as kode_lot_supplier',
                'batch_barang.kode_batch as kode_batch',
                'batch_barang.expired_date as expired_date',
                DB::raw("0 as masuk"),
                'detail_penolakan.jumlah_ditolak as keluar'
            )
            ->join('penolakan', 'detail_penolakan.id_penolakan', '=', 'penolakan.id_penolakan')
            ->join('batch_barang', 'detail_penolakan.id_batch', '=', 'batch_barang.id_batch')
            ->where('batch_barang.id_barang', $id_barang)
            ->whereBetween('penolakan.tanggal_penolakan', [$startDate, $endDate]);

        // Combine all using union
        $transactions = $penerimaan
            ->unionAll($produksi)
            ->unionAll($penolakan)
            ->unionAll($retur)
            ->orderBy('tanggal', 'asc')
            ->get();

        $mutasi = collect($transactions);

        return [
            'saldo_awal' => $saldoAwal,
            'mutasi' => $mutasi
        ];
    }
}
