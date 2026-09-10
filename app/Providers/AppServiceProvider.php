<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Barang;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // service untuk notifikasi stok menipis
        View::composer('layouts.navbar', function ($view) {
            if (Auth::check() && Auth::user()->hasRole(['Admin Gudang', 'Super Admin'])) {
                $lowStockItems = Barang::whereHas('konversiBarang')
                    ->with(['konversiBarang', 'batchBarang', 'satuan'])
                    ->get()
                    ->filter(function ($barang) {
                        $totalStock = $barang->batchBarang->sum('sisa_persediaan');
                        return $totalStock <= $barang->konversiBarang->nilai_konversi;
                    });

                $view->with('lowStockItems', $lowStockItems);
            }
        });
    }
}
