@extends('layouts.master')
@section('title', '| Tambah Bahan Baku')
@section('konten')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Bahan Baku</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="kode_barang" class="text-gray-900">Kode Bahan Baku</label>
                            <input type="text" class="form-control w-25" id="kode_barang"
                                placeholder="Masukan kode bahan baku" name="kode_barang" minlength="3" maxlength="30"
                                value="{{ old('kode_barang', $kode_barang) }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nama_barang" class="text-gray-900">Nama Bahan Baku</label>
                            <input type="text" class="form-control w-25" id="nama_barang" minlength="3" maxlength="50"
                                placeholder="Masukan nama bahan baku" name="nama_barang" value="{{ old('nama_barang') }}">
                        </div>
                        <div class="form-group">
                            <label for="id_satuan" class="text-gray-900">Satuan</label>
                            <select class="form-control w-25" id="id_satuan" name="id_satuan">
                                <option value="" {{ old('id_satuan') == '' ? 'selected' : '' }}>-- Pilih Satuan --
                                </option>
                                @foreach ($data_satuan as $satuan)
                                    <option value="{{ $satuan->id_satuan }}"
                                        {{ old('id_satuan') == $satuan->id_satuan ? 'selected' : '' }}>
                                        {{ $satuan->kode_satuan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i>
                            Tambah</button>
                        <a href="{{ route('barang.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
