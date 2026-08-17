@extends('layouts.master')
@section('title', '| Edit Supplier')
@section('konten')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Supplier</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('supplier.update', $supplier->id_supplier) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="kode_supplier" class="text-gray-900">Kode Supplier</label>
                            <input type="text" class="form-control w-25" id="kode_supplier"
                                placeholder="Masukan kode supplier" name="kode_supplier" minlength="3" maxlength="30"
                                value="{{ old('kode_supplier', $supplier->kode_supplier) }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nama_supplier" class="text-gray-900">Nama Supplier</label>
                            <input type="text" class="form-control w-25" id="nama_supplier"
                                placeholder="Masukan nama supplier" name="nama_supplier" minlength="3" maxlength="100"
                                value="{{ old('nama_supplier', $supplier->nama_supplier) }}">
                        </div>
                        <div class="form-group">
                            <label for="telepon_supplier" class="text-gray-900">Nomor Telepon Supplier</label>
                            <input type="tel" class="form-control w-25" id="telepon_supplier"
                                placeholder="Masukan nomor telepon aktif supplier" name="telepon_supplier"
                                value="{{ old('telepon_supplier', $supplier->telepon_supplier) }}" inputmode="numeric"
                                pattern="[0-9]*" oninput="this.value=this.value.replace(/\D/g,'')"
                                title="Hanya boleh memasukkan angka" maxlength="15">
                        </div>
                        <div class="form-group">
                            <label for="alamat_supplier" class="text-gray-900">Alamat Supplier</label>
                            <textarea class="form-control w-25" id="alamat_supplier" placeholder="Masukan alamat supplier" name="alamat_supplier"
                                minlength="5" maxlength="255" rows="3">{{ old('alamat_supplier', $supplier->alamat_supplier) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="sales" class="text-gray-900">Nama Sales</label>
                            <input type="text" class="form-control w-25" id="sales"
                                placeholder="Masukan nama sales yang bertugas" name="sales" minlength="4" maxlength="50"
                                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                                value="{{ old('sales', $supplier->sales) }}">
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i>
                            Simpan</button>
                        <a href="{{ route('supplier.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
