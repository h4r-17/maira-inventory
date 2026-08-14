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
                    <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="kode_barang" class="text-gray-900">Kode Bahan Baku</label>
                            <input type="text" class="form-control w-25" id="kode_barang"
                                placeholder="Masukan kode bahan baku" minlength="3" maxlength="30" name="kode_barang"
                                value="{{ old('kode_barang', $barang->kode_barang) }}" readonly>
                            @error('kode_barang')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_barang" class="text-gray-900">Nama Bahan Baku</label>
                            <input type="text" class="form-control w-25" id="nama_barang"
                                placeholder="Masukan nama bahan baku" minlength="3" maxlength="50" name="nama_barang"
                                value="{{ old('nama_barang', $barang->nama_barang) }}">
                            @error('nama_barang')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="id_satuan" class="text-gray-900">Satuan</label>
                            <select class="form-control w-25" id="id_satuan" name="id_satuan">
                                @foreach ($data_satuan as $satuan)
                                    <option value="{{ $satuan->id_satuan }}"
                                        {{ old('id_satuan', $barang->id_satuan) == $satuan->id_satuan ? 'selected' : '' }}>
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
                        <a href="{{ route('barang.index') }}" class="btn btn-secondary">Kembali</a>
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
