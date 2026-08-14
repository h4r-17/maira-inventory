<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBarangJadiRequest extends FormRequest
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
        $id_produk = $this->route('barang_jadi');
        $rules = [
            'nama_produk' => 'required|min:3|max:50',
            'id_satuan' => 'required|exists:satuan,id_satuan',
        ];

        if ($this->isMethod('POST')) {
            // Aturan untuk Store
            $rules['kode_produk'] = 'required|unique:barang_jadi,kode_produk';
        } else {
            // Aturan untuk Update (Mengabaikan ID_PRODUK produk yang sedang diedit)
            $rules['kode_produk'] = 'required|unique:barang_jadi,kode_produk,' . $id_produk . ',id_produk';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'kode_produk.required' => 'Kode produk harus diisi',
            'kode_produk.unique' => 'Kode produk sudah digunakan',
            'kode_produk.min' => 'Kode produk minimal 3 karakter',
            'kode_produk.max' => 'Kode produk maksimal 30 karakter',
            'nama_produk.required' => 'Nama produk harus diisi',
            'nama_produk.min' => 'Nama produk minimal 3 karakter',
            'nama_produk.max' => 'Nama produk maksimal 50 karakter',
            'id_satuan.required' => 'Satuan harus dipilih',
            'id_satuan.exists' => 'Satuan yang dipilih tidak valid',
        ];
    }
}
