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
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">No Retur</div>
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
                    <div class="font-weight-bold text-gray-900">{{ $retur->pembelian->supplier->nama_supplier ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">Daftar bahan baku yang Diretur</h6>
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
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Jumlah Retur</th>
                                <th class="text-center">Tanggal Kadaluarsa</th>
                                <th class="text-center">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($retur->detailRetur as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->batch->barang->nama_barang ?? '-' }}</td>
                                    <td class="text-center">{{ $item->batch->kode_lot_supplier ?? '-' }}</td>
                                    <td class="text-center">{{ $item->satuan?->kode_satuan ?? '-' }}</td>
                                    <td class="text-center">{{ $item->jumlah_retur ?? '-' }}</td>
                                    <td class="text-center">
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
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#accept{{ $retur->id_retur }}">
        <i class="fa-solid fa-check"></i> Setujui
    </button>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#reject{{ $retur->id_retur }}">
        <i class="fa-solid fa-times"></i> Tolak
    </button>

    <!-- Modal Accept -->
    <div class="modal fade" id="accept{{ $retur->id_retur }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('retur.accept', $retur->id_retur) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi Disetujui</strong>
                    </h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah anda yakin ingin menyelesaikan retur ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-success" type="submit">Setuju</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Reject -->
    <div class="modal fade" id="reject{{ $retur->id_retur }}" tabindex="-1" aria-labelledby="exampleModal"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('retur.reject', $retur->id_retur) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModal"><strong>Konfirmasi Ditolak</strong>
                    </h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah anda yakin ingin menolak retur ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-danger" type="submit">Ya</button>
                </div>
            </form>
        </div>
    </div>
@endsection
