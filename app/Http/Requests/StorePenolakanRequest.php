<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StorePenolakanRequest extends FormRequest
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
            'no_penolakan' => 'required|string|max:50',
            'tanggal_penolakan' => 'required|date|after_or_equal:today',
            'id_produksi' => 'required|exists:produksi,id_produksi',
            'status' => 'required|in:Retur,Dimusnahkan,Sortir',
            'id_batch' => 'required|array|min:1',
            'id_batch.*' => ['required', 'integer', 'distinct', 'exists:batch_barang,id_batch',],
            'id_barang' => 'required|array|min:1',
            'id_barang.*' => ['required', 'integer', 'exists:barang,id_barang',],
            'jumlah_ditolak' => 'required|array|min:1',
            'jumlah_ditolak.*' => ['required', 'integer', 'min:1000',],
            'alasan_penolakan' => 'required|array|min:1',
            'alasan_penolakan.*' => ['required', 'string',],
            'deskripsi' => 'nullable|array',
            'deskripsi.*' => ['nullable', 'string', 'max:50',],
        ];
    }

    public function messages(): array
    {
        return [
            'no_penolakan.required' => 'Nomor penolakan wajib diisi',
            'no_penolakan.string' => 'Nomor penolakan harus berupa teks',
            'no_penolakan.max' => 'Nomor penolakan maksimal :max karakter',

            'tanggal_penolakan.required' => 'Tanggal penolakan wajib diisi',
            'tanggal_penolakan.date' => 'Format tanggal penolakan tidak valid',
            'tanggal_penolakan.after_or_equal' => 'Tanggal penolakan harus setelah atau sama dengan hari ini',

            'id_produksi.required' => 'Produksi wajib dipilih',
            'id_produksi.exists' => 'Produksi yang dipilih tidak valid',

            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status harus berupa Retur, Dimusnahkan, atau Sortir',

            'id_barang.required' => 'Minimal harus memilih satu bahan baku',
            'id_barang.min' => 'Minimal harus memilih :min bahan baku',

            'id_barang.*.required' => 'Bahan baku pada baris ke-:position wajib dipilih',
            'id_barang.*.integer' => 'Input bahan baku pada baris ke-:position harus berupa angka',
            'id_barang.*.exists' => 'Bahan baku pada baris ke-:position tidak ditemukan',

            'jumlah_ditolak.required' => 'Jumlah ditolak wajib diisi',
            'jumlah_ditolak.*.required' => 'Jumlah ditolak pada baris ke-:position wajib diisi',
            'jumlah_ditolak.*.integer' => 'Jumlah ditolak pada baris ke-:position harus berupa angka',
            'jumlah_ditolak.*.min' => 'Jumlah ditolak pada baris ke-:position minimal :min',

            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi',
            'alasan_penolakan.*.required' => 'Alasan penolakan pada baris ke-:position wajib diisi',

            'deskripsi.*.string' => 'Deskripsi pada baris ke-:position harus berupa teks',
            'deskripsi.*.max' => 'Deskripsi pada baris ke-:position maksimal :max karakter',
        ];
    }
}
