@extends('layouts.master')

@section('title', '| Edit Pembelian')
@section('konten')
@section('judul', 'Form Edit Pembelian')
<form action="{{ route('pembelian.update', $pembelian->id_pembelian) }}" method="POST" id="formPembelian">
    @csrf
    @method('PUT')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pembelian</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="no_nota" class="text-gray-900">No Nota</label>
                        <input type="text" class="form-control" id="no_nota" name="no_nota"
                            value="{{ old('no_nota', $pembelian->no_nota) }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_pembelian" class="text-gray-900">Tanggal Pembelian</label>
                        <input type="date" class="form-control" id="tanggal_pembelian" name="tanggal_pembelian"
                            value="{{ old('tanggal_pembelian', $pembelian->tanggal_pembelian) }}" required>
                        @error('tanggal_pembelian')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="no_pengajuan" class="text-gray-900">No Pengajuan</label>
                        <!-- Input untuk mengetik dan memunculkan autocomplete -->
                        <input type="text" name="no_pengajuan" id="no_pengajuan" class="form-control"
                            placeholder="No Pengajuan"
                            value="{{ old('no_pengajuan', $pembelian->pengajuan->no_pengajuan) }}" readonly>
                        <!-- Input hidden untuk menampung primary key id_pengajuan -->
                        <input type="hidden" name="id_pengajuan" id="id_pengajuan"
                            value="{{ old('id_pengajuan', $pembelian->pengajuan->id_pengajuan) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id_supplier" class="text-gray-900">Supplier</label>
                        <select class="form-control" id="id_supplier" name="id_supplier" required>
                            <option value="">-- Pilih Supplier -- </option>
                            @foreach ($data_supplier as $supplier)
                                <option value="{{ $supplier->id_supplier }}"
                                    {{ old('id_supplier', $pembelian->id_supplier) == $supplier->id_supplier ? 'selected' : '' }}>
                                    {{ $supplier->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_supplier')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cara_bayar" class="text-gray-900">Cara Bayar</label>
                        <select class="form-control" id="cara_bayar" name="cara_bayar" required>
                            <option value="">-- Pilih Cara Bayar --</option>
                            <option value="Tunai"
                                {{ old('cara_bayar', $pembelian->cara_bayar) == 'Tunai' ? 'selected' : '' }}>Tunai
                            </option>
                            <option value="Net 14 Hari"
                                {{ old('cara_bayar', $pembelian->cara_bayar) == 'Net 14 Hari' ? 'selected' : '' }}>Net
                                14 Hari</option>
                            <option value="Net 30 Hari"
                                {{ old('cara_bayar', $pembelian->cara_bayar) == 'Net 30 Hari' ? 'selected' : '' }}>Net
                                30 Hari</option>
                        </select>
                        @error('cara_bayar')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-5">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Detail Pembelian</h6>
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
            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </div>
</form>
@push('scripts')
    <script>
        $(document).ready(function() {
            const satuanList = @json($data_satuan);
            const existingDetails = @json($pembelian->detailPembelian);
            const oldBarang = @json(old('id_barang'));

            function buildSatuanOptions(selectedId = '') {
                let options = '<option value="">Pilih Satuan</option>';

                satuanList.forEach(function(item) {
                    const selected = String(item.id_satuan) === String(selectedId) ? 'selected' : '';
                    options += `<option value="${item.id_satuan}" ${selected}>${item.kode_satuan}</option>`;
                });

                return options;
            }

            function escapeHtml(value) {
                return $('<div>').text(value ?? '').html();
            }

            function fillFromPengajuan(item) {
                $('#id_pengajuan').val(item.id);
                $('#detailTableBody').empty();

                (item.details ?? []).forEach(function(detail) {
                    createRow({
                        id_barang: detail.id_barang ?? '',
                        nama_barang: detail.nama_barang ?? '',
                        kuantitas: detail.kuantitas ?? '',
                        id_satuan: detail.id_satuan ?? '',
                        harga: detail.harga ?? '',
                        deskripsi: detail.deskripsi ?? ''
                    });
                });
            }

            $('#kode_pengajuan').autocomplete({
                source: '{{ route('autocomplete-pengajuan') }}',
                minLength: 1,
                select: function(event, ui) {
                    $(this).val(ui.item.value);
                    fillFromPengajuan(ui.item);
                    return false;
                }
            }).on('input', function() {
                $('#id_pengajuan').val('');
            });

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
                            <input type="text" class="form-control barang" autocomplete="off" placeholder="Masukkan nama barang" value="${escapeHtml(data.nama_barang ?? '')}">
                            <input type="hidden" class="nama_barang" name="nama_barang[]" value="${escapeHtml(data.nama_barang ?? '')}">
                            <input type="hidden" class="id_barang" name="id_barang[]" value="${escapeHtml(data.id_barang ?? '')}">
                        </td>
                        <td><input type="number" class="form-control" min="1" name="kuantitas[]" placeholder="Masukkan jumlah" required value="${escapeHtml(data.kuantitas ?? '')}"></td>
                        <td><select class="form-control satuan" name="id_satuan[]" required>${buildSatuanOptions(data.id_satuan ?? '')}</select></td>
                        <td><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">Rp</span></div><input type="number" class="form-control" name="harga[]" min="1" step="0.01" placeholder="Masukkan harga" required value="${escapeHtml(data.harga ?? '')}"></div></td>
                        <td><input type="text" class="form-control" name="deskripsi[]" placeholder="Jika ada" value="${escapeHtml(data.deskripsi ?? '')}"></td>
                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus"><i class="fas fa-trash"></i></button></td>
                    </tr>`);

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
                const oldDeskripsi = @json(old('deskripsi', []));
                const oldKuantitas = @json(old('kuantitas', []));
                const oldSatuan = @json(old('id_satuan', []));
                const oldHarga = @json(old('harga', []));

                for (let i = 0; i < oldBarang.length; i++) {
                    createRow({
                        id_barang: oldBarang[i],
                        nama_barang: oldNamaBarang[i] ?? '',
                        deskripsi: oldDeskripsi[i] ?? '',
                        kuantitas: oldKuantitas[i] ?? '',
                        id_satuan: oldSatuan[i] ?? '',
                        harga: oldHarga[i] ?? ''
                    });
                }
            } else {
                existingDetails.forEach(function(item) {
                    createRow({
                        id_barang: item.id_barang,
                        nama_barang: item.barang ? item.barang.nama_barang : '',
                        deskripsi: item.deskripsi ?? '',
                        kuantitas: item.kuantitas ?? '',
                        id_satuan: item.id_satuan ?? '',
                        harga: item.harga ?? ''
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
