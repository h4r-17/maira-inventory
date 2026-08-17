@extends('layouts.master')

@section('title', '| Detail Pembelian')
@section('konten')
    @php
        $totalItem = $pembelian->detailPembelian->count();
    @endphp

    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-3 text-gray-900">Detail Pembelian: {{ $pembelian->no_nota }}</h3>
            <a href="{{ route('pembelian.pdf', $pembelian->id_pembelian) }}" target="_blank" class="btn btn-info"><i
                    class="fa-solid fa-print"></i> Cetak PDF
            </a>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">No Nota</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pembelian->no_nota }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Tanggal Pembelian</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d-m-Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan Pembelian</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5 mb-3 mb-md-0">
                    <div class="text-dark small">No Pengajuan</div>
                    <div class="font-weight-bold text-gray-900">{{ $pembelian->pengajuan->no_pengajuan }}</div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="text-dark small">Supplier</div>
                    <div class="font-weight-bold text-gray-900">{{ $pembelian->supplier?->nama_supplier }}</div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="text-dark small">Cara bayar</div>
                    <div class="font-weight-bold text-gray-900">{{ $pembelian->cara_bayar }}</div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">Daftar bahan baku yang dibeli</h6>
                </div>
                <span class="text-gray-900">Total: {{ $totalItem }} bahan baku</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 text-gray-900">
                        <thead class="table-secondary">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Bahan Baku</th>
                                <th class="text-center">Deskripsi</th>
                                <th class="text-center">Kuantitas</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Diskon</th>
                                <th class="text-center">Pajak</th>
                                <th class="text-center">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // hitung total keseluruhan di luar loop agar lebih hemat memori
                                $total_harga =
                                    $pembelian->detailPembelian?->sum(function ($item) {
                                        $subtotal =
                                            $item->harga !== null ? $item->harga * $item->kuantitas - $item->diskon : 0;
                                        $pajak_item = $item->pajak ?? 0;
                                        return $subtotal + $pajak_item;
                                    }) ?? 0; //jika tidak ada detail pembelian, total_harga akan menjadi 0
                            @endphp
                            @forelse($pembelian->detailPembelian as $item)
                                @php
                                    // harga x kuantitas
                                    $subtotal_kotor = $item->harga !== null ? $item->harga * $item->kuantitas : 0;

                                    // Diskon
                                    $diskon_item = $item->diskon ?? 0;

                                    // Subtotal - Diskon + Pajak
                                    $pajak_item = $item->pajak ?? 0;
                                    $subtotal_bersih = $subtotal_kotor - $diskon_item + $pajak_item;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->barang?->nama_barang }}</td>
                                    <td>{{ $item->deskripsi ?: '-' }}</td>
                                    <td class="text-center">{{ $item->kuantitas }}</td>
                                    <td class="text-center">{{ $item->satuan?->kode_satuan }}</td>
                                    <td>
                                        {{ $item->harga !== null ? 'Rp ' . number_format($item->harga, 0, ',', '.') : '-' }}
                                    </td>
                                    <td>
                                        {{ $item->diskon !== null ? 'Rp ' . number_format($item->diskon, 0, ',', '.') : '-' }}
                                    </td>
                                    <td>
                                        {{ $item->pajak !== null ? 'Rp ' . number_format($item->pajak, 0, ',', '.') : '-' }}
                                    </td>
                                    <td>
                                        {{ $subtotal_bersih !== null ? 'Rp ' . number_format($subtotal_bersih, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">Tidak ada data bahan baku pada
                                        pengajuan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-right" colspan="8" id="label_total_harga">Total Harga
                                </th>
                                <th colspan="1" id="total_harga">
                                    {{ 'Rp ' . number_format($total_harga, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('pembelian.index') }}" class="btn btn-secondary mb-5">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
@endsection
