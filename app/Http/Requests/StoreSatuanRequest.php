<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSatuanRequest extends FormRequest
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
        $id_satuan = $this->route('satuan');
        $rules = [
            'nama_satuan' => 'required|min:3|max:20',
        ];

        if ($this->isMethod('POST')) {
            // Aturan untuk Store
            $rules['kode_satuan'] = 'required|unique:satuan,kode_satuan';
        } else {
            // Aturan untuk Update (Mengabaikan ID satuan yang sedang diedit)
            $rules['kode_satuan'] = 'required|unique:satuan,kode_satuan,' . $id_satuan . ',id_satuan';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'kode_satuan.required' => 'Kode satuan harus diisi',
            'kode_satuan.unique' => 'Kode satuan sudah digunakan',
            'nama_satuan.required' => 'Nama satuan harus diisi',
            'nama_satuan.min' => 'Nama satuan minimal 3 karakter',
            'nama_satuan.max' => 'Nama satuan maksimal 20 karakter',
        ];
    }
}
