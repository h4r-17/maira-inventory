@extends('layouts.master')
@section('title', '| Tambah Retur')
@section('konten')
@section('judul', 'Form Tambah Retur')
<form action="{{ route('retur.store') }}" method="POST" id="formRetur" enctype="multipart/form-data">
    @csrf
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
                            value="{{ old('no_retur', $kodeRetur) }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_retur" class="text-gray-900">Tanggal Retur</label>
                        <input type="date" class="form-control" name="tanggal_retur" id="tanggal_retur"
                            value="{{ old('tanggal_retur') }}" min="{{ date('Y-m-d') }}" required>
                        @error('tanggal_retur')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="jenis_sumber_retur" class="text-gray-900">
                            Sumber Retur
                        </label>

                        <select class="form-control" id="jenis_sumber_retur">
                            <option value="">-- Pilih Sumber Retur --</option>
                            <option value="penolakan" {{ old('id_penolakan') ? 'selected' : '' }}>
                                Penolakan Produksi
                            </option>
                            <option value="penerimaan" {{ old('id_penerimaan') ? 'selected' : '' }}>
                                Penolakan Penerimaan
                            </option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="id_penolakan" id="id_penolakan" value="{{ old('id_penolakan') }}">
                <input type="hidden" name="id_penerimaan" id="id_penerimaan" value="{{ old('id_penerimaan') }}">
                <div class="col-md-4" id="wrapperPenolakan" style="display: none;">
                    <div class="form-group">
                        <label for="select_penolakan" class="text-gray-900">
                            No Penolakan
                        </label>

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
                        <label for="no_registrasi" class="text-gray-900">
                            No Registrasi Penerimaan
                        </label>

                        <input type="text" class="form-control" id="no_registrasi"
                            placeholder="Masukkan no registrasi penerimaan" value="{{ old('no_registrasi') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="no_nota" class="text-gray-900">No Nota</label>
                        <!-- Input untuk mengetik dan memunculkan autocomplete -->
                        <input type="text" name="no_nota" id="no_nota" class="form-control"
                            placeholder="Masukkan no nota" value="{{ old('no_nota') }}" required>
                        <!-- Input hidden untuk menampung primary key id_pembelian -->
                        <input type="hidden" name="id_pembelian" id="id_pembelian" value="{{ old('id_pembelian') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id_supplier" class="text-gray-900">Supplier</label>
                        <!-- Input Text: Hanya untuk menampilkan Nama (User Friendly) -->
                        <input type="text" class="form-control" id="nama_supplier"
                            value="{{ old('nama_supplier') }}" readonly>
                        <input type="hidden" class="form-control" name="id_supplier" id="id_supplier"
                            value="{{ old('id_supplier') }}" readonly>
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
                {{-- <button type="button" class="btn btn-primary btn-sm" id="btnTambah"><i class="fas fa-plus"></i>
                    Tambah</button> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-gray-900" id="tabelDetail">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Bahan Baku</th>
                                <th>Kode Batch</th>
                                <th>Jumlah Ditolak</th>
                                <th>Jumlah Retur</th>
                                <th>Satuan</th>
                                <th>Nilai Konversi</th>
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
            function resetHeader() {
                $('#id_penolakan').val('');
                $('#id_penerimaan').val('');
                $('#id_pembelian').val('');
                $('#id_supplier').val('');
                $('#nama_supplier').val('');
                $('#no_nota').val('');
                $('#no_registrasi').val('');
            }

            function clearDetail() {
                $('#detailTableBody').empty();
            }

            function showLoading() {
                $('#detailTableBody').html(`
            <tr id="loading-row">
                <td colspan="9" class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin"></i>
                    Sedang memuat data bahan baku...
                </td>
            </tr>
        `);
            }

            function showEmpty(message) {
                $('#detailTableBody').html(`
            <tr id="empty-row">
                <td colspan="9" class="text-center text-danger py-4">
                    ${message}
                </td>
            </tr>
        `);
            }

            /*ADD ROW DETAIL*/
            function addRow(data = {}) {

                const row = $(`
            <tr>
                <td>
                    <input type="hidden"
                        class="id-detail-penolakan"
                        name="id_detail_penolakan[]"
                        value="${data.id_detail_penolakan ?? ''}">

                    <input type="hidden"
                        class="id-detail-penerimaan"
                        name="id_detail_penerimaan[]"
                        value="${data.id_detail_penerimaan ?? ''}">

                    <input type="hidden"
                        class="id-batch"
                        name="id_batch[]"
                        value="${data.id_batch ?? ''}">

                    <input type="hidden"
                        class="id-barang"
                        name="id_barang[]"
                        value="${data.id_barang ?? ''}">

                    <input type="text"
                        class="form-control"
                        value="${data.nama_barang ?? ''}"
                        readonly>
                </td>
                <td>
                    <input type="text"
                        class="form-control batch-barang"
                        value="${data.batch_barang ?? ''}"
                        readonly>
                </td>
                <td>
                    <input type="number"
                        class="form-control jumlah-ditolak"
                        value="${data.jumlah_ditolak ?? 0}"
                        readonly>
                    <input type="hidden"
                        name="jumlah_ditolak[]"
                        value="${data.jumlah_ditolak ?? 0}">
                </td>
                <td>
                    <input type="number"
                        class="form-control jumlah-retur"
                        name="jumlah_retur[]"
                        min="1"
                        step="1"
                        value="${data.jumlah_retur ?? ''}"
                        required>
                </td>
                <td>
                    <input type="text"
                        class="form-control"
                        value="${data.kode_satuan ?? ''}"
                        readonly>

                    <input type="hidden"
                        name="id_satuan[]"
                        value="${data.id_satuan ?? ''}">
                </td>
                <td>
                    <input type="text"
                        class="form-control nilai-konversi-display"
                        value="${data.nilai_konversi ?? ''}"
                        readonly>

                    <input type="hidden"
                        name="nilai_konversi[]"
                        value="${data.nilai_konversi ?? ''}">
                </td>
                <td>
                    <input type="text"
                        class="form-control"
                        value="${data.expired_date ?? ''}"
                        readonly>
                </td>
                <td>
                    <input type="text"
                        class="form-control"
                        name="deskripsi[]"
                        placeholder="Jika ada"
                        value="${data.deskripsi ?? ''}">
                </td>
                <td class="text-center align-middle">
                    <button type="button"
                        class="btn btn-danger btn-sm hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
                $('#detailTableBody').append(row);
            }


            /*SUMBER RETUR*/
            $('#jenis_sumber_retur').on('change', function() {
                const jenis = $(this).val();

                clearDetail();
                $('#wrapperPenolakan').hide();
                $('#wrapperPenerimaan').hide();

                resetHeader();

                /*PENOLAKAN PRODUKSI*/
                if (jenis === 'penolakan') {
                    $('#wrapperPenolakan').show();
                    $('#no_nota').prop('readonly', true);
                    return;
                }

                /*PENOLAKAN PENERIMAAN*/
                if (jenis === 'penerimaan') {
                    $('#wrapperPenerimaan').show();
                    $('#no_nota').prop('readonly', true);
                    return;
                }
                /*BELUM MEMILIH SUMBER*/
                $('#no_nota').prop('readonly', false);
            });

            /*AUTOCOMPLETE NO NOTA Digunakan hanya jika belum memilih sumber retur*/
            $('#no_nota').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('autocomplete-pembelian') }}",
                        type: "GET",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        },
                        error: function(xhr) {
                            console.error(
                                'Autocomplete nota error:',
                                xhr.responseText
                            );
                            response([]);
                        }
                    });
                },
                minLength: 1,

                select: function(event, ui) {
                    $('#no_nota').val(ui.item.value);
                    $('#id_pembelian').val(ui.item.id_pembelian ?? '');
                    $('#id_supplier').val(ui.item.id_supplier ?? '');
                    $('#nama_supplier').val(ui.item.nama_supplier ?? '');

                    return false;
                }

            });

            /*AUTOCOMPLETE NO REGISTRASI PENERIMAAN*/
            $('#no_registrasi').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('autocomplete-penerimaan-retur') }}",
                        type: "GET",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        },
                        error: function(xhr) {
                            console.error(
                                'Autocomplete penerimaan error:',
                                xhr.responseText
                            );
                            response([]);
                        }
                    });
                },
                minLength: 1,

                select: function(event, ui) {
                    event.preventDefault();
                    $('#no_registrasi').val(ui.item.value);
                    $('#id_penerimaan').val(ui.item.id_penerimaan);
                    $('#id_penolakan').val('');
                    $('#id_pembelian').val(ui.item.id_pembelian);
                    $('#no_nota').val(ui.item.no_nota);
                    $('#id_supplier').val(ui.item.id_supplier);
                    $('#nama_supplier').val(ui.item.supplier);
                    loadPenerimaanDetails(
                        ui.item.id_penerimaan
                    );

                    return false;
                }
            });

            /*CLEAR NO REGISTRASI*/
            $('#no_registrasi').on('input', function() {
                $('#id_penerimaan').val('');
                $('#id_pembelian').val('');
                $('#id_supplier').val('');
                $('#nama_supplier').val('');
                $('#no_nota').val('');
                clearDetail();
            });

            /*PENOLAKAN PRODUKSI*/
            $('#select_penolakan').on('change', function() {
                const idPenolakan = $(this).val();
                clearDetail();
                $('#id_penolakan').val(idPenolakan);
                $('#id_penerimaan').val('');

                if (!idPenolakan) {
                    $('#id_pembelian').val('');
                    $('#id_supplier').val('');
                    $('#nama_supplier').val('');
                    $('#no_nota').val('');
                    return;
                }

                loadPenolakanDetails(idPenolakan);
            });

            /*DETAIL PENERIMAAN*/
            function loadPenerimaanDetails(idPenerimaan) {
                clearDetail();
                showLoading();

                $.ajax({
                    url: `/retur/get-penerimaan-details/${idPenerimaan}`,
                    type: 'GET',
                    dataType: 'json',

                    success: function(response) {
                        $('#loading-row').remove();
                        if (!response || response.length === 0) {
                            showEmpty(
                                'Penerimaan ini tidak memiliki bahan yang ditolak.'
                            );
                            return;
                        }

                        response.forEach(function(item) {
                            addRow({
                                id_detail_penolakan: '',
                                id_detail_penerimaan: item.id_detail_penerimaan,
                                /*Penerimaan ditolak sebelum masuk batch*/
                                id_batch: item.id_batch ?? '',
                                id_barang: item.id_barang,
                                nama_barang: item.nama_barang,
                                batch_barang: item.batch_barang ?? '',
                                jumlah_ditolak: item.jumlah_ditolak ?? 0,
                                id_satuan: item.id_satuan,
                                kode_satuan: item.kode_satuan ?? '',
                                nilai_konversi: item.nilai_konversi ?? '',
                                expired_date: item.expired_date ?? '',
                                deskripsi: item.deskripsi ?? '',
                                jumlah_retur: item.jumlah_ditolak ?? 0
                            });
                        });
                    },

                    error: function(xhr) {
                        $('#loading-row').remove();

                        console.error(
                            'Penerimaan error:',
                            xhr.responseText
                        );

                        showEmpty(
                            'Gagal mengambil data penerimaan dari server.'
                        );
                    }
                });
            }

            // detail penolakan
            function loadPenolakanDetails(idPenolakan) {
                clearDetail();
                showLoading();

                $.ajax({
                    url: `/retur/get-penolakan-details/${idPenolakan}`,
                    type: 'GET',
                    dataType: 'json',

                    success: function(response) {
                        $('#loading-row').remove();

                        if (!response || response.length === 0) {
                            showEmpty('Penolakan ini tidak memiliki detail yang dapat diretur.');
                            return;
                        }

                        // data pembelian dan supplier diambil dari detail penolakan pertama
                        const header = response[0];

                        $('#no_nota').val(header.no_nota ?? '');
                        $('#id_pembelian').val(header.id_pembelian ?? '');
                        $('#id_supplier').val(header.id_supplier ?? '');
                        $('#nama_supplier').val(header.nama_supplier ?? '');

                        response.forEach(function(item) {
                            addRow({
                                // Sumber retur = penolakan
                                id_detail_penolakan: item.id_detail_penolakan ?? '',
                                // Tidak berasal dari penerimaan langsung
                                id_detail_penerimaan: '',
                                id_batch: item.id_batch ?? '',
                                id_barang: item.id_barang ?? '',
                                nama_barang: item.nama_barang ?? '',
                                batch_barang: item.batch_barang ?? '',
                                jumlah_ditolak: item.jumlah_ditolak ?? 0,
                                // Jumlah retur dihitung dari jumlah ditolak dibagi nilai konversi
                                jumlah_retur: (item.jumlah_ditolak && item
                                    .nilai_konversi) ? (item.jumlah_ditolak / item
                                    .nilai_konversi) : (item.jumlah_ditolak ?? 0),
                                id_satuan: item.id_satuan ?? '',
                                kode_satuan: item.kode_satuan ?? '',
                                nilai_konversi: item.nilai_konversi ?? '',
                                expired_date: item.expired_date ?? '',
                                deskripsi: item.deskripsi ?? ''
                            });
                        });
                    },

                    error: function(xhr) {
                        $('#loading-row').remove();

                        console.error('Penolakan error:', xhr.responseText);
                        showEmpty('Gagal mengambil data penolakan dari server.');
                    }
                });
            }

            /*HAPUS BARIS*/
            $(document).on('click', '.hapus', function() {
                $(this)
                    .closest('tr')
                    .remove();
            });

            /*VALIDASI JUMLAH RETUR*/
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

            /*OLD INPUT*/
            const oldDetailPenolakan = @json(old('id_detail_penolakan', []));
            const oldDetailPenerimaan = @json(old('id_detail_penerimaan', []));
            const oldBatchId = @json(old('id_batch', []));
            const oldBarang = @json(old('id_barang', []));
            const oldNamaBarang = @json(old('nama_barang', []));
            const oldBatchBarang = @json(old('batch_barang', []));
            const oldSatuan = @json(old('id_satuan', []));
            const oldJumlahDitolak = @json(old('jumlah_ditolak', []));
            const oldJumlahRetur = @json(old('jumlah_retur', []));
            const oldNilaiKonversi = @json(old('nilai_konversi', []));
            const oldExpiredDate = @json(old('expired_date', []));
            const oldDeskripsi = @json(old('deskripsi', []));

            /*RESTORE OLD INPUT*/
            if (oldBarang.length > 0) {
                for (let i = 0; i < oldBarang.length; i++) {
                    addRow({
                        id_detail_penolakan: oldDetailPenolakan[i] ?? '',
                        id_detail_penerimaan: oldDetailPenerimaan[i] ?? '',
                        id_batch: oldBatchId[i] ?? '',
                        id_barang: oldBarang[i] ?? '',
                        nama_barang: oldNamaBarang[i] ?? '',
                        batch_barang: oldBatchBarang[i] ?? '-',
                        jumlah_ditolak: oldJumlahDitolak[i] ?? 0,
                        id_satuan: oldSatuan[i] ?? '',
                        nilai_konversi: oldNilaiKonversi[i] ?? '',
                        jumlah_retur: oldJumlahRetur[i] ?? '',
                        expired_date: oldExpiredDate[i] ?? '-',
                        deskripsi: oldDeskripsi[i] ?? ''
                    });
                }
            }

            /*SUBMIT FORM*/
            $('#formRetur').on('submit', function(event) {
                const detailRows = $('#detailTableBody tr').has('input[name="id_barang[]"]').length;
                const loading = $('#detailTableBody #loading-row').length > 0;
                /*Harus memiliki detail*/

                if (detailRows === 0 || loading) {
                    event.preventDefault();
                    $('#error-empty-item').remove();
                    const errors = $(`
                <div id="error-empty-item"
                    class="alert alert-danger">
                    <strong>Data belum valid!</strong>
                    <ul class="mb-0 mt-2 pl-3">
                        <li>
                            Minimal satu bahan baku harus ada
                            di dalam detail retur.
                        </li>
                    </ul>
                </div>
            `);

                    $('#formRetur').prepend(errors);
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                    return false;
                }

                /*Harus memiliki sumber retur*/
                const jenisSumber = $('#jenis_sumber_retur').val();
                if (!jenisSumber) {
                    event.preventDefault();
                    alert('Silakan pilih sumber retur terlebih dahulu.');
                    $('#jenis_sumber_retur').focus();
                    return false;
                }

                /*Jika penolakan produksi*/
                if (jenisSumber === 'penolakan' && !$('#id_penolakan').val()) {
                    event.preventDefault();
                    alert('Silakan pilih nomor penolakan terlebih dahulu.');

                    return false;
                }

                /*Jika penolakan penerimaan*/
                if (jenisSumber === 'penerimaan' && !$('#id_penerimaan').val()) {
                    event.preventDefault();
                    alert(
                        'Silakan pilih nomor registrasi penerimaan terlebih dahulu.'
                    );

                    return false;
                }
            });

            /*INITIAL STATE*/
            const oldJenisSumber =
                "{{ old('id_penolakan') ? 'penolakan' : (old('id_penerimaan') ? 'penerimaan' : '') }}";

            if (oldJenisSumber) {
                $('#jenis_sumber_retur')
                    .val(oldJenisSumber)
                    .trigger('change');
            }
        });
    </script>
@endpush
@endsection
