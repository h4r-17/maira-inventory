@extends('layouts.master')

@section('title', '| Resep Produksi')
@section('konten')
@section('judul', 'Tabel Resep Produksi')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-end align-items-center">
        <a href="{{ route('resep-produksi.create') }}" class="btn btn-icon-split btn-primary">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tambah Resep</span>
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-gray-900" id="tabelData">
                <thead class="bg-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($resep as $produk)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $produk->nama_produk }}</td>
                            <td class="text-center">
                                <a href="{{ route('resep-produksi.show', $produk->id_produk) }}"
                                    class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('resep-produksi.edit', $produk->id_produk) }}"
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                    data-target="#hapus{{ $produk->id_produk }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal Hapus-->
@foreach ($resep as $item)
    <div class="modal fade" id="hapus{{ $item->id_produk }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('resep-produksi.destroy', $item->id_produk) }}" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi Hapus</strong></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah anda yakin ingin menghapus resep ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection
