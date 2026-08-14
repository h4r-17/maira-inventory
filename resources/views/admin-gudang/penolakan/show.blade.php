@extends('layouts.master')

@section('title', '| Detail Penolakan')
@section('konten')
    @php
        $totalItem = $penolakan->detailPenolakan->count();
    @endphp

    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-3 text-gray-900">Detail Penolakan: {{ $penolakan->no_penolakan }}</h3>
            <a href="{{ route('penolakan.pdf', $penolakan->id_penolakan) }}" target="_blank" class="btn btn-info"><i
                    class="fa-solid fa-print"></i> Cetak PDF
            </a>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">No Penolakan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $penolakan->no_penolakan }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Tanggal Penolakan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($penolakan->tanggal_penolakan)->format('d-m-Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan Penolakan</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3 mb-md-2">
                    <div class="text-dark small">Batch Produk</div>
                    <div class="font-weight-bold text-gray-900">{{ $penolakan->produksi->batch_produk }}</div>
                </div>
                <div class="col-md-3 mb-3 mb-md-2">
                    <div class="text-dark small">Tanggal Produksi</div>
                    <div class="font-weight-bold text-gray-900">
                        {{ \Carbon\Carbon::parse($penolakan->produksi->tanggal_produksi)->format('d-m-Y') }}</div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="text-dark small">Keputusan</div>
                    <div class="font-weight-bold text-gray-900">{{ $penolakan->status }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Bahan Baku yang Ditolak</h6>
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
                                <th class="text-center">Kode Batch</th>
                                <th class="text-center">Jumlah Ditolak</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Alasan Penolakan</th>
                                <th class="text-center">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penolakan->detailPenolakan as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->batch->barang->nama_barang }}</td>
                                    <td class="text-center">
                                        {{ !blank($item->batch->kode_lot_supplier) ? $item->batch->kode_lot_supplier : (!blank($item->batch->kode_batch) ? $item->batch->kode_batch : '-') }}
                                    </td>
                                    <td class="text-center">{{ $item->jumlah_ditolak }}</td>
                                    <td class="text-center">{{ $item->batch->barang->satuan->kode_satuan }}</td>
                                    <td class="text-center">{{ $item->alasan_penolakan }}</td>
                                    <td>{{ $item->deskripsi ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Tidak ada data bahan baku pada
                                        penolakan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('penolakan.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
@endsection
