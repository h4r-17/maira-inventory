@extends('layouts.master')

@section('title', '| Tambah Resep Produksi')
@section('judul', 'Form Tambah Resep')

@section('konten')
    <form action="{{ route('resep-produksi.store') }}" method="POST" id="formResepProduksi" enctype="multipart/form-data">
        @csrf
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Produk</h6>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label for="id_produk" class="text-gray-900">Nama Produk</label>
                    <select class="form-control" id="id_produk" name="id_produk" required>
                        <option value="">Pilih Produk</option>
                        @foreach ($data_produk as $produk)
                            <option value="{{ $produk->id_produk }}" @selected(old('id_produk') == $produk->id_produk)>
                                {{ $produk->nama_produk }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_produk')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Detail Resep</h6>
                <button type="button" class="btn btn-primary btn-sm" id="btnTambahBaris">
                    <i class="fas fa-plus"></i> Tambah Bahan Baku
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span id="pageInfo" class="text-muted small">Halaman 1 dari 1</span>
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary btn-sm" id="btnPrevPage" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnNextPage" disabled>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <table class="table table-bordered text-gray-900 mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Bahan Baku</th>
                                <th>Kuantitas</th>
                                <th class="text-center" style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Tambah</button>
                <a href="{{ route('resep-produksi.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Mengambil data lama dari Laravel jika validasi gagal
                const oldIdBarang = @json(old('id_barang', []));
                const oldNamaBarang = @json(old('nama_barang', []));
                const oldStandarKuantitas = @json(old('standar_kuantitas', []));

                // Konfigurasi Pagination Sisi Client
                const rowsPerPage = 5;
                let currentPage = 1;

                // Fungsi menampilkan pesan error validasi di atas form
                function showClientError(message) {
                    $('#client-error-message').remove();
                    const errorBox = $(`
                <div id="client-error-message" class="alert alert-danger">
                    <strong>Data belum valid!</strong>
                    <ul class="mb-0 mt-2 pl-3">
                        <li>${message}</li>
                    </ul>
                </div>
            `);
                    $('#formResepProduksi').prepend(errorBox);
                }

                // Fungsi Autocomplete jQuery UI
                function bindAutocomplete(row) {
                    row.find('.barang-autocomplete').autocomplete({
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

                // Fungsi Menambah Baris Baru (Sesuai Aturan Validasi Baru Anda)
                function addRow(data = {}) {
                    const row = $(`
                <tr>
                    <td>
                        <input type="text" class="form-control barang-autocomplete" autocomplete="off" placeholder="Cari barang" value="${data.nama_barang ?? ''}">
                        <!-- nama_barang disimpan sebagai hidden agar teksnya tidak hilang saat reload old() -->
                        <input type="hidden" name="nama_barang[]" class="nama_barang" value="${data.nama_barang ?? ''}">
                        <input type="hidden" name="id_barang[]" class="id_barang" value="${data.id_barang ?? ''}">
                    </td>
                    <td>
                        <input type="number" name="standar_kuantitas[]" class="form-control" min="1" value="${data.standar_kuantitas ?? ''}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm hapus-baris">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);

                    $('#detailTableBody').append(row);
                    bindAutocomplete(row);

                    // Otomatis arahkan halaman ke baris yang baru saja ditambahkan
                    const totalRows = $('#detailTableBody tr').length;
                    currentPage = Math.ceil(totalRows / rowsPerPage);
                    updatePagination();
                }

                // Fungsi Mengontrol Halaman dan Baris yang Tampil (Maksimal 5)
                function updatePagination() {
                    const rows = $('#detailTableBody tr');
                    const totalRows = rows.length;
                    const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

                    // Jaga index halaman jika ada baris yang dihapus
                    if (currentPage > totalPages) {
                        currentPage = totalPages;
                    }

                    // Sembunyikan semua, tampilkan hanya 5 baris di halaman aktif
                    rows.hide();
                    const start = (currentPage - 1) * rowsPerPage;
                    const end = start + rowsPerPage;
                    rows.slice(start, end).show();

                    // Perbarui teks informasi halaman & status tombol
                    $('#pageInfo').text(`Halaman ${currentPage} dari ${totalPages} (Total: ${totalRows} item)`);
                    $('#btnPrevPage').prop('disabled', currentPage === 1);
                    $('#btnNextPage').prop('disabled', currentPage === totalPages);
                }

                // Navigasi Tombol Halaman Sebelum / Sesudah
                $('#btnPrevPage').on('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        updatePagination();
                    }
                });

                $('#btnNextPage').on('click', function() {
                    const totalRows = $('#detailTableBody tr').length;
                    const totalPages = Math.ceil(totalRows / rowsPerPage);
                    if (currentPage < totalPages) {
                        currentPage++;
                        updatePagination();
                    }
                });

                // Event Tombol Tambah Baris (Maksimal Batas 30 Baris)
                $('#btnTambahBaris').on('click', function() {
                    if ($('#detailTableBody tr').length >= 30) {
                        showClientError('Maksimal penginputan resep adalah 30 barang.');
                        return;
                    }
                    addRow();
                });

                // Event Tombol Hapus Baris
                $(document).on('click', '.hapus-baris', function() {
                    if ($('#detailTableBody tr').length === 1) {
                        $('#detailTableBody input').val('');
                        return;
                    }

                    $(this).closest('tr').remove();
                    updatePagination();
                });

                // Memuat Kembali Data Lama / Inisiasi Baris Pertama
                if (oldIdBarang.length > 0) {
                    for (let i = 0; i < oldIdBarang.length; i++) {
                        addRow({
                            id_barang: oldIdBarang[i],
                            nama_barang: oldNamaBarang[i] ?? '',
                            standar_kuantitas: oldStandarKuantitas[i] ?? ''
                        });
                    }
                    // Reset ke halaman 1 saat pertama kali reload data gagal
                    currentPage = 1;
                    updatePagination();
                } else {
                    addRow();
                }

                // Validasi Pre-Submit Client-Side (Memeriksa seluruh baris di semua halaman)
                $('#formResepProduksi').on('submit', function(e) {
                    const rows = $('#detailTableBody tr');

                    if (rows.length === 0) {
                        e.preventDefault();
                        showClientError('Minimal satu barang harus ditambahkan.');
                        return;
                    }

                    const ids = [];
                    let hasEmptyRow = false;

                    rows.each(function() {
                        const row = $(this);
                        const idBarang = row.find('.id_barang').val();

                        if (!idBarang) {
                            hasEmptyRow = true;
                        }

                        if (idBarang) {
                            ids.push(idBarang);
                        }
                    });

                    if (hasEmptyRow) {
                        e.preventDefault();
                        showClientError(
                            'Semua bahan baku wajib dipilih dari saran.');
                        return;
                    }

                    // Validasi duplikasi bahan baku (Sesuai aturan distinct pada Laravel)
                    const uniqueIds = [...new Set(ids)];
                    if (uniqueIds.length !== ids.length) {
                        e.preventDefault();
                        showClientError('Bahan baku tidak boleh sama dalam satu resep produk.');
                    }
                });
            });
        </script>
    @endpush
@endsection
