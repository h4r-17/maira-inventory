@extends('layouts.master')

@section('title', '| Edit Pengajuan')
@section('judul', 'Form Edit Pengajuan')
@section('konten')
    <form action="{{ route('pengajuan.update', $pengajuan->id_pengajuan) }}" method="POST" id="formPengajuan">
        @csrf
        @method('PUT')
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Informasi Pengajuan
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="no_pengajuan" class="text-gray-900">No Pengajuan</label>
                            <input type="text" class="form-control" name="no_pengajuan" id="no_pengajuan"
                                value="{{ old('no_pengajuan', $pengajuan->no_pengajuan) }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tanggal_pengajuan" class="text-gray-900">Tanggal Pengajuan</label>
                            <input type="date" class="form-control" name="tanggal_pengajuan" id="tanggal_pengajuan"
                                value="{{ old('tanggal_pengajuan', $pengajuan->tanggal_pengajuan) }}">
                            @error('tanggal_pengajuan')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="hidden" class="form-control" name="status" id="status"
                                value="{{ old('status', $pengajuan->status) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Detail Pengajuan</h6>
                <button type="button" class="btn btn-primary btn-sm" id="btnTambah"><i class="fas fa-plus"></i>
                    Tambah</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-gray-900" id="tabelDetail">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Bahan Baku</th>
                                <th>Kuantitas</th>
                                <th>Satuan</th>
                                <th>Deskripsi</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
    @push('scripts')
        <script>
            $(document).ready(function() {
                const satuanList = @json($data_satuan);
                const existingDetails = @json($pengajuan->detailPengajuan);
                const oldBarang = @json(old('id_barang'));

                function buildSatuanOptions(selectedId = '') {
                    let options = '<option value="">Pilih Satuan</option>';

                    satuanList.forEach(function(item) {
                        const selected = String(item.id_satuan) === String(selectedId) ? 'selected' : '';
                        options += `<option value="${item.id_satuan}" ${selected}>${item.kode_satuan}</option>`;
                    });

                    return options;
                }

                function bindAutocomplete(row) {
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

                function createRow(data = {}) {
                    const row = $(`
                    <tr>
                        <td>
                            <input type="text" class="form-control barang" placeholder="Masukkan nama barang" autocomplete="off" value="${data.nama_barang ?? ''}">
                            <input type="hidden" name="nama_barang[]" class="nama_barang" value="${data.nama_barang ?? ''}">
                            <input type="hidden" name="id_barang[]" class="id_barang" value="${data.id_barang ?? ''}">
                        </td>
                        <td><input type="number" class="form-control" name="kuantitas[]" min="1" required placeholder="Masukkan jumlah" value="${data.kuantitas ?? ''}"></td>
                        <td><select name="id_satuan[]" class="form-control" required>${buildSatuanOptions(data.id_satuan ?? '')}</select></td>
                        <td>
                            <input type="text" class="form-control" name="deskripsi[]" placeholder="Jika ada" value="${data.deskripsi ?? ''}">
                        </td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" class="form-control" name="harga[]" min="0" step="0.01" placeholder="Jika ada" value="${data.harga ?? ''}">
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm hapus"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `);

                    $('#detailTableBody').append(row);
                    bindAutocomplete(row);
                }

                $('#btnTambah').click(function() {
                    createRow();
                });

                $(document).on('click', '.hapus', function() {
                    $(this).closest('tr').remove();
                });

                if (oldBarang && oldBarang.length > 0) {
                    const oldNamaBarang = @json(old('nama_barang', []));
                    const oldSatuan = @json(old('id_satuan', []));
                    const oldKuantitas = @json(old('kuantitas', []));
                    const oldDeskripsi = @json(old('deskripsi', []));
                    const oldHarga = @json(old('harga', []));

                    for (let i = 0; i < oldBarang.length; i++) {
                        createRow({
                            id_barang: oldBarang[i],
                            nama_barang: oldNamaBarang[i] ?? '',
                            id_satuan: oldSatuan[i] ?? '',
                            kuantitas: oldKuantitas[i] ?? '',
                            deskripsi: oldDeskripsi[i] ?? '',
                            harga: oldHarga[i] ?? ''
                        });
                    }
                } else {
                    existingDetails.forEach(function(item) {
                        createRow({
                            id_barang: item.id_barang,
                            // Ambil nama_barang dari dalam relasi objek barang miliknya Eloquent
                            nama_barang: item.barang ? item.barang.nama_barang : '',
                            id_satuan: item.id_satuan,
                            kuantitas: item.kuantitas,
                            deskripsi: item.deskripsi,
                            harga: item.harga
                        });
                    });
                }
            });

            $('#formPengajuan').submit(function(e) {
                if ($('#detailTableBody tr').length == 0) {
                    $('#error-empty-item').remove(); // hapus pesan error lewat id
                    $errors = $(
                        '<div id="error-empty-item" class="alert alert-danger"><strong>Data belum valid!</strong><ul class="mb-0 mt-2 pl-3"><li>Minimal satu bahan baku harus ditambahkan.</li></ul></div>'
                    );
                    $(this).prepend($errors); // sisipkan pesan error
                    return false;
                } else {
                    $('#error-empty-item').remove();
                }
            });
        </script>
    @endpush
@endsection
