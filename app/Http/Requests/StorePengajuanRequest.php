<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanRequest extends FormRequest
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
        $id_pengajuan = $this->route('id_pengajuan');
        $rules = [
            'tanggal_pengajuan' => 'required|date|after_or_equal:today',
            'id_barang' => 'required|array|min:1',
            'id_barang.*' => 'required|exists:barang,id_barang',
            'id_satuan' => 'required|array|min:1',
            'id_satuan.*' => 'required|exists:satuan,id_satuan',
            'kuantitas' => 'required|array|min:1',
            'kuantitas.*' => 'required|integer|min:1',
            'harga' => 'nullable|array',
            'harga.*' => 'numeric|nullable|min:0',
            'deskripsi' => 'nullable|array',
            'deskripsi.*' => 'nullable|string|max:255',
        ];

        if ($this->isMethod('POST')) {
            // Aturan untuk Store
            $rules['no_pengajuan'] = 'required|unique:pengajuan,no_pengajuan';
        } else {
            // Aturan untuk Update (Mengabaikan ID pengajuan yang sedang diedit)
            $rules['no_pengajuan'] = 'required|unique:pengajuan,no_pengajuan,' . $id_pengajuan . ',id_pengajuan';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'no_pengajuan.required' => 'Kode pengajuan harus diisi',
            'no_pengajuan.unique' => 'Kode pengajuan sudah digunakan',
            'tanggal_pengajuan.required' => 'Tanggal pengajuan harus diisi',
            'tanggal_pengajuan.date' => 'Tanggal pengajuan harus berupa tanggal yang valid',
            'tanggal_pengajuan.after_or_equal' => 'Tanggal pengajuan minimal hari ini',

            'id_barang.required' => 'Bahan baku harus diisi',
            'id_barang.array' => 'Data bahan baku tidak valid',
            'id_satuan.required' => 'Minimal satu satuan harus diisi',
            'id_satuan.array' => 'Data satuan tidak valid',
            'kuantitas.required' => 'Minimal satu kuantitas harus diisi',
            'kuantitas.array' => 'Data kuantitas tidak valid',
            'kuantitas.min' => 'Minimal satu bahan baku harus ditambahkan',
            'harga.array' => 'Data harga tidak valid',
            'deskripsi.array' => 'Data deskripsi tidak valid',

            // pesan untuk item array (pake :position biar tau baris berapa)
            'id_barang.*.required' => 'Bahan baku pada baris ke-:position wajib dipilih dari daftar saran',
            'id_barang.*.exists' => 'Bahan baku pada baris ke-:position tidak ditemukan dalam daftar bahan baku',

            'id_satuan.*.required' => 'Satuan pada baris ke-:position harus diisi',
            'id_satuan.*.exists' => 'Satuan pada baris ke-:position tidak ditemukan dalam daftar satuan',

            'kuantitas.*.required' => 'Kuantitas pada baris ke-:position harus diisi',
            'kuantitas.*.integer' => 'Kuantitas pada baris ke-:position harus berupa bilangan bulat',
            'kuantitas.*.min' => 'Kuantitas pada baris ke-:position minimal adalah 1',

            'harga.*.numeric' => 'Harga pada baris ke-:position harus berupa angka',
            'harga.*.min' => 'Harga pada baris ke-:position minimal adalah 0',

            'deskripsi.*.string' => 'Deskripsi pada baris ke-:position harus berupa teks',
            'deskripsi.*.max' => 'Deskripsi pada baris ke-:position maksimal 255 karakter',
        ];
    }
}
