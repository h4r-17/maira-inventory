@extends('layouts.master')

@section('title', '| Pengajuan')
@section('konten')
@section('judul', 'Tabel Pengajuan Bahan Baku')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-end align-items-center">
        <a href="{{ route('pengajuan.create') }}" class="btn btn-icon-split btn-primary">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tambah Pengajuan</span>
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-gray-900" id="tabelData">
                <thead class="bg-gray-100">
                    <tr>
                        <th>No</th>
                        <th>No Pengajuan</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                        <th>Kontrol</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->no_pengajuan }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                            <td>
                                <span
                                    class="badge badge
                                      @if ($item->status == 'Disetujui') badge-success
                                      @elseif($item->status == 'Ditolak') badge-danger
                                      @elseif($item->status == 'Pending') badge-warning
                                      @else badge-secondary @endif">{{ $item->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('pengajuan.show', $item->id_pengajuan) }}"
                                    class="btn btn-sm btn-info"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('pengajuan.edit', $item->id_pengajuan) }}"
                                    class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="{{ route('pengajuan.pdf', $item->id_pengajuan) }}" target="_blank"
                                    class="btn btn-sm btn-info"><i class="fa-solid fa-print"></i>
                                </a>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                    data-target="#hapus{{ $item->id_pengajuan }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                            <td>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                    data-target="#accept{{ $item->id_pengajuan }}">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#pending{{ $item->id_pengajuan }}">
                                    <i class="fa-solid fa-exclamation"></i>
                                </button>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                    data-target="#reject{{ $item->id_pengajuan }}">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal Hapus -->
@foreach ($pengajuan as $item)
    <div class="modal fade" id="hapus{{ $item->id_pengajuan }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('pengajuan.destroy', $item->id_pengajuan) }}" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi Hapus</strong></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah anda yakin ingin menghapus pengajuan ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
<!-- Modal Accept -->
@foreach ($pengajuan as $item)
    <div class="modal fade" id="accept{{ $item->id_pengajuan }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('pengajuan.accept', $item->id_pengajuan) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi Disetujui</strong>
                    </h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah anda yakin ingin menyetujui pengajuan ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-success" type="submit">Setuju</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
<!-- Modal Reject -->
@foreach ($pengajuan as $item)
    <div class="modal fade" id="reject{{ $item->id_pengajuan }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('pengajuan.reject', $item->id_pengajuan) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi Ditolak</strong>
                    </h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah anda yakin ingin menolak pengajuan ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-danger" type="submit">Tolak</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
<!-- Modal Pending -->
@foreach ($pengajuan as $item)
    <div class="modal fade" id="pending{{ $item->id_pengajuan }}" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('pengajuan.pending', $item->id_pengajuan) }}" method="POST"
                class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi
                            Pending</strong>
                    </h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah anda yakin ingin mengubah status pengajuan ini menjadi
                    pending?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-warning" type="submit">Ya</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection
