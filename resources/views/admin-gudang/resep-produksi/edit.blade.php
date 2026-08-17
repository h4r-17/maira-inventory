@extends('layouts.master')

@section('title', '| Edit Resep Produksi')
@section('judul', 'Form Edit Resep')

@section('konten')
    <form action="{{ route('resep-produksi.update', $produk->id_produk) }}" method="POST" id="formResepProduksiEdit">
        @csrf
        @method('PUT')

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Produk</h6>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label for="nama_produk_display" class="text-gray-900">Nama Produk</label>
                    <input type="hidden" name="id_produk" value="{{ $produk->id_produk }}">
                    <input type="text" id="nama_produk_display" class="form-control" value="{{ $produk->nama_produk }}"
                        readonly>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Detail Resep</h6>
                <button type="button" class="btn btn-primary btn-sm" id="btnTambahBarisEdit">
                    <i class="fas fa-plus"></i> Tambah Bahan Baku
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span id="pageInfoEdit" class="text-muted small">Halaman 1 dari 1</span>
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary btn-sm" id="btnPrevPageEdit" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnNextPageEdit" disabled>
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
                        <tbody id="detailTableBodyEdit"></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('resep-produksi.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Ambil data input lama dari Laravel jika validasi gagal
                const oldIdBarang = @json(old('id_barang', []));
                const oldNamaBarang = @json(old('nama_barang', []));
                const oldKuantitas = @json(old('standar_kuantitas', [])); // Ambil data kuantitas lama jika ada

                // Ambil data awal dari database untuk mode edit
                const initialRows = @json($data->values());

                // Konfigurasi Pagination Sisi Client
                const rowsPerPage = 5;
                let currentPage = 1;

                function showClientError(message) {
                    $('#client-error-message-edit').remove();
                    const errorBox = $(`
                <div id="client-error-message-edit" class="alert alert-danger">
                    <strong>Data belum valid!</strong>
                    <ul class="mb-0 mt-2 pl-3">
                        <li>${message}</li>
                    </ul>
                </div>
            `);
                    $('#formResepProduksiEdit').prepend(errorBox);
                }

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

                // BAGIAN PERUBAHAN: Menghapus input jumlah_kebutuhan dari kolom <tr>
                function addRow(data = {}) {
                    const row = $(`
                <tr>
                    <td>
                        <input type="text" class="form-control barang-autocomplete" autocomplete="off" placeholder="Cari barang" value="${data.nama_barang ?? ''}">
                        <input type="hidden" name="nama_barang[]" class="nama_barang" value="${data.nama_barang ?? ''}">
                        <input type="hidden" name="id_barang[]" class="id_barang" value="${data.id_barang ?? ''}">
                    </td>
                    <td>
                        <input type="number" name="standar_kuantitas[]" class="form-control" min="1" step="1" value="${data.standar_kuantitas ?? ''}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm hapus-baris-edit">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);

                    $('#detailTableBodyEdit').append(row);
                    bindAutocomplete(row);
                }

                // Fungsi utama mengontrol baris yang tampil & memperbarui info tombol navigasi
                function updatePagination() {
                    const rows = $('#detailTableBodyEdit tr');
                    const totalRows = rows.length;
                    const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

                    if (currentPage > totalPages) {
                        currentPage = totalPages;
                    }

                    // Sembunyikan semua baris, lalu tampilkan 5 baris yang sesuai halaman aktif
                    rows.hide();
                    const start = (currentPage - 1) * rowsPerPage;
                    const end = start + rowsPerPage;
                    rows.slice(start, end).show();

                    // Sinkronisasi teks info halaman dan status tombol di HTML
                    $('#pageInfoEdit').text(`Halaman ${currentPage} dari ${totalPages} (Total: ${totalRows} item)`);
                    $('#btnPrevPageEdit').prop('disabled', currentPage === 1);
                    $('#btnNextPageEdit').prop('disabled', currentPage === totalPages);
                }

                // Event Handler Navigasi Halaman
                $('#btnPrevPageEdit').on('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        updatePagination();
                    }
                });

                $('#btnNextPageEdit').on('click', function() {
                    const totalRows = $('#detailTableBodyEdit tr').length;
                    const totalPages = Math.ceil(totalRows / rowsPerPage);
                    if (currentPage < totalPages) {
                        currentPage++;
                        updatePagination();
                    }
                });

                // Tambah baris baru (Maksimal 30)
                $('#btnTambahBarisEdit').on('click', function() {
                    if ($('#detailTableBodyEdit tr').length >= 30) {
                        showClientError('Maksimal penginputan resep adalah 30 barang.');
                        return;
                    }
                    // Pindahkan halaman aktif ke halaman terakhir saat menambah item baru
                    addRow();
                    const totalRows = $('#detailTableBodyEdit tr').length;
                    currentPage = Math.ceil(totalRows / rowsPerPage);
                    updatePagination();
                });

                $(document).on('click', '.hapus-baris-edit', function() {
                    if ($('#detailTableBodyEdit tr').length === 1) {
                        $('#detailTableBodyEdit input').val('');
                        return;
                    }

                    $(this).closest('tr').remove();
                    updatePagination();
                });

                // BAGIAN PERUBAHAN: Load seluruh data mentah terlebih dahulu, baru jalankan updatePagination sekali di akhir
                if (oldIdBarang.length > 0) {
                    for (let i = 0; i < oldIdBarang.length; i++) {
                        addRow({
                            id_barang: oldIdBarang[i],
                            nama_barang: oldNamaBarang[i] ?? '',
                            standar_kuantitas: oldKuantitas[i] ?? ''
                        });
                    }
                } else if (initialRows.length > 0) {
                    initialRows.forEach(function(item) {
                        addRow(item);
                    });
                } else {
                    addRow();
                }

                // Terapkan pagination awal setelah seluruh data ter-render di tabel
                currentPage = 1;
                updatePagination();

                // Validasi Pre-Submit Form
                $('#formResepProduksiEdit').on('submit', function(e) {
                    const rows = $('#detailTableBodyEdit tr');

                    if (rows.length === 0) {
                        e.preventDefault();
                        showClientError('Minimal satu bahan baku harus ditambahkan.');
                        return;
                    }

                    const ids = [];
                    let hasEmptyRow = false;

                    rows.each(function() {
                        const row = $(this);
                        const idBarang = row.find('.id_barang').val();

                        if (!idBarang) {
                            hasEmptyRow = true;
                        } else {
                            ids.push(idBarang);
                        }
                    });

                    if (hasEmptyRow) {
                        e.preventDefault();
                        showClientError('Semua bahan baku wajib dipilih dari daftar saran.');
                        return;
                    }

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
