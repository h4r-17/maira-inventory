@extends('layouts.master')

@section('title', '| Detail Retur')
@section('konten')
    @php
        $statusMap = [
            'Pending' => ['class' => 'warning', 'label' => 'Pending'],
            'Diretur' => ['class' => 'success', 'label' => 'Diretur'],
            'Ditolak' => ['class' => 'danger', 'label' => 'Ditolak'],
        ];
        $statusInfo = $statusMap[$retur->status] ?? ['class' => 'secondary', 'label' => $retur->status];
        $totalItem = $retur->detailRetur->count();
    @endphp

    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-3 text-gray-900">Detail Retur: {{ $retur->no_retur }}</h3>
            <a href="{{ route('retur.pdf', $retur->id_retur) }}" target="_blank" class="btn btn-info"><i
                    class="fa-solid fa-print"></i> Cetak PDF
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Kode Retur</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $retur->no_retur }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Tanggal Retur</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($retur->tanggal_retur)->format('d-m-Y') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-{{ $statusInfo['class'] }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Status</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <span class="badge badge-{{ $statusInfo['class'] }} px-3 py-2">{{ $statusInfo['label'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan Retur</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="text-dark small">Kode Penolakan</div>
                    <div class="font-weight-bold text-gray-900">{{ $retur->penolakan->no_penolakan ?? '-' }}</div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="text-dark small">No Nota</div>
                    <div class="font-weight-bold text-gray-900">{{ $retur->pembelian->no_nota }}</div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="text-dark small">Supplier</div>
                    <div class="font-weight-bold text-gray-900">{{ $retur->pembelian->supplier->nama_supplier }}</div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Bahan Baku yang Diretur</h6>
                </div>
                <span class="text-gray-900">Total: {{ $totalItem }} bahan baku</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 text-gray-900">
                        <thead class="table-secondary">
                            <tr>
                                <th>No</th>
                                <th>Nama Bahan Baku</th>
                                <th>Batch Bahan Baku</th>
                                <th>Satuan</th>
                                <th>Jumlah Retur</th>
                                <th>Tanggal Kadaluarsa</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($retur->detailRetur as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->batch->barang->nama_barang ?? '-' }}</td>
                                    <td>{{ $item->batch->kode_lot_supplier ?? '-' }}</td>
                                    <td>{{ $item->satuan?->kode_satuan }}</td> {{-- ? = null safe operator untuk menghindari
                                    error jika barang atau satuan tidak ada (diisi null/blank) --}}
                                    <td>{{ $item->jumlah_retur }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->batch->expired_date)->format('d-m-Y') ?? '-' }}
                                    </td>
                                    <td>{{ $item->deskripsi ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Tidak ada data bahan baku pada
                                        retur ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('retur.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
@endsection
