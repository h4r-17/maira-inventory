<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('user');
        $rules = [
            'name' => 'required|string|min:3|max:20',
            'email' => 'required|email|unique:users,email|max:20',
            'role_id' => 'required|exists:roles,id',
        ];

        if ($this->isMethod('POST')) {
            // Aturan untuk Store
            $rules['email'] = 'required|unique:users,email';
            $rules['password'] = 'required|string|min:6|max:15';
        } else {
            $user = User::findOrFail($id);
            // Aturan untuk Update (Mengabaikan ID pengajuan yang sedang diedit)
            $rules['email'] = 'required|unique:users,email,' . $user->id . ',id';
            $rules['password'] = 'nullable|string|min:6|max:15';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'email.max' => 'Email maksimal 20 karakter',
            'name.required' => 'Nama wajib diisi',
            'name.string' => 'Nama harus berupa string',
            'name.min' => 'Nama minimal 3 karakter',
            'name.max' => 'Nama maksimal 20 karakter',
            'role_id.required' => 'Role wajib dipilih',
            'role_id.exists' => 'Role yang dipilih tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.string' => 'Password harus berupa string',
            'password.min' => 'Password minimal 6 karakter',
            'password.max' => 'Password maksimal 15 karakter',
        ];
    }
}
