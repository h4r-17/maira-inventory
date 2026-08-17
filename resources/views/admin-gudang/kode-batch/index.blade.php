@extends('layouts.master')

@section('title', '| Kode Batch')
@section('konten')
@section('judul', 'Tabel Kode Batch Bahan Baku')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Tabel Batch Bahan Baku</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('kode-batch.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label class="font-weight-bold">Bahan Baku</label>
                    <select name="bahan_baku" class="form-control">
                        <option value="">-- Pilih Bahan Baku --</option>
                        @foreach ($barangs as $brg)
                            <option value="{{ $brg->id_barang }}"
                                {{ request('bahan_baku') == $brg->id_barang ? 'selected' : '' }}>
                                {{ $brg->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="font-weight-bold">Urutkan Tanggal Expired</label>
                    <select name="sort_tanggal" class="form-control">
                        <option value="">-- Pilih Urutan --</option>
                        <option value="terbaru" {{ request('sort_tanggal') == 'terbaru' ? 'selected' : '' }}>Terdekat
                        </option>
                        <option value="terlama" {{ request('sort_tanggal') == 'terlama' ? 'selected' : '' }}>Terjauh
                        </option>
                    </select>
                </div>
                <div class="col-md-4 mb-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('kode-batch.index') }}" class="btn btn-secondary"><i class="fas fa-sync"></i>
                        Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered text-gray-900 " width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Kode Internal</th>
                        <th>Lot Supplier</th>
                        <th>Nama Bahan Baku</th>
                        <th>Tgl Expired</th>
                        <th class="text-right">Sisa Persediaan (Gram)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($batchBarang as $batch)
                        <tr>
                            <td>{{ $batchBarang->firstItem() + $loop->index }}</td>
                            <td>
                                <span class="badge badge-primary">{{ $batch->kode_batch }}</span>
                            </td>
                            <td>
                                <!-- Jika supplier tidak kasih lot, tampilkan strip (-) -->
                                {{ $batch->kode_lot_supplier ?? '-' }}
                            </td>
                            <td>
                                {{ $batch->barang->nama_barang ?? 'Barang Dihapus' }}
                            </td>
                            <td>
                                @if ($batch->expired_date)
                                    <!-- Menampilkan tanggal dengan format d-m-Y -->
                                    {{ \Carbon\Carbon::parse($batch->expired_date)->format('d-m-Y') }}
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>
                            <td
                                class="text-right font-weight-bold 
                                {{ $batch->sisa_persediaan <= 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($batch->sisa_persediaan, 0, ',', '.') }} g
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $batchBarang->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
