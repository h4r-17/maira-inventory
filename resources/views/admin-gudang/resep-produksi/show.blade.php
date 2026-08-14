@extends('layouts.master')

@section('title', '| Detail Resep Produksi')
@section('judul', 'Detail Resep Produksi')

@section('konten')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $produk->nama_produk }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 text-gray-900">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Bahan Baku</th>
                            <th>Satuan</th>
                            <th>Kuantitas</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodyShow">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span id="pageInfoShow" class="text-muted small">Halaman 1 dari 1</span>
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary btn-sm" id="btnPrevPageShow" disabled>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" id="btnNextPageShow" disabled>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_barang }}</td>
                                <td>{{ $item->kode_satuan }}</td>
                                <td>{{ $item->standar_kuantitas }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12 text-left">
            <a href="{{ route('resep-produksi.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                Kembali</a>
            <a href="{{ route('resep-produksi.edit', $produk->id_produk) }}" class="btn btn-warning"><i
                    class="fas fa-pen"></i> Edit</a>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                // Konfigurasi Pagination (10 baris per halaman)
                const rowsPerPage = 8;
                let currentPage = 1;

                // Ambil semua elemen baris tr di dalam tbody
                const rows = $('#tableBodyShow tr');
                const totalRows = rows.length;
                const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

                function updateShowPagination() {
                    // Sembunyikan seluruh baris, lalu tampilkan hanya 10 baris yang sesuai halaman aktif
                    rows.hide();
                    const start = (currentPage - 1) * rowsPerPage;
                    const end = start + rowsPerPage;
                    rows.slice(start, end).show();

                    // Perbarui teks informasi halaman
                    $('#pageInfoShow').text(`Halaman ${currentPage} dari ${totalPages} (Total: ${totalRows} item)`);

                    // Atur status tombol aktif/disabled
                    $('#btnPrevPageShow').prop('disabled', currentPage === 1);
                    $('#btnNextPageShow').prop('disabled', currentPage === totalPages);
                }

                // Event Klik Tombol Sebelumnya
                $('#btnPrevPageShow').on('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        updateShowPagination();
                    }
                });

                // Event Klik Tombol Berikutnya
                $('#btnNextPageShow').on('click', function() {
                    if (currentPage < totalPages) {
                        currentPage++;
                        updateShowPagination();
                    }
                });

                // Jalankan fungsi pagination pertama kali saat halaman dimuat
                updateShowPagination();
            });
        </script>
    @endpush
@endsection
