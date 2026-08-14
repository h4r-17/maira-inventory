@extends('layouts.master')

@section('title', '| Detail Produksi')
@section('konten')
    @php
        $totalItem = $produksi->detailProduksi->count();
    @endphp

    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-3 text-gray-900">Detail Produksi: {{ $produksi->nama_produk }}</h3>
            <a href="{{ route('produksi.pdf', $produksi->id_produksi) }}" target="_blank" class="btn btn-info"><i
                    class="fa-solid fa-print"></i> Cetak PDF
            </a>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Batch Produk</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $produksi->batch_produk }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Tanggal Produksi</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($produksi->tanggal_produksi)->format('d-m-Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan Produksi</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="text-dark small">Batch Produk</div>
                    <div class="font-weight-bold text-gray-900">{{ $produksi->batch_produk }}</div>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <div class="text-dark small">Hasil Produksi</div>
                    <div class="font-weight-bold text-gray-900">{{ $produksi->hasil_produksi }} PCS</div>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <div class="text-dark small">Tujuan Produksi</div>
                    <div class="font-weight-bold text-gray-900">{{ $produksi->tujuan_produksi }}</div>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <div class="text-dark small">Produk Expired</div>
                    <div class="font-weight-bold text-gray-900">
                        {{ \Carbon\Carbon::parse($produksi->produk_expired)->format('d-m-Y') }}</div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Barang yang Dipakai</h6>
                </div>
                <span class="text-gray-900">Total: {{ $totalItem }} barang</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span id="pageInfoProduksiShow" class="text-muted small">Halaman 1 dari 1</span>
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary btn-sm" id="btnPrevPageProduksiShow" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnNextPageProduksiShow" disabled>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover align-middle mb-0 text-gray-900">
                        <thead class="table-secondary">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Barang</th>
                                <th class="text-center">Kode Batch</th>
                                <th class="text-center">Jumlah Keluar</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBodyProduksiShow">
                            @foreach ($produksi->detailProduksi as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->batch->barang->nama_barang }}</td>
                                    <td class="text-center">{{ $item->batch->kode_lot_supplier ?? '-' }}</td>
                                    <td class="text-center">{{ $item->jumlah_keluar }}</td>
                                    <td class="text-center">{{ $item->batch->barang->satuan->kode_satuan }}</td>
                                    <td>{{ $item->deskripsi ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('produksi.index') }}" class="btn btn-secondary mb-4">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    @push('scripts')
        <script>
            $(document).ready(function() {
                // Konfigurasi Pagination (10 baris per halaman)
                const rowsPerPage = 8;
                let currentPage = 1;

                // Ambil semua elemen baris tr di dalam tbody
                const rows = $('#tableBodyProduksiShow tr');
                const totalRows = rows.length;
                const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

                function updateProduksiShowPagination() {
                    // Sembunyikan seluruh baris, lalu tampilkan hanya 10 baris yang sesuai halaman aktif
                    rows.hide();
                    const start = (currentPage - 1) * rowsPerPage;
                    const end = start + rowsPerPage;
                    rows.slice(start, end).show();

                    // Perbarui teks informasi halaman
                    $('#pageInfoProduksiShow').text(
                        `Halaman ${currentPage} dari ${totalPages} (Total: ${totalRows} item)`);

                    // Atur status aktif/disabled pada tombol navigasi
                    $('#btnPrevPageProduksiShow').prop('disabled', currentPage === 1);
                    $('#btnNextPageProduksiShow').prop('disabled', currentPage === totalPages);
                }

                // Event Klik Tombol Sebelumnya
                $('#btnPrevPageProduksiShow').on('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        updateProduksiShowPagination();
                    }
                });

                // Event Klik Tombol Berikutnya
                $('#btnNextPageProduksiShow').on('click', function() {
                    if (currentPage < totalPages) {
                        currentPage++;
                        updateProduksiShowPagination();
                    }
                });

                // Jalankan pembagian halaman pertama kali saat data siap
                updateProduksiShowPagination();
            });
        </script>
    @endpush
@endsection
