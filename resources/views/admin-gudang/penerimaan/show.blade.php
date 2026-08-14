@extends('layouts.master')

@section('title', '| Detail Penerimaan')
@section('konten')
    @php
        $totalItem = $penerimaan->detailPenerimaan->count();
        $totalMasukKonversi = $penerimaan->detailPenerimaan->sum(function ($item) {
            $nilaiKonversi = $item->batch?->barang?->konversiBarang?->nilai_konversi ?? 1;
            return $item->jumlah_masuk * $nilaiKonversi;
        });
    @endphp

    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-3 text-gray-900">Detail Penerimaan: {{ $penerimaan->no_registrasi }}</h3>
            <a href="{{ route('penerimaan.pdf', $penerimaan->id_penerimaan) }}" target="_blank" class="btn btn-info"><i
                    class="fa-solid fa-print"></i> Cetak PDF
            </a>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">No Registrasi</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $penerimaan->no_registrasi }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Tanggal Penerimaan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($penerimaan->tanggal_masuk)->format('d-m-Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan Penerimaan</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3 mb-md-2">
                    <div class="text-dark small">No Registrasi</div>
                    <div class="font-weight-bold text-gray-900">{{ $penerimaan->no_registrasi }}</div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="text-dark small">No Nota</div>
                    <div class="font-weight-bold text-gray-900">{{ $penerimaan->pembelian?->no_nota ?? '-' }}</div>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <div class="text-dark small">No Faktur</div>
                    <div class="font-weight-bold text-gray-900">{{ $penerimaan->no_faktur ?? '-' }}</div>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <div class="text-dark small">No Surat Jalan</div>
                    <div class="font-weight-bold text-gray-900">{{ $penerimaan->surat_jalan ?? '-' }}</div>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <div class="text-dark small">Supplier</div>
                    <div class="font-weight-bold text-gray-900">
                        {{ $penerimaan->pembelian?->supplier?->nama_supplier ?? '-' }}</div>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <div class="text-dark small">Status Penerimaan</div>
                    <div class="font-weight-bold text-gray-900">{{ $penerimaan->jenis_penerimaan }}</div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Bahan Baku yang Diterima</h6>
                </div>
                <span class="text-gray-900">Total: {{ $totalItem }} bahan baku</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 text-gray-900"
                        style="table-layout: fixed">
                        <thead class="table-secondary">
                            <tr>
                                <th class="text-center" style="width: 5%;">No</th>
                                <th class="text-center" style="width: 15%;">Nama Bahan Baku</th>
                                <th class="text-center" style="width: 10%;">Kode Batch</th>
                                <th class="text-center" style="width: 10%;">Jumlah Masuk</th>
                                <th class="text-center" style="width: 15%;">Jumlah Masuk (Gram)</th>
                                <th class="text-center" style="width: 15%;">Tanggal Kadaluarsa</th>
                                <th class="text-center" style="width: 10%;">Jumlah Ditolak</th>
                                <th class="text-center" style="width: 15%;">Alasan Penolakan</th>
                                <th class="text-center" style="width: 15%;">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penerimaan->detailPenerimaan as $item)
                                @php
                                    // Ambil data konversi dari relasi barang
                                    $konversi = $item->batch?->barang?->konversiBarang;

                                    // Ambil kolom 'nilai_konversi' dari gambar. Jika kosong, default 1
                                    $nilaiKonversiItem = $konversi?->nilai_konversi ?? 1;

                                    // Hitung hasil perkalian (Contoh: 2 x 15000 = 30000)
                                    $jumlahHasilKonversi = $item->jumlah_masuk * $nilaiKonversiItem;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->batch?->barang?->nama_barang ?? '-' }}</td>
                                    <td class="text-center">{{ $item->batch?->kode_lot_supplier ?? '-' }}</td>
                                    <td class="text-center">{{ number_format($item->jumlah_masuk, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ number_format($jumlahHasilKonversi, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        {{ $item->batch?->expired_date ? \Carbon\Carbon::parse($item->batch->expired_date)->format('d-m-Y') : '-' }}
                                    </td>
                                    <td class="text-center">{{ $item->jumlah_ditolak ?? '-' }}</td>
                                    <td class="text-center">{{ $item->alasan_penolakan ?? '-' }}</td>
                                    <td>{{ $item->deskripsi ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Tidak ada data bahan baku
                                        pada penerimaan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('penerimaan.index') }}" class="btn btn-secondary mb-5">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
@endsection
