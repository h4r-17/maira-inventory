@extends('layouts.master')

@section('title', '| Produksi')
@section('konten')
@section('judul', 'Tabel Produksi')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-end align-items-center">
        <a href="{{ route('produksi.create') }}" class="btn btn-icon-split btn-primary">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tambah Produksi</span>
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-gray-900" id="tabelData"
                style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Batch Produk</th>
                        <th>Tanggal Produksi</th>
                        <th>Nama Produk</th>
                        <th>Hasil Produksi</th>
                        <th>Hasil Lolos QC</th>
                        <th>Produk Expired</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produksi as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->batch_produk }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_produksi)->format('d-m-Y') }}
                            </td>
                            <td>{{ $item->barangJadi->nama_produk }}</td>
                            <td>{{ $item->hasil_produksi }} PCS</td>
                            <td>{{ $item->hasil_qc }} PCS</td>
                            <td>{{ \Carbon\Carbon::parse($item->produk_expired)->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('produksi.show', $item->id_produksi) }}"
                                    class="btn btn-sm btn-info"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('produksi.edit', $item->id_produksi) }}"
                                    class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="{{ route('produksi.pdf', $item->id_produksi) }}" target="_blank"
                                    class="btn btn-sm btn-info"><i class="fa-solid fa-print"></i>
                                </a>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                    data-target="#hapus{{ $item->id_produksi }}">
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
@foreach ($produksi as $item)
    <div class="modal fade" id="hapus{{ $item->id_produksi }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('produksi.destroy', $item->id_produksi) }}" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi Hapus</strong></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah Anda yakin ingin menghapus catatan produksi ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection
