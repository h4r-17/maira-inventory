@extends('layouts.master')

@section('title', '| Tambah Penolakan')
@section('konten')
@section('judul', 'Form Tambah Penolakan Bahan Baku')
<form action="{{ route('penolakan.store') }}" method="POST" id="formPenolakan" enctype="multipart/form-data">
    @csrf
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Penolakan Bahan Baku</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="no_penolakan" class="text-gray-900">No Penolakan</label>
                        <input type="text" class="form-control" id="no_penolakan" name="no_penolakan"
                            value="{{ old('no_penolakan', $kodePenolakan) }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="batch_produk" class="text-gray-900">Batch Produk</label>
                        <input type="text" name="batch_produk" id="batch_produk" class="form-control"
                            placeholder="Masukkan batch produk" value="{{ old('batch_produk') }}" required>
                        <input type="hidden" name="id_produksi" id="id_produksi" value="{{ old('id_produksi') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_penolakan" class="text-gray-900">Tanggal Penolakan</label>
                        <input type="date" name="tanggal_penolakan" id="tanggal_penolakan" class="form-control"
                            min="{{ \Carbon\Carbon::now()->translatedFormat('Y-m-d') }}" placeholder="Tanggal Penolakan"
                            value="{{ old('tanggal_penolakan') }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status" class="text-gray-900">Keputusan</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">-- Pilih Keputusan --</option>
                            <option value="Retur" {{ old('status') == 'Retur' ? 'selected' : '' }}>Retur</option>
                            <option value="Dimusnahkan" {{ old('status') == 'Dimusnahkan' ? 'selected' : '' }}>
                                Dimusnahkan
                            </option>
                            <option value="Sortir" {{ old('status') == 'Sortir' ? 'selected' : '' }}>Sortir
                            </option>
                        </select>
                        @error('status')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Detail Penolakan Bahan Baku</h6>
            <button type="button" class="btn btn-primary btn-sm" id="btnTambah"><i class="fas fa-plus"></i>
                Tambah</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-gray-900" id="tabelDetail">
                    <thead class="bg-light">
                        <tr>
                            <th>Kode Batch</th>
                            <th>Nama Bahan Baku</th>
                            <th>Jumlah Ditolak</th>
                            <th>Alasan Penolakan</th>
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
            <a href="{{ route('penolakan.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </div>
