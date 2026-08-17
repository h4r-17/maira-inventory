@extends('layouts.master')
@section('title', '| Pengguna')
@section('konten')
@section('judul', 'Tabel Pengguna')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-end align-items-center">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambah">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tambah Pengguna</span>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-gray-900" id="tabelData">
                <thead class="bg-gray-100">
                    <tr>
                        <th>No </th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role->nama_role }}</td>
                            <td>
                                @if ($user->role->nama_role !== 'Super Admin')
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                        data-target="#edit{{ $user->id }}">
                                        <i class="fa-solid fa-pen-to-square"></i></button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                        data-target="#hapus{{ $user->id }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@foreach ($users as $user)
    <div class="modal fade" id="hapus{{ $user->id }}" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-gray-900" id="exampleModalLabel"><strong>Konfirmasi Hapus</strong></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                    </button>
                </div>
                <div class="modal-body text-gray-900">Apakah anda yakin ingin menghapus pengguna ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Tambah Modal Form -->
    <div class="modal fade" id="tambah" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="tambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-gray-900" id="tambahLabel"><strong>Tambah
                                Pengguna</strong>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name" class="text-gray-900">Nama</label>
                            <input type="name" class="form-control" id="name" minlength="3" maxlength="20"
                                placeholder="Masukan nama" name="name" value="{{ old('name') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="text-gray-900">Email</label>
                            <input type="email" class="form-control" id="email" minlength="3" maxlength="20"
                                placeholder="Masukan email" name="email" value="{{ old('email') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="text-gray-900">Password</label>
                            <input type="password" class="form-control" id="password" minlength="6" maxlength="15"
                                placeholder="Masukan password" name="password" value="{{ old('password') }}">
                        </div>
                        <div class="form-group">
                            <label for="role" class="text-gray-900">Role</label>
                            <select class="form-control" id="role" name="role_id">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ old('role_id') && old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->nama_role }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Edit Pengguna -->
    <div class="modal fade" id="edit{{ $user->id }}" data-backdrop="static" data-keyboard="false"
        tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEditPengguna" action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-gray-900"><strong>Edit Pengguna</strong></h5>
                        <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name" class="text-gray-900">Nama</label>
                            <input type="name" class="form-control" id="name" minlength="3" maxlength="20"
                                placeholder="Masukan nama" name="name" value="{{ old('name', $user->name) }}">
                            @error('name')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_email" class="text-gray-900">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email"
                                placeholder="Masukan email" maxlength="20" value="{{ old('email', $user->email) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_password" class="text-gray-900">Password</label>
                            <input type="password" class="form-control" id="edit_password" name="password"
                                maxlength="15" placeholder="Kosongkan jika tidak ingin mengubah password">
                            <small class="text-muted">Minimal 6 karakter jika ingin diubah.</small>
                        </div>
                        <div class="form-group">
                            <label for="edit_role" class="text-gray-900">Role</label>
                            <select class="form-control" id="edit_role" name="role_id">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ old('role_id', $user->role_id) }}"
                                        {{ $role->id == old('role_id', $user->role_id) ? 'selected' : '' }}>
                                        {{ $role->nama_role }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection
