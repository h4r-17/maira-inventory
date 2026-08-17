<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Penerimaan;
use App\Models\Pengajuan;
use App\Models\Penolakan;
use App\Models\Produksi;
use App\Models\Retur;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        if ($startDate && $endDate) {
            $pengajuan  = $this->queryPengajuan($startDate, $endDate);
            $pembelian  = $this->queryPembelian($startDate, $endDate);
            $penerimaan = $this->queryPenerimaan($startDate, $endDate);
            $produksi   = $this->queryProduksi($startDate, $endDate);
            $penolakan  = $this->queryPenolakan($startDate, $endDate);
            $retur      = $this->queryRetur($startDate, $endDate);
        } else {
            $pengajuan  = collect();
            $pembelian  = collect();
            $penerimaan = collect();
            $produksi   = collect();
            $penolakan  = collect();
            $retur      = collect();
        }

        return view('direktur.laporan.laporan', compact(
            'pengajuan',
            'pembelian',
            'penerimaan',
            'produksi',
            'penolakan',
            'retur',
            'startDate',
            'endDate'
        ));
    }

    public function pdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $jenis     = $request->input('jenis', 'pengajuan');

        if (!$startDate || !$endDate) {
            return redirect()->back()->with('error', 'Silakan pilih rentang tanggal terlebih dahulu.');
        }

        $data = compact('startDate', 'endDate', 'jenis');

        switch ($jenis) {
            case 'pengajuan':
                $data['pengajuan'] = $this->queryPengajuan($startDate, $endDate);
                break;
            case 'pembelian':
                $data['pembelian'] = $this->queryPembelian($startDate, $endDate);
                break;
            case 'penerimaan':
                $data['penerimaan'] = $this->queryPenerimaan($startDate, $endDate);
                break;
            case 'produksi':
                $data['produksi'] = $this->queryProduksi($startDate, $endDate);
                break;
            case 'penolakan':
                $data['penolakan'] = $this->queryPenolakan($startDate, $endDate);
                break;
            case 'retur':
                $data['retur'] = $this->queryRetur($startDate, $endDate);
                break;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('direktur.laporan.pdf', $data)->setPaper('a4', 'landscape');

        $title = ucfirst($jenis);
        return $pdf->stream('Laporan_' . $title . '_' . $startDate . '_' . $endDate . '.pdf');
    }

    private function queryPengajuan($startDate, $endDate)
    {
        $query = Pengajuan::query();
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);
        }
        return $query->orderBy('tanggal_pengajuan', 'desc')->get();
    }

    private function queryPembelian($startDate, $endDate)
    {
        $query = Pembelian::with('supplier');
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_pembelian', [$startDate, $endDate]);
        }
        return $query->orderBy('tanggal_pembelian', 'desc')->get();
    }

    private function queryPenerimaan($startDate, $endDate)
    {
        $query = Penerimaan::with('pembelian.supplier');
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_masuk', [$startDate, $endDate]);
        }
        return $query->orderBy('tanggal_masuk', 'desc')->get();
    }

    private function queryProduksi($startDate, $endDate)
    {
        $query = Produksi::with('barangJadi');
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_produksi', [$startDate, $endDate]);
        }
        return $query->orderBy('tanggal_produksi', 'desc')->get();
    }

    private function queryPenolakan($startDate, $endDate)
    {
        $query = Penolakan::with('produksi');
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_penolakan', [$startDate, $endDate]);
        }
        return $query->orderBy('tanggal_penolakan', 'desc')->get();
    }

    private function queryRetur($startDate, $endDate)
    {
        $query = Retur::with('pembelian.supplier');
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_retur', [$startDate, $endDate]);
        }
        return $query->orderBy('tanggal_retur', 'desc')->get();
    }
}