</form>
@push('scripts')
    <script>
        $(document).ready(function() {
            function bindAutoCompleteBatch(row) {
                row.find('.kode-batch').autocomplete({

                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('autocomplete-batch-penolakan') }}",
                            dataType: "json",
                            data: {
                                term: request.term,
                                id_produksi: $('#id_produksi').val()
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },

                    minLength: 1,

                    select: function(event, ui) {
                        let batchSudahDipilih = false;
                        $('.id_batch').each(function() {
                            if ($(this).val() == ui.item.id_batch) {
                                batchSudahDipilih = true;
                            }
                        });

                        if (batchSudahDipilih) {
                            alert('Batch tersebut sudah dipilih.');
                            return false;
                        }

                        $(this).val(ui.item.label);

                        row.find('.id_batch').val(ui.item.id_batch);
                        row.find('.id_barang').val(ui.item.id_barang);
                        row.find('.nama-barang').val(ui.item.nama_barang);
                        row.find('.kode-batch-value').val(ui.item.label);
                        row.find('.jumlah-ditolak').attr('max', ui.item.sisa_persediaan);

                        return false;
                    }

                }).on('input', function() {

                    row.find('.id_batch').val('');
                    row.find('.id_barang').val('');
                    row.find('.nama-barang').val('');
                    row.find('.kode-batch-value').val('');
                    row.find('.jumlah-ditolak').removeAttr('max');

                });
            }

            $(function() {
                $("#batch_produk").autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('autocomplete-batch-produk') }}",
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },

                    minLength: 1,

                    select: function(event, ui) {
                        $("#batch_produk").val(ui.item.value);
                        $("#id_produksi").val(ui.item.id);
                        $("#tanggal_penolakan").val(ui.item.tanggal_produksi);

                        return false;
                    }
                });
            });

            function addRow(data = {}) {
                let row = $(`
                            <tr>
                                <td>
                                    <input type="text"
                                        class="form-control kode-batch"
                                        autocomplete="off"
                                        placeholder="Masukkan kode batch"
                                        value="${data.kode_batch ?? ''}">

                                        <input type="hidden"
                                        class="kode-batch-value"
                                        name="kode_batch[]"
                                        value="${data.kode_batch ?? ''}">

                                    <input type="hidden"
                                        class="id_batch"
                                        name="id_batch[]"
                                        value="${data.id_batch ?? ''}">
                                </td>

                                <td>
                                    <input type="text"
                                        class="form-control nama-barang"
                                        name="nama_barang[]"
                                        value="${data.nama_barang ?? ''}"
                                        readonly>

                                    <input type="hidden"
                                        class="id_barang"
                                        name="id_barang[]"
                                        value="${data.id_barang ?? ''}">
                                </td>

                                <td>
                                    <input type="number"
                                        class="form-control jumlah-ditolak"
                                        min="1000"
                                        name="jumlah_ditolak[]"
                                        placeholder="Masukkan jumlah ditolak"
                                        value="${data.jumlah_ditolak ?? ''}"
                                        required>
                                </td>

                                <td>
                                    <select class="form-control alasan"
                                        name="alasan_penolakan[]"
                                        required>
                                        <option value="">-- Pilih Alasan --</option>
                                        <option value="Terkontaminasi"
                                            ${data.alasan_penolakan === 'Terkontaminasi' ? 'selected' : ''}>
                                            Terkontaminasi
                                        </option>
                                        <option value="Tidak Sesuai"
                                            ${data.alasan_penolakan === 'Tidak Sesuai' ? 'selected' : ''}>
                                            Tidak Sesuai
                                        </option>
                                        <option value="Kadaluarsa"
                                            ${data.alasan_penolakan === 'Kadaluarsa' ? 'selected' : ''}>
                                            Kadaluarsa
                                        </option>
                                    </select>
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

                bindAutoCompleteBatch(row);
            }

            $('#btnTambah').click(function() {
                addRow();
            });

            $(document).on('click', '.hapus', function() {
                $(this).closest('tr').remove();
            });

            const oldBarang = @json(old('id_barang', []));
            const oldKodeBatch = @json(old('kode_batch', []));
            const oldNamaBarang = @json(old('nama_barang', []));
            const oldBatchBarang = @json(old('id_batch', []));
            const oldJumlahDitolak = @json(old('jumlah_ditolak', []));
            const oldAlasanPenolakan = @json(old('alasan_penolakan', []));
            const oldDeskripsi = @json(old('deskripsi', []));

            if (oldBatchBarang.length > 0) {
                for (let i = 0; i < oldBatchBarang.length; i++) {
                    addRow({
                        id_batch: oldBatchBarang[i],
                        kode_batch: oldKodeBatch[i] ?? '',
                        id_barang: oldBarang[i] ?? '',
                        nama_barang: oldNamaBarang[i] ?? '',
                        jumlah_ditolak: oldJumlahDitolak[i] ?? '',
                        alasan_penolakan: oldAlasanPenolakan[i] ?? '',
                        deskripsi: oldDeskripsi[i] ?? '',
                    });
                }
            }
        });

        $('#formPenolakan').submit(function(e) {
            if ($('#detailTableBody tr').length == 0) {
                $('#error-empty-item').remove();
                $errors = $(
                    '<div id="error-empty-item" class="alert alert-danger"><strong>Data belum valid!</strong><ul class="mb-0 mt-2 pl-3"><li>Minimal satu bahan baku harus ditambahkan.</li></ul></div>'
                );
                $(this).prepend($errors);
                return false;
            } else {
                $('#error-empty-item').remove();
            }
        });
    </script>
@endpush
@endsection
