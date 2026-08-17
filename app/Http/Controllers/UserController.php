<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function update(StoreUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role_id = $validated['role_id'];

        // Jika kolom password diisi oleh admin, maka update password lama
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Pengguna diperbarui!');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}
