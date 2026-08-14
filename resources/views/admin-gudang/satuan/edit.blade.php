@extends('layouts.master')
@section('title', '| Edit Satuan')
@section('konten')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Satuan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('satuan.update', $satuan->id_satuan) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="kode_satuan">Kode Satuan</label>
                            <select type="text" class="form-control" id="kode_satuan" placeholder="Masukan Kode Satuan"
                                name="kode_satuan">
                                <option value="">-- Pilih Kode Satuan --</option>
                                <option value="PCS"
                                    {{ old('kode_satuan', $satuan->kode_satuan) == 'PCS' ? 'selected' : '' }}>PCS</option>
                                <option value="KG"
                                    {{ old('kode_satuan', $satuan->kode_satuan) == 'KG' ? 'selected' : '' }}>KG</option>
                                <option value="LTR"
                                    {{ old('kode_satuan', $satuan->kode_satuan) == 'LTR' ? 'selected' : '' }}>LTR</option>
                                <option value="GRAM"
                                    {{ old('kode_satuan', $satuan->kode_satuan) == 'GRAM' ? 'selected' : '' }}>GRAM</option>
                                <option value="CTN"
                                    {{ old('kode_satuan', $satuan->kode_satuan) == 'CTN' ? 'selected' : '' }}>CTN</option>
                                <option value="ML"
                                    {{ old('kode_satuan', $satuan->kode_satuan) == 'ML' ? 'selected' : '' }}>ML</option>
                                <option value="SAK"
                                    {{ old('kode_satuan', $satuan->kode_satuan) == 'SAK' ? 'selected' : '' }}>SAK</option>
                                <option value="BTL"
                                    {{ old('kode_satuan', $satuan->kode_satuan) == 'BTL' ? 'selected' : '' }}>BTL</option>
                            </select>
                            @error('kode_satuan')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_satuan">Nama Satuan</label>
                            <input type="text" class="form-control" id="nama_satuan" placeholder="Masukan Nama Satuan"
                                name="nama_satuan" value="{{ old('nama_satuan', $satuan->nama_satuan) }}" minlength="3"
                                maxlength="20">
                            @error('nama_satuan')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i>
                            Ubah</button>
                        <button type="reset" class="btn btn-warning" onclick="this.form.reset()"
                            id="resetBtn">Reset</button>
                        <a href="{{ route('satuan.index') }}" class="btn btn-secondary">Kembali</a>
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
