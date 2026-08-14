@extends('layouts.master')

@section('title', '| Edit Penerimaan')
@section('konten')
@section('judul', 'Form Edit Penerimaan')
<form action="{{ route('penerimaan.update', $penerimaan->id_penerimaan) }}" method="post" id="formPenerimaan">
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
                        <label for="no_registrasi" class="text-gray-900">No Registrasi</label>
                        <input type="text" class="form-control" id="no_registrasi" name="no_registrasi"
                            value="{{ old('no_registrasi', $penerimaan->no_registrasi) }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_masuk" class="text-gray-900">Tanggal Masuk</label>
                        <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk"
                            value="{{ old('tanggal_masuk', $penerimaan->tanggal_masuk) }}" readonly>
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
                            <option value="Pembelian"
                                {{ old('jenis_penerimaan', $penerimaan->jenis_penerimaan) == 'Pembelian' ? 'selected' : '' }}>
                                Pembelian
                            </option>
                            <option value="Retur"
                                {{ old('jenis_penerimaan', $penerimaan->jenis_penerimaan) == 'Retur' ? 'selected' : '' }}>
                                Retur
                            </option>
                        </select>
                        @error('jenis_penerimaan')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="no_nota" class="text-gray-900">No Nota</label>
                        <input type="text" name="no_nota" id="no_nota" class="form-control"
                            placeholder="Nomor Nota"
                            value="{{ old('no_nota', optional($penerimaan->pembelian)->no_nota) }}" readonly>
                        <input type="hidden" name="id_pembelian" id="id_pembelian"
                            value="{{ old('id_pembelian', optional($penerimaan->pembelian)->id_pembelian) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="no_faktur" class="text-gray-900">No Faktur</label>
                        <input type="text" class="form-control" id="no_faktur" name="no_faktur"
                            value="{{ old('no_faktur', $penerimaan->no_faktur) }}">
                        @error('no_faktur')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="surat_jalan" class="text-gray-900">No Surat Jalan</label>
                        <input type="text" class="form-control" id="surat_jalan" name="surat_jalan"
                            value="{{ old('surat_jalan', $penerimaan->surat_jalan) }}">
                        @error('surat_jalan')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nama_supplier" class="text-gray-900">Supplier</label>
                        <input type="text" class="form-control" id="nama_supplier" name="nama_supplier"
                            value="{{ old('nama_supplier', $penerimaan->pembelian->supplier->nama_supplier ?? '') }}"
                            readonly placeholder="Pilih No Nota terlebih dahulu">
                        <input type="hidden" id="id_supplier" name="id_supplier"
                            value="{{ old('id_supplier', $penerimaan->pembelian->id_supplier ?? '') }}">
                        @error('id_supplier')
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
                                    {{ old('id_retur', optional($penerimaan->retur)->id_retur) == $retur->id_retur ? 'selected' : '' }}>
                                    {{ $retur->no_retur }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_retur')
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
    @php
        $mappedDetails = $penerimaan->detailPenerimaan
            ->map(function ($detail) use ($penerimaan) {
                $idBarang = $detail->batch?->id_barang;
                $detailPembelian = $penerimaan->pembelian?->detailPembelian->firstWhere('id_barang', $idBarang);
                $idSatuan = $detailPembelian?->satuan?->id_satuan;
                $kodeSatuan = $detailPembelian?->satuan?->kode_satuan;

                return [
                    'id_detail_penerimaan' => $detail->id_detail_penerimaan,
                    'id_barang' => $idBarang ?? '',
                    'nama_barang' => $detail->batch?->barang?->nama_barang ?? '',
                    'id_batch' => $detail->id_batch,
                    'kode_lot_supplier' => $detail->batch?->kode_lot_supplier ?? '',
                    'jumlah_masuk' => $detail->jumlah_masuk,
                    'jumlah_ditolak' => $detail->jumlah_ditolak ?? 0,
                    'id_satuan' => $idSatuan ?? '',
                    'kode_satuan' => $kodeSatuan ?? '',
                    'rasio_konversi' => $detail->rasio_konversi,
                    'expired_date' => $detail->batch?->expired_date ?? '',
                    'alasan_penolakan' => $detail->alasan_penolakan ?? '',
                    'deskripsi' => $detail->deskripsi ?? '',
                ];
            })
            ->values()
            ->toArray();
    @endphp
    <script>
        const existingDetails = @json($mappedDetails);
        $(document).ready(function() {
            const oldDetailId = @json(old('id_detail_penerimaan', []));
            const oldBarang = @json(old('id_barang', []));

            function escapeHtml(value) {
                return $('<div>').text(value ?? '').html();
            }

            $(document).ready(function() {

                // Fungsi untuk mengatur visibilitas Form No Retur
                function handleJenisPenerimaan() {
                    let jenis = $('#jenis_penerimaan').val();

                    if (jenis === 'Retur') {
                        $('#wrapper_retur').show();
                    } else {
                        $('#wrapper_retur').hide();

                        // Opsional: Bersihkan isi input No Retur & reset detail tabel jika user mengubah pilihan
                        if ($('#id_retur').val() !== '') {
                            $('#id_retur').val('').trigger('change');
                        }
                    }
                }

                // Jalankan fungsi saat halaman pertama dimuat. Berfungsi menjaga posisi form jika halaman di-reload karena error validasi
                handleJenisPenerimaan();

                // Trigger fungsi setiap kali Dropdown Jenis Penerimaan diubah oleh user
                $('#jenis_penerimaan').change(function() {
                    handleJenisPenerimaan();
                });
            });


            function bindAutocomplete(row) {
                row.find('.barang').autocomplete({
                    source: '{{ route('autocomplete-barang') }}',
                    minLength: 1,
                    select: function(event, ui) {
                        $(this).val(ui.item.label);
                        row.find('.id_barang').val(ui.item.id);
                        row.find('.label-satuan').text(ui.item.kode_satuan ?? '-');
                        row.find('.id_satuan').val(ui.item.id_satuan ?? '');
                        row.find('.kode_satuan').val(ui.item.kode_satuan ?? '');
                        return false;
                    }
                }).on('input', function() {
                    row.find('.id_barang').val('');
                    row.find('.label-satuan').text('-');
                    row.find('.id_satuan').val('');
                    row.find('.kode_satuan').val('');
                });
            }

            function createRow(data = {}) {

                const row = $(`
                        <tr>
                            <td>
                                <input type="hidden"
                                    name="id_detail_penerimaan[]"
                                    value="${escapeHtml(data.id_detail_penerimaan)}">
                                <input type="text"
                                class="form-control nama-barang"
                                value="${escapeHtml(data.nama_barang ?? '')}"
                                readonly>

                            <input type="hidden"
                                name="id_barang[]"
                                value="${escapeHtml(data.id_barang ?? '')}">

                            <input type="hidden"
                                name="nama_barang[]"
                                value="${escapeHtml(data.nama_barang ?? '')}">
                            </td>
                            <td>
                                <input type="text"
                                    class="form-control"
                                    name="kode_lot_supplier[]"
                                    placeholder="Masukkan kode batch"
                                    value="${escapeHtml(data.kode_lot_supplier)}">
                            </td>
                            <td>
                                <input type="number"
                                    class="form-control text-right jumlah-masuk"
                                    min="0"
                                    step="1"
                                    name="jumlah_masuk[]"
                                    placeholder="Masukkan jumlah"
                                    required
                                    value="${escapeHtml(data.jumlah_masuk)}">
                            </td>
                            <td>
                                <input type="number"
                                    class="form-control text-right jumlah-ditolak"
                                    min="0"
                                    step="1"
                                    name="jumlah_ditolak[]"
                                    placeholder="Jika ada"
                                    value="${escapeHtml(data.jumlah_ditolak)}">
                            </td>
                            <td class="text-center">
                                <span class="label-satuan">
                                    ${escapeHtml(data.kode_satuan) || '-'}
                                </span>
                                <input type="hidden"
                                    class="id_satuan"
                                    name="id_satuan[]"
                                    value="${escapeHtml(data.id_satuan)}">

                                <input type="hidden"
                                    class="kode_satuan"
                                    name="kode_satuan[]"
                                    value="${escapeHtml(data.kode_satuan)}">

                                <input type="hidden"
                                    class="rasio_konversi"
                                    name="rasio_konversi[]"
                                    value="${escapeHtml(data.rasio_konversi)}">
                            </td>
                            <td>
                                <input type="date"
                                    class="form-control"
                                    name="expired_date[]"
                                    min="{{ date('Y-m-d', strtotime('+1 year')) }}"
                                    value="${escapeHtml(data.expired_date)}">
                            </td>
                            <td>
                                <input type="text"
                                    class="form-control alasan-penolakan"
                                    name="alasan_penolakan[]"
                                    placeholder="Jika ada"
                                    value="${escapeHtml(data.alasan_penolakan)}">
                            </td>
                            <td>
                                <input type="text"
                                    class="form-control"
                                    name="deskripsi[]"
                                    placeholder="Jika ada"
                                    value="${escapeHtml(data.deskripsi)}">
                            </td>
                            <td class="text-center">
                                <button type="button"
                                    class="btn btn-danger btn-sm hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                bindAutocomplete(row);
                $('#detailTableBody').append(row);
            }

            $('#btnTambah').click(function() {
                createRow();
            });

            $(document).on('click', '.hapus', function() {
                $(this).closest('tr').remove();
            });

            if (oldBarang && oldBarang.length > 0) {
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

                for (let i = 0; i < oldBarang.length; i++) {
                    createRow({
                        id_detail_penerimaan: oldDetailId[i] ?? '',
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
            } else {
                console.log("Data Lama dari Database:", existingDetails);
                existingDetails.forEach(function(item) {
                    createRow({
                        id_detail_penerimaan: item.id_detail_penerimaan ?? '',
                        id_barang: item.id_barang ?? '',
                        nama_barang: item.nama_barang ?? '',
                        kode_lot_supplier: item.kode_lot_supplier ?? '',
                        jumlah_masuk: item.jumlah_masuk ?? '',
                        jumlah_ditolak: item.jumlah_ditolak ?? '',
                        alasan_penolakan: item.alasan_penolakan ?? '',
                        id_satuan: item.id_satuan ?? '',
                        kode_satuan: item.kode_satuan ?? '-',
                        rasio_konversi: item.rasio_konversi ?? '',
                        expired_date: item.expired_date ?? '',
                        deskripsi: item.deskripsi ?? ''
                    });
                });
            }

            const jumlahMasuk = row.find('.jumlah-masuk');
            const expired = row.find('input[name="expired_date[]"]');

            function updateExpiredRequired() {
                const masuk = Number(jumlahMasuk.val()) || 0;
                expired.prop('required', masuk > 0);
            }

            jumlahMasuk.on('input', updateExpiredRequired);
            updateExpiredRequired();

            $('#formPenerimaan').submit(function(e) {
                if ($('#detailTableBody tr').length == 0) {
                    $('#error-empty-item').remove();
                    let $errors = $(
                        '<div id="error-empty-item" class="alert alert-danger"><strong>Data belum valid!</strong><ul class="mb-0 mt-2 pl-3"><li>Minimal satu barang harus ditambahkan.</li></ul></div>'
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
