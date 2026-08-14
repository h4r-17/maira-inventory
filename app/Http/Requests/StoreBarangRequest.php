<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBarangRequest extends FormRequest
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
        $id_barang = $this->route('barang');
        $rules = [
            'kode_barang' => 'required|unique:barang,kode_barang|min:3|max:30',
            'nama_barang' => 'required|min:3|max:50',
            'id_satuan' => 'required|exists:satuan,id_satuan',
        ];

        if ($this->isMethod('POST')) {
            // Aturan untuk Store
            $rules['kode_barang'] = 'required|unique:barang,kode_barang|min:3|max:30';
        } else {
            // Aturan untuk Update (Mengabaikan ID_BARANG barang yang sedang diedit)
            $rules['kode_barang'] = 'required|unique:barang,kode_barang,' . $id_barang . ',id_barang|min:3|max:30';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'kode_barang.required' => 'Kode bahan baku harus diisi',
            'kode_barang.unique' => 'Kode bahan baku sudah digunakan',
            'kode_barang.min' => 'Kode bahan baku minimal 3 karakter',
            'kode_barang.max' => 'Kode bahan baku maksimal 30 karakter',
            'nama_barang.required' => 'Nama bahan baku harus diisi',
            'nama_barang.min' => 'Nama bahan baku minimal 3 karakter',
            'nama_barang.max' => 'Nama bahan baku maksimal 50 karakter',
            'id_satuan.required' => 'Satuan harus dipilih',
            'id_satuan.exists' => 'Satuan yang dipilih tidak valid',
        ];
    }
}
