@extends('layouts.master')

@section('title', '| Retur Bahan Baku')
@section('konten')
@section('judul', 'Tabel Retur Bahan Baku')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-end align-items-center">
        <a href="{{ route('retur.create') }}" class="btn btn-icon-split btn-primary">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tambah Retur</span>
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-gray-900" id="tabelData">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>No Retur</th>
                        <th>Tanggal Retur</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th>Aksi</th>
                        {{-- <th>Kontrol</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($retur as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->no_retur }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_retur)->format('d-m-Y') }}</td>
                            <td>{{ $item->pembelian->supplier->nama_supplier }}</td>
                            <td>
                                <span
                                    class="badge badge
                                      @if ($item->status == 'Diretur') badge-success
                                      @elseif($item->status == 'Ditolak') badge-danger
                                      @elseif($item->status == 'Pending') badge-warning
                                      @else badge-secondary @endif">{{ $item->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('retur.show', $item->id_retur) }}" class="btn btn-sm btn-info"><i
                                        class="fa-solid fa-eye"></i></a>
                                @if ($item->status != 'Diretur' && $item->status != 'Ditolak')
                                    <a href="{{ route('retur.edit', $item->id_retur) }}"
                                        class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                @endif
                                <a href="{{ route('retur.pdf', $item->id_retur) }}" target="_blank"
                                    class="btn btn-sm btn-info"><i class="fa-solid fa-print"></i>
                                </a>
                                <!-- Button trigger modal -->
                                @if ($item->status != 'Diretur' && $item->status != 'Ditolak')
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                        data-target="#hapus{{ $item->id_retur }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                            {{-- <td>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                    data-target="#accept{{ $item->id_retur }}">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                    data-target="#reject{{ $item->id_retur }}">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal Hapus -->
@foreach ($retur as $item)
    <div class="modal fade" id="hapus{{ $item->id_retur }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('retur.destroy', $item->id_retur) }}" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi Hapus</strong></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah Anda yakin ingin menghapus retur ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Disetujui -->
    <div class="modal fade" id="accept{{ $item->id_retur }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('retur.accept', $item->id_retur) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi Diretur</strong>
                    </h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah anda yakin ingin menyelesaikan retur ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-success" type="submit">Ya</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Ditolak -->
    <div class="modal fade" id="reject{{ $item->id_retur }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('retur.reject', $item->id_retur) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi Ditolak</strong>
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
@endforeach
@endsection
