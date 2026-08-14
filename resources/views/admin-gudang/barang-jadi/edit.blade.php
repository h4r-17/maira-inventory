@extends('layouts.master')
@section('title', '| Edit Produk')
@section('konten')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Produk</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('barang-jadi.update', $barangJadi->id_produk) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="kode_produk" class="text-gray-900">Kode produk</label>
                            <input type="text" class="form-control w-25" id="kode_produk"
                                placeholder="Masukan kode produk" minlength="3" maxlength="30" name="kode_produk"
                                value="{{ old('kode_produk', $barangJadi->kode_produk) }}" readonly>
                            @error('kode_produk')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_produk" class="text-gray-900">Nama Produk</label>
                            <input type="text" class="form-control w-25" id="nama_produk"
                                placeholder="Masukan nama produk" minlength="3" maxlength="50" name="nama_produk"
                                value="{{ old('nama_produk', $barangJadi->nama_produk) }}">
                            @error('nama_produk')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="id_satuan" class="text-gray-900">Satuan</label>
                            <select class="form-control w-25" id="id_satuan" name="id_satuan">
                                <option value="" {{ old('id_satuan') == '' ? 'selected' : '' }}>-- Pilih Satuan --
                                </option>
                                @foreach ($data_satuan as $satuan)
                                    <option value="{{ $satuan->id_satuan }}"
                                        {{ old('id_satuan', $barangJadi->id_satuan) == $satuan->id_satuan ? 'selected' : '' }}>
                                        {{ $satuan->kode_satuan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_satuan')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i>
                            Ubah</button>
                        <button type="reset" class="btn btn-warning" onclick="this.form.reset()"
                            id="resetBtn">Reset</button>
                        <a href="{{ route('barang-jadi.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        const resetBtn = document.getElementById('resetBtn');

        form.addEventListener('reset', () => {
            setTimeout(checkFormChanges, 0);
        });
    </script>
@endsection
