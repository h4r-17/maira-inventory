@extends('layouts.master')
@section('title', '| Tambah Supplier')
@section('konten')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Supplier</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('supplier.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="kode_supplier" class="text-gray-900">Kode Supplier</label>
                            <input type="text" class="form-control w-25" id="kode_supplier" minlength="3" maxlength="30"
                                placeholder="Masukan kode supplier" name="kode_supplier"
                                value="{{ old('kode_supplier', $kodeSupplier) }}" readonly>
                            @error('kode_supplier')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_supplier" class="text-gray-900">Nama Supplier</label>
                            <input type="text" class="form-control w-25" id="nama_supplier" minlength="3"
                                maxlength="100" placeholder="Masukan nama supplier" name="nama_supplier"
                                value="{{ old('nama_supplier') }}">
                            @error('nama_supplier')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="telepon_supplier" class="text-gray-900">Nomor Telepon Supplier</label>
                            <input type="tel" class="form-control w-25" id="telepon_supplier"
                                placeholder="Masukan nomor telepon aktif supplier" name="telepon_supplier"
                                value="{{ old('telepon_supplier') }}" inputmode="numeric" pattern="[0-9]*"
                                oninput="this.value=this.value.replace(/\D/g,'')" title="Hanya boleh memasukkan angka"
                                maxlength="15">
                            @error('telepon_supplier')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="alamat_supplier" class="text-gray-900">Alamat Supplier</label>
                            <textarea class="form-control w-25" id="alamat_supplier" minlength="3" maxlength="255" rows="3"
                                placeholder="Masukan alamat supplier" name="alamat_supplier">{{ old('alamat_supplier') }}</textarea>
                            @error('alamat_supplier')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="sales" class="text-gray-900">Nama Sales</label>
                            <input type="text" class="form-control w-25" id="sales" minlength="3" maxlength="50"
                                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                                placeholder="Masukan nama sales yang bertugas" name="sales" value="{{ old('sales') }}">
                            @error('sales')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i>
                            Tambah</button>
                        <button type="reset" class="btn btn-warning" onclick="this.form.reset()"
                            id="resetBtn">Reset</button>
                        <a href="{{ route('supplier.index') }}" class="btn btn-secondary">Kembali</a>
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
