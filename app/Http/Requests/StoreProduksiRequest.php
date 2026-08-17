<?php

namespace App\Http\Requests;

use App\Models\Produksi;
use Illuminate\Foundation\Http\FormRequest;

class StoreProduksiRequest extends FormRequest
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
        $id_produksi = $this->route('produksi');
        $rules = [
            'batch_produk' => 'required|min:5|max:50',
            'id_produk' => 'required|exists:barang_jadi,id_produk',
            'hasil_produksi' => 'required|numeric|min:1',
            'tujuan_produksi' => 'required|min:3|max:50',
            'id_barang' => 'required|array|min:1',
            'id_barang.*' => 'required|exists:barang,id_barang',
            'jumlah_keluar' => 'required|array',
            'jumlah_keluar.*' => 'required|numeric|min:1',
            'deskripsi' => 'nullable|array',
            'deskripsi.*' => 'nullable|string|max:255',
        ];

        if ($this->isMethod('POST')) {
            $rules['tanggal_produksi'] = 'required|date|after_or_equal:today';
            $rules['produk_expired'] = 'required|date|after_or_equal:' . date('Y-m-d', strtotime('+1 year'));
        } else {
            $produksi = Produksi::findOrFail($id_produksi);
            $rules['tanggal_produksi'] = 'required|date|after_or_equal:' . $produksi->tanggal_produksi;
            $rules['produk_expired'] = 'required|date|after_or_equal:date' . $produksi->produk_expired;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'batch_produk.required' => 'Batch produk wajib diisi',
            'batch_produk.min' => 'Batch produk minimal berisi :min karakter',
            'batch_produk.max' => 'Batch produk maksimal berisi :max karakter',
            'tanggal_produksi.required' => 'Tanggal produksi wajib diisi',
            'tanggal_produksi.date' => 'Format tanggal produksi tidak valid',
            'tanggal_produksi.after_or_equal' => 'Tanggal produksi minimal hari ini',
            'id_produk.required' => 'Produk wajib dipilih',
            'id_produk.exists' => 'Produk yang dipilih tidak terdaftar di sistem',
            'hasil_produksi.required' => 'Hasil produksi wajib diisi',
            'hasil_produksi.numeric' => 'Hasil produksi harus berupa angka',
            'hasil_produksi.min' => 'Hasil produksi minimal :min',
            'produk_expired.required' => 'Tanggal expired wajib diisi',
            'produk_expired.date' => 'Format tanggal expired tidak valid',
            'produk_expired.after' => 'Tanggal expired minimal 1 tahun dari hari ini (minimal tanggal ' . date('d-m-Y', strtotime('+1 year')) . ')',
            'tujuan_produksi.required' => 'Tujuan produksi wajib diisi',
            'tujuan_produksi.min' => 'Tujuan produksi minimal berisi :min karakter',
            'tujuan_produksi.max' => 'Tujuan produksi maksimal berisi :max karakter',

            'id_barang.required' => 'Minimal harus ada 1 bahan baku detail yang dimasukkan',
            'id_barang.array' => 'Format data bahan baku tidak valid',
            'id_barang.min' => 'Minimal harus memilih :min bahan baku',

            'id_barang.*.required' => 'Bahan baku pada baris ke-:position wajib dipilih',
            'id_barang.*.exists' => 'Bahan baku pada baris ke-:position tidak terdaftar',

            'jumlah_keluar.*.required' => 'Jumlah keluar pada baris ke-:position wajib diisi',
            'jumlah_keluar.*.numeric' => 'Jumlah keluar pada baris ke-:position harus berupa angka',
            'jumlah_keluar.*.min' => 'Jumlah keluar pada baris ke-:position minimal :min',

            'deskripsi.*.max' => 'Deskripsi pada baris ke-:position maksimal :max karakter',
            'deskripsi.*.string' => 'Deskripsi pada baris ke-:position harus berupa teks',
        ];
    }
}
