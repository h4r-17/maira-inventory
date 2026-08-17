@extends('layouts.master')
@section('title', '| Tambah Produk')
@section('konten')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Produk</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('barang-jadi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="kode_produk" class="text-gray-900">Kode produk</label>
                            <input type="text" class="form-control w-25" id="kode_produk" minlength="3" maxlength="30"
                                placeholder="Masukan kode produk" name="kode_produk"
                                value="{{ old('kode_produk', $kodeProduk) }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nama_produk" class="text-gray-900">Nama Produk</label>
                            <input type="text" class="form-control w-25" id="nama_produk" minlength="3" maxlength="50"
                                placeholder="Masukan nama produk" name="nama_produk" value="{{ old('nama_produk') }}">
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
                        <a href="{{ route('barang-jadi.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
