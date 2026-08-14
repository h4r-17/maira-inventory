<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreKonversiBarangRequest extends FormRequest
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
        $id_konversi = $this->route('konversi_barang');
        $rules = [
            'nama_barang' => 'required|string|max:255',
            'id_barang' => 'required|exists:barang,id_barang',
            'nilai_konversi' => 'required|numeric|min:1000|max:100000',
            'id_satuan' => "required|exists:satuan,id_satuan|unique:konversi_barang,id_satuan,{$id_konversi},id_konversi,id_barang,{$this->id_barang}",

        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nama_barang.required' => 'Nama bahan baku tidak boleh kosong',
            'id_barang.required' => 'Anda harus memilih bahan baku yang valid dari autocomplete',
            'id_barang.exists' => 'Bahan baku yang Anda pilih tidak terdaftar',

            'id_satuan.required' => 'Silakan pilih satuan terlebih dahulu',
            'id_satuan.exists' => 'Satuan yang Anda pilih tidak valid',
            'id_satuan.unique' => 'Kombinasi bahan baku dan satuan ini sudah ada',

            'nilai_konversi.required' => 'Nilai konversi tidak boleh kosong',
            'nilai_konversi.numeric' => 'Nilai konversi harus berupa angka',
            'nilai_konversi.min' => 'Nilai konversi minimal harus bernilai 1000.',
            'nilai_konversi.max' => 'Nilai konversi maksimal harus bernilai 100000.',

        ];
    }
}
