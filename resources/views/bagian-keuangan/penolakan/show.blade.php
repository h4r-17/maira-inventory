@extends('layouts.master')

@section('title', '| Detail Penolakan')
@section('konten')
    @php
        $totalItem = $penolakan->detailPenolakan->count();
    @endphp

    <div class="mb-4">
        <div>
            <h3 class="mb-3 text-gray-900">Detail Penolakan: {{ $penolakan->kode_penolakan }}</h3>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Kode Penolakan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $penolakan->kode_penolakan }}</div>
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
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Bahan baku yang Ditolak</h6>
                </div>
                <span class="text-gray-900">Total: {{ $totalItem }} bahan baku</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 text-gray-900">
                        <thead class="table-secondary">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Bahan baku</th>
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
                                    <td>{{ $item->barang->nama_barang }}</td>
                                    <td class="text-center">{{ $item->batch_barang }}</td>
                                    <td class="text-center">{{ $item->jumlah_ditolak }}</td>
                                    <td class="text-center">{{ $item->satuan->kode_satuan }}</td>
                                    <td class="text-center">{{ $item->alasan_penolakan }}</td>
                                    <td class="text-center">{{ $item->deskripsi ?: '-' }}</td>
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
