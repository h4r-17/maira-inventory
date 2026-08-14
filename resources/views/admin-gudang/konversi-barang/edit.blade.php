@extends('layouts.master')
@section('title', '| Edit Bahan Baku')
@section('konten')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Bahan Baku</h6>
                </div>
                <div class="card-body">
                    <!-- Pastikan method menggunakan PUT atau PATCH untuk update -->
                    <form action="{{ route('konversi-barang.update', $konversiBarang->id_konversi) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="nama_barang" class="text-gray-900">Nama Bahan Baku</label>
                            <input type="text" name="nama_barang" id="nama_barang" class="form-control w-25"
                                placeholder="Nama Bahan Baku"
                                value="{{ old('nama_barang') ?? ($konversiBarang->barang->nama_barang ?? '') }}" required
                                autocomplete="off">

                            <input type="hidden" name="id_barang" id="id_barang"
                                value="{{ old('id_barang') ?? ($konversiBarang->id_barang ?? '') }}">

                            @error('id_barang')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="id_satuan" class="text-gray-900">Satuan Supplier</label>
                            <select class="form-control w-25" id="id_satuan" name="id_satuan">
                                <option value="">-- Pilih Satuan --</option>
                                @foreach ($data_satuan as $satuan)
                                    <option value="{{ $satuan->id_satuan }}"
                                        {{ old('id_satuan', $konversiBarang->id_satuan) == $satuan->id_satuan ? 'selected' : '' }}>
                                        {{ $satuan->kode_satuan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nilai_konversi" class="text-gray-900">Nilai Konversi</label>
                            <input type="number" class="form-control w-25" id="nilai_konversi"
                                placeholder="Masukan nilai konversi" name="nilai_konversi"
                                value="{{ old('nilai_konversi', $konversiBarang->nilai_konversi) }}">
                        </div>

                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i> Simpan
                            Perubahan</button>
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
