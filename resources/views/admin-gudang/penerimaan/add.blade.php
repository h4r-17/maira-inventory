@extends('layouts.master')

@section('title', '| Tambah Penerimaan')
@section('konten')
@section('judul', 'Form Tambah Penerimaan')
<form action="{{ route('penerimaan.store') }}" method="post" id="formPenerimaan">
    @csrf
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pembelian</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="no_registrasi" class="text-gray-900">No Registrasi</label>
                        <input type="text" class="form-control" id="no_registrasi" name="no_registrasi"
                            value="{{ old('no_registrasi', $kodeRegis) }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_masuk" class="text-gray-900">Tanggal Masuk</label>
                        <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk"
                            value="{{ old('tanggal_masuk') }}" min="{{ date('Y-m-d') }}" required>
                        @error('tanggal_masuk')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="jenis_penerimaan" class="text-gray-900">Jenis Penerimaan</label>
                        <select class="form-control" id="jenis_penerimaan" name="jenis_penerimaan" required>
                            <option value="">-- Pilih Jenis Penerimaan --</option>
                            <option value="Pembelian" {{ old('jenis_penerimaan') == 'Pembelian' ? 'selected' : '' }}>
                                Pembelian
                            </option>
                            <option value="Retur" {{ old('jenis_penerimaan') == 'Retur' ? 'selected' : '' }}>Retur
                            </option>
                        </select>
                        @error('jenis_penerimaan')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4" id="wrapper_retur" style="display: none;">
                    <div class="form-group">
                        <label for="id_retur" class="text-gray-900">No Retur (Jika jenis penerimaan adalah
                            retur)</label>
                        <select class="form-control" name="id_retur" id="id_retur">
                            <option value="">-- Pilih No Retur --</option>
                            @foreach ($data_retur as $retur)
                                <option value="{{ $retur->id_retur }}"
                                    {{ old('id_retur') == $retur->id_retur ? 'selected' : '' }}>
                                    {{ $retur->no_retur }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_retur')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="no_nota" class="text-gray-900">No Nota</label>
                        <input type="text" name="no_nota" id="no_nota" class="form-control"
                            placeholder="Nomor Nota" value="{{ old('no_nota') }}" required>
                        <input type="hidden" name="id_pembelian" id="id_pembelian" value="{{ old('id_pembelian') }}">
                        @error('id_pembelian')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="no_faktur" class="text-gray-900">No Faktur</label>
                        <input type="text" class="form-control" id="no_faktur" name="no_faktur"
                            value="{{ old('no_faktur') }}">
                        @error('no_faktur')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="surat_jalan" class="text-gray-900">No Surat Jalan</label>
                        <input type="text" class="form-control" id="surat_jalan" name="surat_jalan"
                            value="{{ old('surat_jalan') }}">
                        @error('surat_jalan')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id_supplier" class="text-gray-900">Supplier</label>
                        <input type="text" class="form-control" id="nama_supplier" name="nama_supplier"
                            value="{{ old('nama_supplier') }}" readonly placeholder="Pilih No Nota terlebih dahulu">
                        <input type="hidden" id="id_supplier" name="id_supplier" value="{{ old('id_supplier') }}">
                        @error('id_supplier')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-5">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Detail Penerimaan</h6>
            {{-- <button type="button" class="btn btn-primary btn-sm" id="btnTambah"><i class="fas fa-plus"></i>
                Tambah</button> --}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-gray-900" id="tabelDetail">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 10%;">Nama Bahan Baku</th>
                            <th style="width: 13%;">Kode Batch</th>
                            <th style="width: 10%;">Jumlah Masuk</th>
                            <th style="width: 10%;">Jumlah Ditolak</th>
                            <th style="width: 5%;" class="text-center">Satuan</th>
                            <th style="width: 13%;">Tanggal Kadaluarsa</th>
                            <th style="width: 15%;">Alasan Penolakan</th>
                            <th style="width: 15%;">Deskripsi</th>
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
            <a href="{{ route('penerimaan.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </div>
</form>
@push('scripts')
    <script>
        $(document).ready(function() {
            function escapeHtml(value) {
                return $('<div>').text(value ?? '').html();
            }

            function handleJenisPenerimaan() {
                let jenis = $('#jenis_penerimaan').val();

                if (jenis === 'Retur') {
                    $('#wrapper_retur').show();
                    $('#no_nota').prop('readonly', true).attr('placeholder', 'Otomatis terisi dari No Retur');
                } else {
                    $('#wrapper_retur').hide();
                    $('#no_nota').prop('readonly', false).attr('placeholder', 'Nomor Nota');
                    $('#id_retur').val('').trigger('change');
                }
            }

            handleJenisPenerimaan();

            $('#jenis_penerimaan').change(function() {
                handleJenisPenerimaan();
            });

            function fillFromPembelian(item) {
                $('#id_pembelian').val(item.id);
                $('#id_supplier').val(item.id_supplier ?? '');
                $("#nama_supplier").val(item.supplier ?? '');
                $('#detailTableBody').empty();

                (item.details ?? []).forEach(function(detail) {

                    // Jika sudah tidak ada sisa, jangan masukkan ke tabel
                    if (Number(detail.jumlah_sisa) <= 0) {
                        return;
                    }

                    addRow({
                        id_barang: detail.id_barang ?? '',
                        nama_barang: detail.nama_barang ?? '',
                        // Default jumlah masuk = jumlah yang masih tersedia
                        jumlah_masuk: detail.jumlah_sisa ?? '',
                        jumlah_ditolak: '',
                        alasan_penolakan: '',
                        id_satuan: detail.id_satuan ?? '',
                        kode_satuan: detail.kode_satuan ?? '',
                        rasio_konversi: detail.rasio_konversi ?? '',
                        kode_lot_supplier: '',
                        expired_date: '',
                        deskripsi: detail.deskripsi ?? '',
                        // Data untuk batas validasi frontend
                        jumlah_sisa: detail.jumlah_sisa ?? 0,
                    });
                });
            }

            $('#no_nota').autocomplete({
                source: '{{ route('autocomplete-nota') }}',
                minLength: 1,
                select: function(event, ui) {
                    $(this).val(ui.item.value);
                    fillFromPembelian(ui.item);
                    return false;
                }
            }).on('input', function() {
                $('#id_pembelian').val('');
            });

            function bindAutoComplete(row) {
                row.find('.barang').autocomplete({
                    source: '{{ route('autocomplete-barang') }}',
                    minLength: 1,
                    select: function(event, ui) {
                        $(this).val(ui.item.label);
                        row.find('.id_barang').val(ui.item.id);
                        row.find('.nama_barang').val(ui.item.label);
                        row.find('.id_satuan').val(ui.item.id_satuan ?? '');
                        row.find('.label-satuan').text(ui.item.kode_satuan ?? '-');
                        return false;
                    }
                }).on('input', function() {
                    row.find('.id_barang').val('');
                    row.find('.nama_barang').val($(this).val());
                    row.find('.id_satuan').val('');
                    row.find('.label-satuan').text('-');
                });
            }

            // Event ketika No Retur dipilih dari dropdown
            $('#id_retur').change(function() {
                let idRetur = $(this).val();

                if (idRetur) {
                    let url = "{{ route('get-detail-retur', ':id') }}".replace(':id', idRetur);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        beforeSend: function() {
                            $('#detailTableBody').empty();
                        },
                        success: function(response) {
                            if (response && response.status === 'success') {
                                $('#id_pembelian').val(response.id_pembelian ?? '');
                                $('#no_nota').val(response.no_nota ?? '');

                                if (response.nama_supplier) {
                                    $('#nama_supplier').val(response.nama_supplier);
                                    $('input[type="hidden"]#id_supplier').val(response
                                        .id_supplier);
                                }

                                if (response.details) {
                                    response.details.forEach(function(detail) {
                                        addRow({
                                            id_barang: detail.id_barang ?? '',
                                            nama_barang: detail.nama_barang ??
                                                '',
                                            jumlah_masuk: detail.jumlah_retur ??
                                                '',
                                            id_satuan: detail.id_satuan ?? '',
                                            kode_satuan: detail.kode_satuan ??
                                                '',
                                            rasio_konversi: detail
                                                .rasio_konversi ?? '',
                                            kode_lot_supplier: '',
                                            expired_date: '',
                                            deskripsi: detail.deskripsi ?? ''
                                        });
                                    });
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('Gagal mengambil data detail retur:', xhr
                                .responseText);
                        }
                    });
                } else {
                    $('#detailTableBody').empty();
                    $('#id_pembelian').val('');
                    $('#no_nota').val('');
                    $('#nama_supplier').val('');
                    $('input[type="hidden"]#id_supplier').val('');
                }
            });

            // Fungsi menambah baris baru ke dalam tabel (Bagian yang terpotong telah dilengkapi)
            function addRow(data = {}) {
                let row = $(`
                        <tr>
                            <td>
                                <span>${escapeHtml(data.nama_barang ?? '')}</span>

                                <input type="hidden"
                                    class="nama_barang"
                                    name="nama_barang[]"
                                    value="${escapeHtml(data.nama_barang ?? '')}">

                                <input type="hidden"
                                    class="id_barang"
                                    name="id_barang[]"
                                    value="${escapeHtml(data.id_barang ?? '')}">
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control"
                                    name="kode_lot_supplier[]"
                                    placeholder="Masukkan kode batch"
                                    value="${escapeHtml(data.kode_lot_supplier ?? '')}">
                            </td>

                            <td>
                                <input type="number"
                                    class="form-control jumlah-masuk"
                                    min="0"
                                    step="1"
                                    name="jumlah_masuk[]"
                                    placeholder="Masukkan jumlah"
                                    required
                                    value="${escapeHtml(data.jumlah_masuk ?? '')}"
                                    data-jumlah-sisa="${data.jumlah_sisa ?? 0}">
                            </td>

                            <td>
                                <input type="number"
                                    class="form-control jumlah-ditolak"
                                    min="0"
                                    step="1"
                                    name="jumlah_ditolak[]"
                                    placeholder="Jika ada"
                                    value="${escapeHtml(data.jumlah_ditolak ?? '')}">
                            </td>

                            <td class="text-center">
                                <span class="label-satuan">
                                    ${escapeHtml(data.kode_satuan ?? '') || '-'}
                                </span>

                                <input type="hidden"
                                    class="id_satuan"
                                    name="id_satuan[]"
                                    value="${escapeHtml(data.id_satuan ?? '')}">

                                <input type="hidden"
                                    class="kode_satuan"
                                    name="kode_satuan[]"
                                    value="${escapeHtml(data.kode_satuan ?? '')}">

                                <input type="hidden"
                                    class="rasio_konversi"
                                    name="rasio_konversi[]"
                                    value="${escapeHtml(data.rasio_konversi ?? '')}">
                            </td>

                            <td>
                                <input type="date"
                                    class="form-control"
                                    name="expired_date[]"
                                    min="{{ date('Y-m-d', strtotime('+4 months')) }}"
                                    ${Number(data.jumlah_masuk ?? 0) > 0 ? 'required' : ''}
                                    value="${escapeHtml(data.expired_date ?? '')}">
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control alasan-penolakan"
                                    name="alasan_penolakan[]"
                                    placeholder="Jika ada"
                                    value="${escapeHtml(data.alasan_penolakan ?? '')}">
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control"
                                    name="deskripsi[]"
                                    placeholder="Jika ada"
                                    value="${escapeHtml(data.deskripsi ?? '')}">
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
                updateRowValidation(row);
            }

            // Fungsi untuk memperbarui validasi pada baris tertentu
            function updateRowValidation(row) {
                const jumlahMasuk = row.find('.jumlah-masuk');
                const jumlahDitolak = row.find('.jumlah-ditolak');
                const alasan = row.find('.alasan-penolakan');
                const expired = row.find('input[name="expired_date[]"]');

                function validate() {
                    const masuk = Number(jumlahMasuk.val()) || 0;
                    const ditolak = Number(jumlahDitolak.val()) || 0;
                    const jumlahSisa = Number(jumlahMasuk.attr('data-jumlah-sisa')) || 0;
                    const total = masuk + ditolak;

                    /*Jika ada penolakan, alasan wajib.*/
                    if (ditolak > 0) {
                        alasan.prop('required', true);
                    } else {
                        alasan.prop('required', false);
                        alasan.removeClass('is-invalid');
                    }

                    /*Jika ada barang masuk,expired date wajib.*/
                    if (masuk > 0) {
                        expired.prop('required', true);
                    } else {
                        expired.prop('required', false);
                        expired.removeClass('is-invalid');
                    }
                }
                jumlahMasuk.on('input', validate);
                jumlahDitolak.on('input', validate);

                validate();
            }

            // Event tombol tambah baris baru
            $('#btnTambah').click(function() {
                addRow();
            });

            // Event tombol hapus baris
            $(document).on('click', '.hapus', function() {
                $(this).closest('tr').remove();
            });

            // Sinkronisasi data lama dari Laravel Old Input
            const oldBarang = @json(old('id_barang', []));
            const oldNamaBarang = @json(old('nama_barang', []));
            const oldKodeLot = @json(old('kode_lot_supplier', []));
            const oldJumlahMasuk = @json(old('jumlah_masuk', []));
            const oldJumlahDitolak = @json(old('jumlah_ditolak', []));
            const oldAlasanPenolakan = @json(old('alasan_penolakan', []));
            const oldSatuan = @json(old('id_satuan', []));
            const oldKodeSatuan = @json(old('kode_satuan', []));
            const oldRasioKonversi = @json(old('rasio_konversi', []));
            const oldExpiredDate = @json(old('expired_date', []));
            const oldDeskripsi = @json(old('deskripsi', []));

            if (oldBarang.length > 0) {
                for (let i = 0; i < oldBarang.length; i++) {
                    addRow({
                        id_barang: oldBarang[i],
                        nama_barang: oldNamaBarang[i] ?? '',
                        kode_lot_supplier: oldKodeLot[i] ?? '',
                        jumlah_masuk: oldJumlahMasuk[i] ?? '',
                        jumlah_ditolak: oldJumlahDitolak[i] ?? '',
                        alasan_penolakan: oldAlasanPenolakan[i] ?? '',
                        id_satuan: oldSatuan[i] ?? '',
                        kode_satuan: oldKodeSatuan[i] ?? '',
                        rasio_konversi: oldRasioKonversi[i] ?? '',
                        expired_date: oldExpiredDate[i] ?? '',
                        deskripsi: oldDeskripsi[i] ?? ''
                    });
                }
            }
        });
    </script>
@endpush
@endsection
