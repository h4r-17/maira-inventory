<?php

namespace App\Http\Requests;

use App\Models\Pembelian;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePembelianRequest extends FormRequest
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
        $id_pembelian = $this->route('pembelian');
        $rules = [
            'id_pengajuan' => 'required|exists:pengajuan,id_pengajuan',
            'id_supplier' => 'required',
            'cara_bayar' => 'required|in:Tunai,Net 14 Hari,Net 30 Hari',
            'id_barang' => 'required|array|min:1',
            'id_barang.*' => 'required|exists:barang,id_barang',
            'id_satuan' => 'required|array|min:1',
            'id_satuan.*' => 'required|exists:satuan,id_satuan',
            'deskripsi' => 'nullable|array',
            'deskripsi.*' => 'nullable|string|max:255',
            'kuantitas' => 'required|array|min:1',
            'kuantitas.*' => 'required|integer|min:1',
            'harga' => 'required|array|min:1',
            'harga.*' => 'required|numeric|min:1',
            'diskon' => 'nullable|array',
            'diskon.*' => 'nullable|numeric|min:0',
        ];

        if ($this->isMethod('POST')) {
            // Aturan untuk Store
            $rules['no_nota'] = 'required|unique:pembelian,no_nota';
            $rules['tanggal_pembelian'] = 'required|date|after_or_equal:today';
        } else {
            $pembelian = Pembelian::findOrFail($id_pembelian);
            // Aturan untuk Update (Mengabaikan ID pembelian yang sedang diedit)
            $rules['no_nota'] = 'required|unique:pembelian,no_nota,' . $id_pembelian . ',id_pembelian';
            $rules['tanggal_pembelian'] = 'required|date|after_or_equal:' . $pembelian->tanggal_pembelian;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'no_nota.required' => 'Nomor nota wajib diisi',
            'no_nota.unique' => 'Nomor nota sudah digunakan oleh transaksi lain',
            'tanggal_pembelian.required' => 'Tanggal pembelian wajib diisi',
            'tanggal_pembelian.date' => 'Format tanggal pembelian tidak valid',
            'tanggal_pembelian.after_or_equal' => 'Tanggal pembelian minimal hari ini',
            'id_pengajuan.required' => 'Pengajuan wajib dipilih',
            'id_pengajuan.exists' => 'Pengajuan yang dipilih tidak valid',
            'id_supplier.required' => 'Supplier wajib dipilih',
            'cara_bayar.required' => 'Cara bayar wajib dipilih',
            'cara_bayar.in' => 'Cara bayar harus berupa Tunai, Net 14 Hari, atau Net 30 Hari',

            'id_barang.required' => 'Minimal harus ada 1 bahan baku yang dimasukkan',
            'id_barang.array' => 'Format data bahan baku tidak valid',
            'id_barang.min' => 'Minimal harus memilih :min bahan baku',
            'id_satuan.required' => 'Satuan bahan baku wajib diisi',
            'id_satuan.array' => 'Format satuan tidak valid',
            'id_satuan.min' => 'Minimal harus mengisi :min satuan',
            'kuantitas.required' => 'Kuantitas bahan baku wajib diisi',
            'kuantitas.array' => 'Format kuantitas tidak valid',
            'kuantitas.min' => 'Minimal harus mengisi :min kuantitas',
            'harga.required' => 'Harga bahan baku wajib diisi',
            'harga.array' => 'Format harga tidak valid',
            'harga.min' => 'Minimal harus mengisi :min harga',

            'id_barang.*.required' => 'Bahan baku pada baris ke-:position wajib dipilih dari daftar saran',
            'id_barang.*.exists' => 'Bahan baku pada baris ke-:position tidak terdaftar',

            'id_satuan.*.required' => 'Satuan pada baris ke-:position wajib dipilih',
            'id_satuan.*.exists' => 'Satuan pada baris ke-:position tidak terdaftar',

            'deskripsi.*.string' => 'Deskripsi pada baris ke-:position harus berupa teks',
            'deskripsi.*.max' => 'Deskripsi pada baris ke-:position maksimal :max karakter',

            'kuantitas.*.required' => 'Kuantitas pada baris ke-:position wajib diisi',
            'kuantitas.*.integer' => 'Kuantitas pada baris ke-:position harus berupa angka bulat',
            'kuantitas.*.min' => 'Kuantitas pada baris ke-:position minimal :min',

            'harga.*.required' => 'Harga pada baris ke-:position wajib diisi',
            'harga.*.numeric' => 'Harga pada baris ke-:position harus berupa angka',
            'harga.*.min' => 'Harga pada baris ke-:position minimal :min',

            'diskon.*.numeric' => 'Diskon pada baris ke-:position harus berupa angka',
            'diskon.*.min' => 'Diskon pada baris ke-:position tidak boleh kurang dari :min',
        ];
    }
}
