<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreResepProduksiRequest extends FormRequest
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
        return [
            'id_produk'   => 'required|exists:barang_jadi,id_produk',
            'id_barang'   => 'required|array|min:1',
            'id_barang.*' => 'required|exists:barang,id_barang|distinct',
            'standar_kuantitas' => 'required|array',
            'standar_kuantitas.*' => 'required|numeric|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'id_produk.required' => 'Produk wajib dipilih',
            'id_produk.exists' => 'Produk yang dipilih tidak ditemukan',
            'id_barang.required' => 'Bahan baku harus dipilih dari daftar saran',
            'id_barang.array' => 'Bahan baku tidak valid',
            'id_barang.min' => 'Minimal satu bahan baku harus ditambahkan',
            'id_barang.*.required' => 'Bahan baku wajib dipilih dari daftar saran',
            'id_barang.*.exists' => 'bahan baku yang dipilih tidak ditemukan',
            'id_barang.*.distinct' => 'Bahan baku tidak boleh sama dalam satu resep produk',
            'standar_kuantitas.required' => 'Standar kuantitas wajib diisi',
            'standar_kuantitas.array' => 'Data standar kuantitas tidak valid',
            'standar_kuantitas.*.required' => 'Standar kuantitas wajib diisi',
            'standar_kuantitas.*.numeric' => 'Standar kuantitas harus berupa angka',
            'standar_kuantitas.*.min' => 'Standar kuantitas harus lebih besar dari 0',
        ];
    }
}
