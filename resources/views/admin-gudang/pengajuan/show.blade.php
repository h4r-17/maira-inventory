@extends('layouts.master')

@section('title', '| Detail Pengajuan')
@section('konten')
    @php
        $statusMap = [
            'Pending' => ['class' => 'warning', 'label' => 'Pending'],
            'Disetujui' => ['class' => 'success', 'label' => 'Disetujui'],
            'Ditolak' => ['class' => 'danger', 'label' => 'Ditolak'],
        ];
        $statusInfo = $statusMap[$pengajuan->status] ?? ['class' => 'secondary', 'label' => $pengajuan->status];
        $totalItem = $pengajuan->detailPengajuan->count();
    @endphp

    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-3 text-gray-900">Detail Pengajuan: {{ $pengajuan->no_pengajuan }}</h3>
            <a href="{{ route('pengajuan.pdf', $pengajuan->id_pengajuan) }}" target="_blank" class="btn btn-info"><i
                    class="fa-solid fa-print"></i> Cetak PDF
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">No Pengajuan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pengajuan->no_pengajuan }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Tanggal Pengajuan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d-m-Y') }}</div>
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
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan Pengajuan</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="text-dark small">Status Saat Ini</div>
                    <div class="font-weight-bold text-gray-900">{{ $statusInfo['label'] }}</div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">Daftar bahan baku yang diajukan</h6>
                </div>
                <span class="text-gray-900">Total: {{ $totalItem }} bahan baku</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 text-gray-900"
                        style="table-layout: fixed;">
                        <thead class="table-secondary">
                            <tr>
                                <th class="text-center col-1">No</th>
                                <th class="text-center col-3">Nama Bahan Baku</th>
                                <th class="text-center col-1">Kuantitas</th>
                                <th class="text-center col-1">Satuan</th>
                                <th class="text-center col-2">Harga</th>
                                <th class="text-center col-2">Deskripsi</th>
                                <th class="text-center col-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuan->detailPengajuan as $item)
                                @php
                                    $subtotal = $item->harga !== null ? $item->harga * $item->kuantitas : null;
                                    $total_harga =
                                        $pengajuan->detailPengajuan?->sum(function ($item) {
                                            return $item->harga !== null ? $item->harga * $item->kuantitas : 0;
                                        }) ?? 0; //jika tidak ada detail pengajuan, total_harga akan menjadi 0
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->barang?->nama_barang }}</td>
                                    <td class="text-center">{{ $item->kuantitas }}</td>
                                    <td class="text-center">{{ $item->satuan?->kode_satuan }}</td> {{-- ? = null safe operator untuk menghindari
                                    error jika barang atau satuan tidak ada (diisi null/blank) --}}
                                    <td>
                                        {{ $item->harga !== null ? 'Rp ' . number_format($item->harga, 0, ',', '.') : '-' }}
                                    </td>
                                    <td>{{ $item->deskripsi ?: '-' }}</td>
                                    <td class="text-center">
                                        {{ $subtotal !== null ? 'Rp ' . number_format($subtotal, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Tidak ada data bahan baku pada
                                        pengajuan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-right" id="label_total_harga">Total Harga</th>
                                <th colspan="1" class="" id="total_harga">
                                    {{ 'Rp ' . number_format($total_harga, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary mb-5">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
@endsection
