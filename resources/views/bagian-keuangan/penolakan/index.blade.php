@extends('layouts.master')

@section('title', '| Penolakan')
@section('konten')
@section('judul', 'Tabel Penolakan Bahan Baku')

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-gray-900" id="tabelData">
                <thead class="bg-light">
                    <tr>
                        <th>No</th>
                        <th>No Penolakan</th>
                        <th>Tanggal Penolakan</th>
                        <th>Batch Produk</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penolakan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->no_penolakan }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_penolakan)->format('d-m-Y') }}</td>
                            <td>{{ $item->produksi->batch_produk }}</td>
                            <td>{{ $item->status }}</td>
                            <td>
                                <a href="{{ route('penolakan.show', $item->id_penolakan) }}"
                                    class="btn btn-sm btn-info"><i class="fa-solid fa-eye"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
