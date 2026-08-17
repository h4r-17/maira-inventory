@extends('layouts.master')

@section('title', '| Edit Produksi')
@section('konten')
@section('judul', 'Form Edit Produksi')
<form action="{{ route('produksi.update', $produksi->id_produksi) }}" method="POST" id="formProduksi"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Produksi</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group form-row">
                        <label for="nama_produk" class="text-gray-900">Nama Produk</label>
                        <input type="text" class="form-control produk" placeholder="Masukkan nama produk"
                            autocomplete="off" id="nama_produk" name="nama_produk"
                            value="{{ old('nama_produk', $produksi->barangJadi->nama_produk) }}" required readonly>
                        <input type="hidden" name="id_produk" class="id_produk"
                            value="{{ old('id_produk', $produksi->barangJadi->id_produk) }}">
                    </div>
                </div>
                @php
                    $batchFull = old('batch_produk', $produksi->batch_produk);
                    $hasilProduksi = old('hasil_produksi', $produksi->hasil_produksi);
                    $suffix = '-' . $hasilProduksi;
                    $prefix = ($produksi->barangJadi->kode_produk ?? '') . '-';
                    $tengah = $batchFull;
                    if (str_starts_with($tengah, $prefix)) {
                        $tengah = substr($tengah, strlen($prefix));
                    }
                    if (str_ends_with($tengah, $suffix)) {
                        $tengah = substr($tengah, 0, -strlen($suffix));
                    }
                @endphp
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="batch_produk_tengah" class="text-gray-900">Batch Produk</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"
                                    id="batch_prefix">{{ old('batch_prefix', $prefix) }}</span>
                            </div>
                            <input type="text" class="form-control" id="batch_produk_tengah"
                                name="batch_produk_tengah" placeholder="Kode Tengah"
                                value="{{ old('batch_produk_tengah', $tengah) }}" required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="batch_suffix">-{{ $hasilProduksi }}</span>
                            </div>
                        </div>
                        <input type="hidden" id="batch_produk" name="batch_produk" value="{{ $batchFull }}">
                        <input type="hidden" id="batch_prefix_input" name="batch_prefix"
                            value="{{ old('batch_prefix', $prefix) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_produksi" class="text-gray-900">Tanggal Produksi</label>
                        <input type="date" class="form-control" id="tanggal_produksi" name="tanggal_produksi"
                            placeholder="Masukkan tanggal produksi"
                            value="{{ old('tanggal_produksi', $produksi->tanggal_produksi) }}"
                            min="{{ isset($produksi) ? $produksi->tanggal_produksi : date('Y-m-d') }}" required>
                        @error('tanggal_produksi')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="hasil_produksi" class="text-gray-900">Hasil Produksi</label>
                        <input type="number" class="form-control" id="hasil_produksi" name="hasil_produksi"
                            placeholder="Masukkan hasil produksi produk" min="1"
                            value="{{ old('hasil_produksi', $produksi->hasil_produksi) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="produk_expired" class="text-gray-900">Produk Expired</label>
                        <input type="date" class="form-control" id="produk_expired" name="produk_expired"
                            min="{{ date('Y-m-d', strtotime('+1 year')) }}"
                            placeholder="Masukkan tanggal expired produk"
                            value="{{ old('produk_expired', $produksi->produk_expired) ?? date('Y-m-d', strtotime('+1 year')) }}"
                            required>
                        @error('produk_expired')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tujuan_produksi" class="text-gray-900">Tujuan Produksi</label>
                        <input type="text" class="form-control" id="tujuan_produksi" name="tujuan_produksi"
                            placeholder="Masukkan tujuan produksi"
                            value="{{ old('tujuan_produksi', $produksi->tujuan_produksi) }}" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-5">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Detail Produksi</h6>
            {{-- <button type="button" class="btn btn-primary btn-sm" id="btnTambah"><i class="fas fa-plus"></i>
                Tambah</button> --}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span id="pageInfoProduksiEdit" class="text-muted small">Halaman 1 dari 1</span>
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary btn-sm" id="btnPrevPageProduksiEdit"
                            disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="btnNextPageProduksiEdit"
                            disabled>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <table class="table table-bordered text-gray-900" id="tabelDetail">
                    <thead class="bg-light">
                        <tr>
                            <th>Nama Bahan Baku</th>
                            <th>Jumlah Keluar (Gram)</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="detailTableBody">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('produksi.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </div>
</form>
@php
    $mappedDetails = $produksi->detailProduksi->map(function ($item) use ($resep) {
        $idBarang = $item->batch?->id_barang ?? '';

        return [
            'id_barang' => $idBarang,
            'nama_barang' => $item->batch?->barang?->nama_barang ?? '',
            'standar_kuantitas' => $resep[$idBarang]->standar_kuantitas ?? 0,
            'jumlah_keluar' => $item->jumlah_keluar,
            'deskripsi' => $item->deskripsi,
        ];
    });
@endphp
@push('scripts')
    <script>
        $(document).ready(function() {
            const resepUrlBase = '{{ url('/get-resep') }}';
            const productRow = $('.form-row');

            function updateBatchProduk() {
                let prefix = $('#batch_prefix_input').val();
                let tengah = $('#batch_produk_tengah').val();
                let suffix = $('#hasil_produksi').val() ? '-' + $('#hasil_produksi').val() : '-0';

                $('#batch_produk').val(prefix + tengah + suffix);
            }

            // --- PERUBAHAN 1: Tambahkan Konfigurasi Pagination (Batas 5 Baris) ---
            const rowsPerPage = 5;
            let currentPage = 1;

            function bindAutoComplete(row) {
                row.find('.barang').autocomplete({
                    source: '{{ route('autocomplete-barang') }}',
                    minLength: 1,
                    select: function(event, ui) {
                        $(this).val(ui.item.label);
                        row.find('.id_barang').val(ui.item.id);
                        row.find('.nama_barang').val(ui.item.label);
                        return false;
                    }
                }).on('input', function() {
                    row.find('.id_barang').val('');
                    row.find('.nama_barang').val($(this).val());
                });
            }

            function addRow(data = {}) {
                const row = $(`
                        <tr>
                            <td>
                                <input type="text"
                                    class="form-control barang"
                                    autocomplete="off"
                                    placeholder="Masukkan nama bahan baku"
                                    value="${data.nama_barang ?? ''}">

                                <input type="hidden"
                                    class="nama_barang"
                                    name="nama_barang[]"
                                    value="${data.nama_barang ?? ''}">

                                <input type="hidden"
                                    class="id_barang"
                                    name="id_barang[]"
                                    value="${data.id_barang ?? ''}">

                                <input type="hidden"
                                    class="standar_kuantitas"
                                    name="standar_kuantitas[]"
                                    value="${data.standar_kuantitas ?? ''}">
                            </td>

                            <td>
                                <input type="number"
                                    class="form-control jumlah-keluar"
                                    min="1"
                                    name="jumlah_keluar[]"
                                    placeholder="Masukkan jumlah keluar"
                                    value="${data.jumlah_keluar ?? ''}">
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control"
                                    name="deskripsi[]"
                                    placeholder="Jika ada"
                                    value="${data.deskripsi ?? ''}">
                            </td>

                            <td class="text-center">
                                <button type="button"
                                    class="btn btn-danger btn-sm hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                $('#detailTableBody').append(row);
                bindAutoComplete(row);
            }

            // --- PERUBAHAN 2: Fungsi Utama Pengontrol Tampilan Baris Halaman ---
            function updatePagination() {
                const rows = $('#detailTableBody tr');
                const totalRows = rows.length;
                const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

                if (currentPage > totalPages) {
                    currentPage = totalPages;
                }

                // Sembunyikan semua, hanya tampilkan 5 baris di halaman aktif
                rows.hide();
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                rows.slice(start, end).show();

                // Perbarui visual teks info halaman & tombol navigasi
                $('#pageInfoProduksiEdit').text(
                    `Halaman ${currentPage} dari ${totalPages} (Total: ${totalRows} item)`);
                $('#btnPrevPageProduksiEdit').prop('disabled', currentPage === 1);
                $('#btnNextPageProduksiEdit').prop('disabled', currentPage === totalPages);
            }

            // --- PERUBAHAN 3: Event Handler Navigasi Klik Tombol Halaman ---
            $('#btnPrevPageProduksiEdit').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    updatePagination();
                }
            });

            $('#btnNextPageProduksiEdit').on('click', function() {
                const totalRows = $('#detailTableBody tr').length;
                const totalPages = Math.ceil(totalRows / rowsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    updatePagination();
                }
            });

            function clearDetailRows() {
                $('#detailTableBody').empty();
                currentPage = 1; // Reset ke halaman 1 saat tabel dibersihkan
                updatePagination();
            }

            function showClientError(message) {
                $('#error-empty-item').remove();
                const errorBox = $(`
                            <div id="error-empty-item" class="alert alert-danger">
                                <strong>Data belum valid!</strong>
                                <ul class="mb-0 mt-2 pl-3">
                                    <li>${message}</li>
                                </ul>
                            </div>
                        `);

                $('#formProduksi').prepend(errorBox);
            }

            // event listener untuk menghitung jumlah keluar berdasarkan hasil produksi
            $('#hasil_produksi').on('input', function() {
                const hasilProduksi = parseInt($(this).val()) || 0;

                $('#detailTableBody tr').each(function() {
                    const row = $(this);
                    const standarKuantitas =
                        parseFloat(row.find('.standar_kuantitas').val()) || 0;
                    const jumlahKeluar = standarKuantitas * hasilProduksi;
                    row.find('.jumlah-keluar').val(jumlahKeluar);
                });

                $('#batch_suffix').text('-' + (hasilProduksi || '0'));
                updateBatchProduk();
            });

            function renderRecipeRows(rows) {
                clearDetailRows();

                if (!rows.length) {
                    showClientError('Resep produksi untuk produk ini belum tersedia.');
                    return;
                }

                $('#error-empty-item').remove();
                const hasilProduksi = parseInt($('#hasil_produksi').val()) || 1;

                rows.forEach(function(item) {
                    const jumlahKeluar = item.standar_kuantitas * hasilProduksi;

                    addRow({
                        id_barang: item.id_barang,
                        nama_barang: item.nama_barang,
                        standar_kuantitas: item.standar_kuantitas,
                        jumlah_keluar: jumlahKeluar
                    });
                });

                // --- PERUBAHAN 4: Jalankan Pagination Sekali Saja Setelah AJAX Selesai Loop ---
                currentPage = 1;
                updatePagination();
            }

            function loadRecipeByProduct(idProduk) {
                if (!idProduk) {
                    clearDetailRows();
                    return;
                }

                $.getJSON(`${resepUrlBase}/${idProduk}`)
                    .done(function(response) {
                        renderRecipeRows(response);
                    })
                    .fail(function() {
                        clearDetailRows();
                        showClientError('Resep produksi tidak dapat dimuat.');
                    });
            }

            productRow.find('.produk').autocomplete({
                source: '{{ route('autocomplete-produk') }}',
                minLength: 1,
                select: function(event, ui) {
                    $(this).val(ui.item.label);
                    productRow.find('.id_produk').val(ui.item.id);
                    productRow.find('.nama_produk').val(ui.item.label);

                    $('#batch_prefix').text(ui.item.batch + '-');
                    $('#batch_prefix_input').val(ui.item.batch + '-');
                    updateBatchProduk();

                    $('#batch_produk_tengah').focus();
                    loadRecipeByProduct(ui.item.id);
                    return false;
                }
            }).on('input', function() {
                productRow.find('.id_produk').val('');
                productRow.find('.nama_produk').val($(this).val());
                $('#batch_prefix').text('KODE-');
                $('#batch_prefix_input').val('KODE-');
                $('#batch_produk_tengah').val('');
                updateBatchProduk();
                clearDetailRows();
            });

            // event listener untuk update hidden batch_produk
            $('#batch_produk_tengah').on('input', function() {
                updateBatchProduk();
            });

            $('#btnTambah').click(function() {
                // --- PERUBAHAN 5: Pindahkan Halaman ke Paling Akhir saat Menambah Baris Manual ---
                addRow();
                const totalRows = $('#detailTableBody tr').length;
                currentPage = Math.ceil(totalRows / rowsPerPage);
                updatePagination();
            });

            $(document).on('click', '.hapus', function() {
                $(this).closest('tr').remove();
                updatePagination
                    (); // --- PERUBAHAN 6: Kalkulasi Ulang Jumlah Halaman Setelah Baris Dihapus ---
            });

            const oldBarang = @json(old('id_barang', []));
            const oldNamaBarang = @json(old('nama_barang', []));
            const oldJumlahKeluar = @json(old('jumlah_keluar', []));
            const oldStandarKuantitas = @json(old('standar_kuantitas', []));
            const oldDeskripsi = @json(old('deskripsi', []));
            const existingDetails = @json($mappedDetails);

            // --- PERUBAHAN 7: Atur Alur Muat Data Masal, Lalu Eksekusi Pagination di Akhir ---
            if (oldBarang.length > 0) {
                for (let i = 0; i < oldBarang.length; i++) {
                    addRow({
                        id_barang: oldBarang[i],
                        nama_barang: oldNamaBarang[i] ?? '',
                        jumlah_keluar: oldJumlahKeluar[i] ?? '',
                        standar_kuantitas: oldStandarKuantitas[i] ?? '',
                        deskripsi: oldDeskripsi[i] ?? '',
                    });
                }
            } else if (existingDetails.length > 0) {
                existingDetails.forEach(function(item) {
                    addRow(item);
                });
            } else if (productRow.find('.id_produk').val()) {
                loadRecipeByProduct(productRow.find('.id_produk').val());
            }

            // Terapkan pembagian halaman pertama kali saat halaman edit selesai memuat data awal
            currentPage = 1;
            updatePagination();

            $('#formProduksi').on('submit', function(e) {
                if ($('#detailTableBody tr').length === 0) {
                    e.preventDefault();
                    showClientError('Minimal satu barang harus ditambahkan.');
                    return false;
                }
                $('#error-empty-item').remove();
            });

            // Initialize batch produk value
            updateBatchProduk();
        });
    </script>
@endpush
@endsection
