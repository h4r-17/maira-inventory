@extends('layouts.master')

@section('title', '| Mutasi Bahan Baku')
@section('judul', 'Mutasi Bahan Baku')
@section('konten')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Bahan Baku</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('mutasi.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_barang" class="text-gray-900">Bahan Baku</label>
                            <select name="id_barang" id="id_barang" class="form-control" required>
                                <option value="">-- Pilih Bahan Baku --</option>
                                @foreach ($barangs as $barang)
                                    <option value="{{ $barang->id_barang }}"
                                        {{ isset($id_barang) && $id_barang == $barang->id_barang ? 'selected' : '' }}>
                                        {{ $barang->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date" class="text-gray-900">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ $start_date ?? '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date" class="text-gray-900">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ $end_date ?? '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Tampilkan
                            </button>
                            @php
                                // Cek apakah data mutasi siap cetak (sudah dicari dan ada datanya)
                                $isReady =
                                    isset($id_barang) &&
                                    isset($start_date) &&
                                    isset($end_date) &&
                                    isset($mutasi) &&
                                    count($mutasi) > 0;
                                $pdfUrl = $isReady
                                    ? route('mutasi.pdf', [
                                        'id_barang' => $id_barang,
                                        'start_date' => $start_date,
                                        'end_date' => $end_date,
                                    ])
                                    : '#';
                            @endphp
                            <a href="{{ $pdfUrl }}" target="_blank"
                                class="btn btn-info ml-2 {{ !$isReady ? 'disabled opacity-50' : '' }}"
                                {!! !$isReady ? 'style="pointer-events: none; cursor: not-allowed;" aria-disabled="true"' : '' !!}>
                                <i class="fa-solid fa-print"></i> Cetak PDF
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            @if ($start_date && $end_date)
                <div class="alert alert-info alert-sm py-2 mb-0 mt-2">
                    Menampilkan data dari <strong>{{ \Carbon\Carbon::parse($start_date)->format('d-m-Y') }}</strong>
                    sampai <strong>{{ \Carbon\Carbon::parse($end_date)->format('d-m-Y') }}</strong>
                </div>
            @endif
        </div>
    </div>

    @if ($id_barang && $start_date && $end_date)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hasil Mutasi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-gray-900" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>No. Dokumen</th>
                                <th>Kode Batch</th>
                                <th>Expired Date</th>
                                <th>Tipe</th>
                                <th>Keterangan</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($mutasi) > 0)
                                <tr>
                                    <td colspan="9" class="text-right font-weight-bold">Saldo Awal</td>
                                    <td class="font-weight-bold">{{ number_format($saldoAwal) }}</td>
                                </tr>
                            @endif
                            @php $saldo = $saldoAwal; @endphp
                            @foreach ($mutasi as $index => $item)
                                @php
                                    if ($item->tipe != 'Retur') {
                                        $saldo = (float) $saldo + (float) $item->masuk - (float) $item->keluar;
                                    } else {
                                        $saldo = (float) $saldo;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $item->no_dokumen }}</td>
                                    <td>{{ $item->kode_lot_supplier ?? ($item->kode_batch ?? '-') }}</td>
                                    <td>{{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('d-m-Y') : '-' }}
                                    </td>
                                    <td>
                                        @if ($item->tipe == 'Penerimaan')
                                            <span class="badge badge-success">{{ $item->tipe }}</span>
                                        @elseif($item->tipe == 'Produksi')
                                            <span class="badge badge-danger">{{ $item->tipe }}</span>
                                        @elseif($item->tipe == 'Retur')
                                            <span class="badge badge-danger">{{ $item->tipe }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $item->tipe }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                    <td class="text-success">
                                        {{ $item->masuk > 0 ? number_format($item->masuk) : '-' }}</td>
                                    <td class="text-danger">
                                        {{ $item->keluar > 0 ? number_format($item->keluar) : '-' }}</td>
                                    <td>{{ number_format($saldo) }}</td>
                                </tr>
                            @endforeach
                            @if (count($mutasi) == 0)
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada transaksi pada periode ini.</td>
                                </tr>
                            @endif
                        </tbody>
                        @if (count($mutasi) > 0)
                            <tfoot>
                                <tr>
                                    <td colspan="9" class="text-right font-weight-bold">Saldo Akhir</td>
                                    <td class="font-weight-bold">{{ number_format($saldo) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning text-center mt-4">
            Silakan pilih bahan baku dan periode terlebih dahulu untuk menampilkan data mutasi.
        </div>
    @endif
@endsection
