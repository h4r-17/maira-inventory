@extends('layouts.master')

@section('title', '| Alasan Penolakan')
@section('konten')
@section('judul', 'Tabel Alasan Penolakan')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-end align-items-center">
        <a href="{{ route('alasan-penolakan.create') }}" class="btn btn-icon-split btn-primary">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tambah Alasan</span>
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-gray-900" id="tabelData">
                <thead class="bg-light">
                    <tr>
                        <th>No</th>
                        <th>Kode Alasan</th>
                        <th>Nama Alasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_alasan }}</td>
                            <td>{{ $item->nama_alasan }}</td>
                            <td>
                                <a href="{{ route('alasan-penolakan.edit', $item->id_alasan) }}"
                                    class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                    data-target="#hapus{{ $item->id_alasan }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal -->
@foreach ($data as $item)
    <div class="modal fade" id="hapus{{ $item->id_alasan }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('alasan-penolakan.destroy', $item->id_alasan) }}" method="POST"
                class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi Hapus</strong></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah anda yakin ingin menghapus alasan ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection
