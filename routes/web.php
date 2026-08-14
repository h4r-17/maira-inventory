<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangJadiController;
use App\Http\Controllers\BatchBarangController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KonversiBarangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PenolakanController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\ResepProduksiController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// public
Auth::routes();
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/mutasi', [MutasiController::class, 'index'])->name('mutasi.index');
    Route::get('/mutasi/pdf', [MutasiController::class, 'pdf'])->name('mutasi.pdf');
    Route::get('/autocomplete-barang', [KonversiBarangController::class, 'autocomplete'])->name('autocomplete-barang');
    Route::get('/get-resep/{id_produk}', [ProduksiController::class, 'resepProduksi'])->name('get-resep');
});

// manajemen user
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::post('/pengajuan/{id_pengajuan}/pending', [PengajuanController::class, 'pending'])->name('pengajuan.pending');
});


Route::middleware(['auth', 'role:Admin Gudang'])->group(function () {
    Route::resource('satuan', SatuanController::class);
    Route::resource('barang', BarangController::class);
    Route::resource('barang-jadi', BarangJadiController::class);
    Route::resource('konversi-barang', KonversiBarangController::class);
    Route::resource('resep-produksi', ResepProduksiController::class);
    Route::resource('kode-batch', BatchBarangController::class);
});

Route::middleware(['auth', 'role:Bagian Keuangan'])->group(function () {
    Route::resource('supplier', SupplierController::class);
});

// modul pengajuan
Route::middleware(['auth', 'role:Admin Gudang'])->group(function () {
    Route::get('/pengajuan/create', [PengajuanController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan/{id_pengajuan}/edit', [PengajuanController::class, 'edit'])->name('pengajuan.edit');
    Route::put('/pengajuan/{id_pengajuan}', [PengajuanController::class, 'update'])->name('pengajuan.update');
    Route::delete('/pengajuan/{id_pengajuan}', [PengajuanController::class, 'destroy'])->name('pengajuan.destroy');
});

Route::middleware(['auth', 'role:Admin Gudang,Bagian Keuangan'])->group(function () {
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan/{id_pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
    Route::post('/pengajuan/{id_pengajuan}/accept', [PengajuanController::class, 'accept'])->name('pengajuan.accept');
    Route::post('/pengajuan/{id_pengajuan}/reject', [PengajuanController::class, 'reject'])->name('pengajuan.reject');
    Route::get('/pengajuan/{id_pengajuan}/pdf', [PengajuanController::class, 'cetakPdf'])->name('pengajuan.pdf');
});

// modul pembelian
Route::middleware(['auth', 'role:Bagian Keuangan'])->group(function () {
    Route::resource('pembelian', PembelianController::class);
    Route::get('/autocomplete-pengajuan', [PembelianController::class, 'autoCompletePengajuan'])->name('autocomplete-pengajuan');
    Route::get('/pembelian/{id_pembelian}/pdf', [PembelianController::class, 'cetakPdf'])->name('pembelian.pdf');
});

// modul penerimaan
Route::middleware(['auth', 'role:Admin Gudang'])->group(function () {
    Route::get('/penerimaan/create', [PenerimaanController::class, 'create'])->name('penerimaan.create');
    Route::post('/penerimaan', [PenerimaanController::class, 'store'])->name('penerimaan.store');
    Route::get('/penerimaan/{id_penerimaan}/edit', [PenerimaanController::class, 'edit'])->name('penerimaan.edit');
    Route::put('/penerimaan/{id_penerimaan}', [PenerimaanController::class, 'update'])->name('penerimaan.update');
    Route::delete('/penerimaan/{id_penerimaan}', [PenerimaanController::class, 'destroy'])->name('penerimaan.destroy');
    Route::get('/autocomplete-nota', [PenerimaanController::class, 'autocompleteNota'])->name('autocomplete-nota');
    Route::get('/penerimaan/{id_penerimaan}/pdf', [PenerimaanController::class, 'cetakPdf'])->name('penerimaan.pdf');
    Route::get('/penerimaan/get-detail-retur/{id_retur}', [PenerimaanController::class, 'getDetailRetur'])->name('get-detail-retur');
});

Route::middleware(['auth', 'role:Admin Gudang,Bagian Keuangan'])->group(function () {
    Route::get('/penerimaan', [PenerimaanController::class, 'index'])->name('penerimaan.index');
    Route::get('/penerimaan/{id_penerimaan}', [PenerimaanController::class, 'show'])->name('penerimaan.show');
});

// modul produksi
Route::middleware(['auth', 'role:Admin Gudang'])->group(function () {
    Route::resource('produksi', ProduksiController::class);

    Route::get('/penolakan/create', [PenolakanController::class, 'create'])->name('penolakan.create');
    Route::post('/penolakan', [PenolakanController::class, 'store'])->name('penolakan.store');
    Route::get('/penolakan/{id_penolakan}/edit', [PenolakanController::class, 'edit'])->name('penolakan.edit');
    Route::put('/penolakan/{id_penolakan}', [PenolakanController::class, 'update'])->name('penolakan.update');
    Route::delete('/penolakan/{id_penolakan}', [PenolakanController::class, 'destroy'])->name('penolakan.destroy');

    Route::get('/produksi/{id_produksi}/pdf', [ProduksiController::class, 'cetakPdf'])->name('produksi.pdf');
    Route::get('/penolakan/{id_penolakan}/pdf', [PenolakanController::class, 'cetakPdf'])->name('penolakan.pdf');
    Route::get('/autocomplete-produk', [ProduksiController::class, 'autocompleteProduk'])->name('autocomplete-produk');
    Route::get('/autocomplete-batch-produk', [PenolakanController::class, 'autoCompleteBatchProduk'])->name('autocomplete-batch-produk');
    Route::get('/autocomplete-batch-penolakan', [PenolakanController::class, 'autoCompleteBatchPenolakan'])->name('autocomplete-batch-penolakan');
});

Route::middleware(['auth', 'role:Admin Gudang,Bagian Keuangan'])->group(function () {
    Route::get('/penolakan', [PenolakanController::class, 'index'])->name('penolakan.index');
    Route::get('/penolakan/{id_penolakan}', [PenolakanController::class, 'show'])->name('penolakan.show');
});

//modul retur
Route::middleware(['auth', 'role:Bagian Keuangan'])->group(function () {
    Route::get('/retur/create', [ReturController::class, 'create'])->name('retur.create');
    Route::post('/retur', [ReturController::class, 'store'])->name('retur.store');
    Route::get('/retur/{id_retur}/edit', [ReturController::class, 'edit'])->name('retur.edit');
    Route::put('/retur/{id_retur}', [ReturController::class, 'update'])->name('retur.update');
    Route::delete('/retur/{id_retur}', [ReturController::class, 'destroy'])->name('retur.destroy');
    Route::get('/retur/get-penolakan-details/{id_penolakan}', [ReturController::class, 'getPenolakanDetails']); // Endpoint API (AJAX) untuk mengambil barang ditolak berdasarkan id_penolakan yang dipilih
    Route::post('/retur/{id_retur}/accept', [ReturController::class, 'accept'])->name('retur.accept');
    Route::post('/retur/{id_retur}/reject', [ReturController::class, 'reject'])->name('retur.reject');
    Route::get('/retur/autocomplete-pembelian', [ReturController::class, 'autoCompleteReturPembelian'])->name('autocomplete-pembelian');
    Route::get('/retur/get-penolakan-details/{id_penolakan}', [ReturController::class, 'getPenolakanDetails'])->name('retur.get-penolakan-details');
    Route::get('/retur/autocomplete-penerimaan', [ReturController::class, 'autocompletePenerimaan'])->name('autocomplete-penerimaan-retur');
    Route::get('/retur/get-penerimaan-details/{id_penerimaan}', [ReturController::class, 'getPenerimaanDetails'])->name('retur.get-penerimaan-details');
});

Route::middleware(['auth', 'role:Admin Gudang,Bagian Keuangan'])->group(function () {
    Route::get('/retur', [ReturController::class, 'index'])->name('retur.index');
    Route::get('/retur/{id_retur}', [ReturController::class, 'show'])->name('retur.show');
    Route::get('/retur/{id_retur}/pdf', [ReturController::class, 'cetakPdf'])->name('retur.pdf');
});

Route::middleware(['auth', 'role:Direktur'])->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');
});
