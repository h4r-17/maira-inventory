@extends('layouts.master')
@section('title', '| Tambah Alasan Penolakan')
@section('konten')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Tambah Alasan Penolakan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('alasan-penolakan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="kode_alasan" class="text-gray-900">Kode Alasan</label>
                            <input type="text" class="form-control w-25" id="kode_alasan" minlength="3" maxlength="30"
                                placeholder="Masukan kode alasan" name="kode_alasan"
                                value="{{ old('kode_alasan', $kodeAlasan) }}" readonly>
                            @error('kode_alasan')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_alasan" class="text-gray-900">Nama Alasan</label>
                            <input type="text" class="form-control w-25" id="nama_alasan" placeholder="Masukan alasan"
                                minlength="3" maxlength="30" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                                name="nama_alasan" value="{{ old('nama_alasan') }}">
                            @error('nama_alasan')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i>
                            Tambah</button>
                        <button type="reset" class="btn btn-warning" onclick="this.form.reset()"
                            id="resetBtn">Reset</button>
                        <a href="{{ route('alasan-penolakan.index') }}" class="btn btn-secondary">Kembali</a>
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
