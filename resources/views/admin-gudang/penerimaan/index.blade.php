@extends('layouts.master')

@section('title', '| Penerimaan')
@section('konten')
@section('judul', 'Tabel Penerimaan Bahan Baku')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-end align-items-center">
        <a href="{{ route('penerimaan.create') }}" class="btn btn-icon-split btn-primary">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tambah Penerimaan</span>
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-gray-900" id="tabelData"
                style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>No Registrasi</th>
                        <th>No Nota</th>
                        <th>Tanggal Masuk</th>
                        <th>Supplier</th>
                        <th>Status Penerimaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penerimaan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->no_registrasi }}</td>
                            <td>{{ $item->pembelian->no_nota }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y') }}</td>
                            <td>{{ $item->pembelian->supplier->nama_supplier ?? 'Supplier tidak ditemukan' }}</td>
                            <td>{{ $item->jenis_penerimaan }}</td>
                            <td>
                                <a href="{{ route('penerimaan.show', $item->id_penerimaan) }}"
                                    class="btn btn-sm btn-info"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('penerimaan.edit', $item->id_penerimaan) }}"
                                    class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="{{ route('penerimaan.pdf', $item->id_penerimaan) }}" target="_blank"
                                    class="btn btn-sm btn-info"><i class="fa-solid fa-print"></i>
                                </a>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                    data-target="#hapus{{ $item->id_penerimaan }}">
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
<!-- Modal Hapus -->
@foreach ($penerimaan as $item)
    <div class="modal fade" id="hapus{{ $item->id_penerimaan }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('penerimaan.destroy', $item->id_penerimaan) }}" method="POST"
                class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi Hapus</strong></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah Anda yakin ingin menghapus catatan penerimaan ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection
