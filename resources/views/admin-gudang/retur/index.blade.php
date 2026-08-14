@extends('layouts.master')

@section('title', '| Retur')
@section('konten')
@section('judul', 'Tabel Retur Bahan Baku')

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-gray-900" id="tabelData"
                style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>No Retur</th>
                        <th>Tanggal Retur</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th>Aksi</th>
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
                                </a>
                                <a href="{{ route('retur.pdf', $item->id_retur) }}" target="_blank"
                                    class="btn btn-sm btn-info"><i class="fa-solid fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
