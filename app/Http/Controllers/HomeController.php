<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangJadi;
use App\Models\Supplier;
use App\Models\Pengajuan;
use App\Models\Pembelian;
use App\Models\Produksi;
use App\Models\Penerimaan;
use App\Models\Retur;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $totalBarang = Barang::count();
        $totalBarangJadi = BarangJadi::count();
        $totalSupplier = Supplier::count();
        $totalPengajuan = Pengajuan::count();
        $totalPembelian = Pembelian::count();
        $totalProduksi = Produksi::count();

        // Data untuk tabel
        $returTerbaru = Retur::with('pembelian.supplier')->latest('tanggal_retur')->take(5)->get();
        $penerimaanTerbaru = Penerimaan::with('pembelian.supplier')->latest('tanggal_masuk')->take(5)->get();
        $produksiTerbaru = Produksi::with('barangJadi')->latest('tanggal_produksi')->take(5)->get();
        $pembelianTerbaru = Pembelian::with('supplier')->latest('tanggal_pembelian')->take(5)->get();

        // Data aktivitas terbaru (Gabungan dari beberapa transaksi terakhir)
        $aktivitasTerbaru = collect();
        
        foreach($penerimaanTerbaru as $p) {
            $aktivitasTerbaru->push([
                'tanggal' => $p->tanggal_masuk,
                'jenis' => 'Penerimaan',
                'keterangan' => 'Penerimaan No: ' . $p->no_registrasi,
                'status' => 'Selesai'
            ]);
        }
        
        foreach($produksiTerbaru as $p) {
            $aktivitasTerbaru->push([
                'tanggal' => $p->tanggal_produksi,
                'jenis' => 'Produksi',
                'keterangan' => 'Produksi Batch: ' . $p->batch_produk,
                'status' => 'Selesai'
            ]);
        }
        
        foreach($returTerbaru as $r) {
            $aktivitasTerbaru->push([
                'tanggal' => $r->tanggal_retur,
                'jenis' => 'Retur',
                'keterangan' => 'Retur No: ' . $r->no_retur,
                'status' => $r->status
            ]);
        }

        $aktivitasTerbaru = $aktivitasTerbaru->sortByDesc('tanggal')->take(5);

        // Data untuk grafik 6 bulan kedepan
        $labelsBulan = [];
        $dataProduksi = []; // Untuk bar chart (sum)
        $dataPembelian = [];
        $dataPenerimaan = [];
        $dataPengajuan = [];
        $dataProduksiCount = []; // Untuk line chart (count)
        $dataPenolakan = [];
        $dataRetur = [];

        for ($i = 0; $i <= 5; $i++) {
            $month = Carbon::now()->addMonths($i);
            $labelsBulan[] = $month->translatedFormat('M Y');
            
            // Produksi (Sum)
            $prod = Produksi::whereYear('tanggal_produksi', $month->year)
                            ->whereMonth('tanggal_produksi', $month->month)
                            ->sum('hasil_produksi');
            $dataProduksi[] = (int) $prod;

            // Produksi (Count)
            $prodCount = Produksi::whereYear('tanggal_produksi', $month->year)
                                 ->whereMonth('tanggal_produksi', $month->month)
                                 ->count();
            $dataProduksiCount[] = $prodCount;

            // Pembelian
            $beli = Pembelian::whereYear('tanggal_pembelian', $month->year)
                             ->whereMonth('tanggal_pembelian', $month->month)
                             ->count();
            $dataPembelian[] = $beli;

            // Penerimaan
            $terima = Penerimaan::whereYear('tanggal_masuk', $month->year)
                                ->whereMonth('tanggal_masuk', $month->month)
                                ->count();
            $dataPenerimaan[] = $terima;

            // Pengajuan
            $aju = Pengajuan::whereYear('tanggal_pengajuan', $month->year)
                            ->whereMonth('tanggal_pengajuan', $month->month)
                            ->count();
            $dataPengajuan[] = $aju;

            // Penolakan
            $tolak = \App\Models\Penolakan::whereYear('tanggal_penolakan', $month->year)
                                          ->whereMonth('tanggal_penolakan', $month->month)
                                          ->count();
            $dataPenolakan[] = $tolak;

            // Retur
            $retur = Retur::whereYear('tanggal_retur', $month->year)
                          ->whereMonth('tanggal_retur', $month->month)
                          ->count();
            $dataRetur[] = $retur;
        }

        return view('home', compact(
            'totalBarang',
            'totalBarangJadi',
            'totalSupplier',
            'totalPengajuan',
            'totalPembelian',
            'totalProduksi',
            'returTerbaru',
            'penerimaanTerbaru',
            'produksiTerbaru',
            'pembelianTerbaru',
            'aktivitasTerbaru',
            'labelsBulan',
            'dataProduksi',
            'dataPembelian',
            'dataPenerimaan',
            'dataPengajuan',
            'dataProduksiCount',
            'dataPenolakan',
            'dataRetur'
        ));
    }
}

