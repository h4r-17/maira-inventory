@extends('layouts.master')
@section('title', '| Edit Retur')
@section('konten')
@section('judul', 'Form Edit Retur')
<form action="{{ route('retur.update', $retur->id_retur) }}" method="POST" id="formRetur" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Informasi Retur
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="no_retur" class="text-gray-900">No Retur</label>
                        <input type="text" class="form-control" name="no_retur" id="no_retur"
                            value="{{ old('no_retur', $retur->no_retur) }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_retur" class="text-gray-900">Tanggal Retur</label>
                        <input type="date" class="form-control" name="tanggal_retur" id="tanggal_retur"
                            value="{{ old('tanggal_retur', $retur->tanggal_retur) }}" min="{{ $retur->tanggal_retur }}"
                            required>
                        @error('tanggal_retur')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="jenis_sumber_retur" class="text-gray-900">Sumber Retur</label>
                        <select class="form-control" id="jenis_sumber_retur" readonly>
                            <option value="">-- Pilih Sumber Retur --</option>
                            <option value="penolakan" {{ old('id_penolakan', $retur->id_penolakan) ? 'selected' : '' }}>
                                Penolakan Produksi
                            </option>
                            <option value="penerimaan"
                                {{ old('id_penerimaan', $retur->id_penerimaan) ? 'selected' : '' }}>Penolakan Penerimaan
                            </option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="id_penolakan" id="id_penolakan"
                    value="{{ old('id_penolakan', $retur->id_penolakan) }}">
                <input type="hidden" name="id_penerimaan" id="id_penerimaan"
                    value="{{ old('id_penerimaan', $retur->id_penerimaan) }}">
                <div class="col-md-4" id="wrapperPenolakan" style="display: none;">
                    <div class="form-group">
                        <label for="select_penolakan" class="text-gray-900">No Penolakan</label>
                        <select class="form-control" id="select_penolakan">
                            <option value="">-- Pilih Penolakan --</option>
                            @foreach ($penolakan as $pen)
                                <option value="{{ $pen->id_penolakan }}"
                                    {{ old('id_penolakan') == $pen->id_penolakan ? 'selected' : '' }}>
                                    {{ $pen->no_penolakan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4" id="wrapperPenerimaan" style="display: none;">
                    <div class="form-group">
                        <label for="no_registrasi" class="text-gray-900">No Registrasi Penerimaan</label>
                        <input type="text" class="form-control" id="no_registrasi"
                            placeholder="Masukkan no registrasi penerimaan" value="{{ old('no_registrasi') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="no_nota" class="text-gray-900">No Nota</label>
                        <!-- Input untuk mengetik dan memunculkan autocomplete -->
                        <input type="text" name="no_nota" id="no_nota" class="form-control"
                            placeholder="Nomor Nota" value="{{ old('no_nota', $retur->pembelian->no_nota) }}" readonly>
                        <!-- Input hidden untuk menampung primary key id_pembelian -->
                        <input type="hidden" name="id_pembelian" id="id_pembelian"
                            value="{{ old('id_pembelian', $retur->id_pembelian) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id_supplier" class="text-gray-900">Supplier</label>
                        <!-- Input Text: Hanya untuk menampilkan Nama (User Friendly) -->
                        <input type="text" class="form-control" id="nama_supplier"
                            value="{{ old('nama_supplier', $retur->pembelian->supplier->nama_supplier ?? '') }}"
                            readonly>
                        <input type="hidden" class="form-control" name="id_supplier" id="id_supplier"
                            value="{{ old('id_supplier', $retur->pembelian->id_supplier) }}" readonly>
                        @error('id_supplier')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Detail Retur</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-gray-900" id="tabelDetail">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Bahan Baku</th>
                                <th>Kode Batch</th>
                                <th>Jumlah Retur</th>
                                <th>Satuan</th>
                                <th>Tanggal Kadaluarsa</th>
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
                <a href="{{ route('retur.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
@push('scripts')
    <script>
        $(document).ready(function() {

            function addRow(data = {}) {
                let formattedDate = '';
                if (data.expired_date) {
                    formattedDate = String(data.expired_date).substring(0, 10);
                }
                let row = $(`
                        <tr>
                            <td>
                                <span>${data.nama_barang ?? ''}</span>
                                <input
                                    type="hidden"
                                    name="id_detail_penolakan[]"
                                    value="${data.id_detail_penolakan ?? ''}">

                                <input
                                    type="hidden"
                                    name="id_detail_penerimaan[]"
                                    value="${data.id_detail_penerimaan ?? ''}">

                                <input
                                    type="hidden"
                                    name="id_barang[]"
                                    value="${data.id_barang ?? ''}">

                                <input
                                    type="hidden"
                                    name="nama_barang[]"
                                    value="${data.nama_barang ?? ''}">
                            </td>

                            <td>
                                <span>${data.batch_barang ?? ''}</span>
                                <input type="hidden" name="batch_barang[]" value="${data.batch_barang ?? ''}" >
                                <input type="hidden" name="id_batch[]" value="${data.id_batch ?? ''}" >
                            </td>
                            <td>
                                <input type="number" 
                                    class="form-control jumlah-retur" 
                                    name="jumlah_retur[]" 
                                    min="1" 
                                    step="1" 
                                    value="${data.jumlah_retur ?? 0}" 
                                    required>
                                <input type="hidden" class="jumlah-ditolak" name="jumlah_ditolak[]" value="${data.jumlah_ditolak ?? 0}">
                            </td>
                            <td>
                                <span>${data.kode_satuan ?? ''}</span>
                                <input type="hidden" name="id_satuan[]" value="${data.id_satuan ?? ''}">
                                <input type="hidden"name="nilai_konversi[]"value="${data.nilai_konversi ?? ''}">
                            </td>
                            <td>
                                <span>${formattedDate || '-'}</span>
                                <input
                                    type="hidden"
                                    class="form-control"
                                    name="expired_date[]"
                                    value="${formattedDate}">
                            </td>
                            <td>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="deskripsi[]"
                                    placeholder="Jika ada"
                                    value="${data.deskripsi ?? ''}"
                                >
                            </td>
                            <td class="text-center align-middle">
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm hapus"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>

                        </tr>
                    `);
                row.find('select.satuan').val(data.id_satuan ?? '');
                $('#detailTableBody').append(row);
            }

            $('#select_penolakan').change(function() {

                const idPenolakan = $(this).val();
                $('#id_penolakan').val(idPenolakan);
                $('#id_penerimaan').val('');
                $('#detailTableBody').empty();

                if (!idPenolakan) {
                    return;
                }

                $('#detailTableBody').append('<tr id="loading-row">' +
                    '<td colspan="7" class="text-center py-4 text-muted">' +
                    'Sedang memuat ulang data bahan baku...' + '</td>' + '</tr>'
                );

                $.ajax({
                    url: `/retur/get-penolakan-details/${idPenolakan}`,
                    type: 'GET',
                    dataType: 'json',

                    success: function(response) {
                        $('#loading-row').remove();

                        if (!response || response.length === 0) {
                            $('#detailTableBody').append('<tr>' +
                                '<td colspan="7" class="text-center text-danger py-4">' +
                                'Dokumen penolakan ini tidak memiliki item bahan baku.' +
                                '</td>' + '</tr>');
                            return;
                        }

                        response.forEach(function(item) {
                            addRow({
                                id_detail_penolakan: item.id_detail_penolakan ??
                                    '',
                                id_detail_penerimaan: '',
                                id_batch: item.id_batch ?? '',
                                id_barang: item.id_barang ?? '',
                                nama_barang: item.nama_barang ?? '',
                                batch_barang: item.batch_barang ?? '',
                                jumlah_ditolak: item.jumlah_ditolak ?? 0,
                                jumlah_retur: item.jumlah_ditolak ?? '',
                                id_satuan: item.id_satuan ?? '',
                                nilai_konversi: item.nilai_konversi ?? '',
                                expired_date: item.expired_date ?? '',
                                deskripsi: item.deskripsi ?? ''
                            });
                        });
                    },

                    error: function(xhr) {
                        $('#loading-row').remove();
                        console.error('Penolakan error:', xhr.responseText);

                        alert('Gagal mengambil data bahan baku dari server.');
                    }
                });
            });

            // 5. Event Hapus Baris
            $(document).on('click', '.hapus', function() {
                $(this).closest('tr').remove();
            });

            // 6. Validasi Jumlah Retur
            $(document).on('input', '.jumlah-retur', function() {
                const row = $(this).closest('tr');
                const jumlahRetur = parseInt($(this).val()) || 0;
                const jumlahDitolak = parseInt(row.find('.jumlah-ditolak').val()) || 0;
                const jenisSumber = $('#jenis_sumber_retur').val();

                let maxRetur = jumlahDitolak;

                // Jika dari Penolakan Produksi, batas maksimal adalah jumlah ditolak dibagi nilai konversi
                if (jenisSumber === 'penolakan') {
                    const nilaiKonversi = parseInt(row.find('input[name="nilai_konversi[]"]').val()) || 1;
                    maxRetur = Math.floor(jumlahDitolak / nilaiKonversi);
                }

                // Validasi: Jumlah retur tidak boleh melebihi batas maksimal
                if (jumlahRetur > maxRetur) {
                    $('#error-empty-item').remove();
                    const errors = $(`
                        <div id="error-empty-item" class="alert alert-danger">
                            <strong>Pemberitahuan!</strong>
                            <ul class="mb-0 mt-2 pl-3">
                                <li>Batas maksimal jumlah retur adalah <strong>${maxRetur}</strong></li>
                            </ul>
                        </div>
                    `);
                    $('#formRetur').prepend(errors);
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                    $(this).val(maxRetur > 0 ? maxRetur : '');
                }
            });

            const oldDetailPenolakan = @json(old('id_detail_penolakan', []));
            const oldBatchId = @json(old('id_batch', []));
            const oldBarang = @json(old('id_barang', []));
            const oldNamaBarang = @json(old('nama_barang', []));
            const oldBatchBarang = @json(old('batch_barang', []));
            const oldSatuan = @json(old('id_satuan', []));
            const oldJumlahRetur = @json(old('jumlah_retur', []));
            const oldExpiredDate = @json(old('expired_date', []));
            const oldDeskripsi = @json(old('deskripsi', []));
            const oldDetailPenerimaan = @json(old('id_detail_penerimaan', []));
            const oldJumlahDitolak = @json(old('jumlah_ditolak', []));
            const oldNilaiKonversi = @json(old('nilai_konversi', []));
            const oldKodeSatuan = @json(old('kode_satuan', []));

            // Ambil data dari database yang dikirim oleh Controller
            const existingDatabaseItems = @json($detail_retur ?? []);

            if (oldDetailPenolakan.length > 0) {
                for (let i = 0; i < oldDetailPenolakan.length; i++) {
                    addRow({
                        id_detail_penolakan: oldDetailPenolakan[i] ?? '',
                        id_detail_penerimaan: oldDetailPenerimaan[i] ?? '',
                        id_batch: oldBatchId[i] ?? '',
                        id_barang: oldBarang[i] ?? '',
                        nama_barang: oldNamaBarang[i] ?? '',
                        batch_barang: oldBatchBarang[i] ?? '',
                        jumlah_ditolak: oldJumlahDitolak[i] ?? 0,
                        jumlah_retur: oldJumlahRetur[i] ?? '',
                        id_satuan: oldSatuan[i] ?? '',
                        kode_satuan: oldKodeSatuan[i] ?? '',
                        nilai_konversi: oldNilaiKonversi[i] ?? '',
                        expired_date: oldExpiredDate[i] ?? '',
                        deskripsi: oldDeskripsi[i] ?? ''
                    });
                }
            } else if (existingDatabaseItems.length > 0) {
                existingDatabaseItems.forEach(function(item) {
                    addRow({
                        id_detail_penolakan: item.id_detail_penolakan ?? '',
                        id_detail_penerimaan: item.id_detail_penerimaan ?? '',
                        id_batch: item.id_batch ?? '',
                        id_barang: item.id_barang ?? '',
                        nama_barang: item.nama_barang ?? '',
                        batch_barang: item.batch_barang ?? '',
                        jumlah_ditolak: item.jumlah_ditolak ?? 0,
                        jumlah_retur: item.jumlah_retur ?? '',
                        id_satuan: item.id_satuan ?? '',
                        kode_satuan: item.kode_satuan ?? '',
                        nilai_konversi: item.nilai_konversi ?? '',
                        expired_date: item.expired_date ?? '',
                        deskripsi: item.deskripsi ?? ''
                    });
                });
            } else if ($('#id_penolakan').val()) {
                // PRIORITAS 3: Fallback AJAX jika database kosong tapi dropdown penolakan terisi
                $('#id_penolakan').trigger('change');
            }

            // 7. Validasi Form saat Submit
            $('#formRetur').submit(function(e) {
                if ($('#detailTableBody tr').length == 0 || $('#detailTableBody tr#loading-row').length >
                    0) {
                    $('#error-empty-item').remove();
                    let $errors = $(
                        '<div id="error-empty-item" class="alert alert-danger"><strong>Data belum valid!</strong><ul class="mb-0 mt-2 pl-3"><li>Minimal satu bahan baku harus ada di dalam detail retur.</li></ul></div>'
                    );
                    $(this).prepend($errors);
                    return false;
                } else {
                    $('#error-empty-item').remove();
                }
            });
        });
    </script>
@endpush
@endsection
