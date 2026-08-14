@extends('layouts.master')
@section('title', '| Tambah Konversi Bahan Baku')
@section('konten')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Konversi Bahan Baku</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('konversi-barang.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="nama_barang" class="text-gray-900">Nama Bahan Baku</label>
                            <!-- Input untuk mengetik dan memunculkan autocomplete -->
                            <input type="text" name="nama_barang" id="nama_barang" class="form-control w-25"
                                placeholder="Nama Bahan Baku" value="{{ old('nama_barang') }}" required autocomplete="off">
                            <!-- Input hidden untuk menampung primary key id_pengajuan -->
                            <input type="hidden" name="id_barang" id="id_barang" value="{{ old('id_barang') }}">
                        </div>
                        <div class="form-group">
                            <label for="id_satuan" class="text-gray-900">Satuan</label>
                            <select class="form-control w-25" id="id_satuan" name="id_satuan">
                                <option value="" {{ old('id_satuan') == '' ? 'selected' : '' }}>-- Pilih
                                    Satuan --
                                </option>
                                @foreach ($data_satuan as $satuan)
                                    <option value="{{ $satuan->id_satuan }}"
                                        {{ old('id_satuan') == $satuan->id_satuan ? 'selected' : '' }}>
                                        {{ $satuan->kode_satuan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nilai_konversi" class="text-gray-900">Nilai Konversi</label>
                            <input type="number" class="form-control w-25" id="nilai_konversi"
                                placeholder="Masukan nilai konversi"
                                name="nilai_konversi"value="{{ old('nilai_konversi') }}">
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i>
                            Tambah</button>
                        <button type="reset" class="btn btn-warning" onclick="this.form.reset()"
                            id="resetBtn">Reset</button>
                        <a href="{{ route('konversi-barang.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            const form = document.querySelector('form');
            const resetBtn = document.getElementById('resetBtn');

            if (form) {
                form.addEventListener('reset', () => {
                    if (typeof checkFormChanges === 'function') {
                        setTimeout(checkFormChanges, 0);
                    }
                });
            }

            $(document).ready(function() {
                $('#nama_barang').autocomplete({
                    source: '{{ route('autocomplete-barang') }}',
                    minLength: 1,
                    select: function(event, ui) {
                        $('#nama_barang').val(ui.item.label);
                        $('#id_barang').val(ui.item.id);
                        return false;
                    }
                }).on('input', function() {
                    $('#id_barang').val('');
                });
            });
        </script>
    @endpush
@endsection
