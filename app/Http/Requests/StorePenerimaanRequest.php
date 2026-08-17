<?php

namespace App\Http\Requests;

use App\Models\BatchBarang;
use App\Models\Penerimaan;
use App\Models\Retur;
use Illuminate\Foundation\Http\FormRequest;

class StorePenerimaanRequest extends FormRequest
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
        $id_penerimaan = $this->route('id_penerimaan');
        $rules = [
            'tanggal_masuk' => 'required|date|after_or_equal:today',
            'no_nota' => 'required|exists:pembelian,no_nota',
            'id_pembelian' => 'required|exists:pembelian,id_pembelian',
            'no_faktur' => 'nullable|max:25',
            'surat_jalan' => 'nullable|max:25',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'jenis_penerimaan' => 'required|in:Pembelian,Retur',
            'id_retur' => 'required_if:jenis_penerimaan,Retur|nullable|exists:retur,id_retur',
            'id_barang' => 'required|array|min:1',
            'id_barang.*' => 'required|integer|exists:barang,id_barang',
            'id_satuan' => 'required|array',
            'id_satuan.*' => 'required|integer|exists:satuan,id_satuan',
            'rasio_konversi' => 'nullable|array',
            'rasio_konversi.*' => 'nullable|numeric|min:1',
            'jumlah_masuk' => 'required|array',
            'jumlah_masuk.*' => 'required|numeric|min:0',
            'jumlah_ditolak' => 'nullable|array',
            'jumlah_ditolak.*' => 'nullable|numeric|min:0',
            'alasan_penolakan' => 'nullable|array',
            'alasan_penolakan.*' => 'nullable|string|max:255',
            'expired_date' => 'required|array',
            'expired_date.*' => 'nullable|date|after_or_equal:' . date('Y-m-d', strtotime('+4 months')),
            'kode_lot_supplier' => 'nullable|array',
            'kode_lot_supplier.*' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|array',
            'deskripsi.*' => 'nullable|string|max:255',
            'id_detail_penerimaan'   => 'nullable|array',
            'id_detail_penerimaan.*' => 'nullable|integer|exists:detail_penerimaan,id_detail_penerimaan',
            'id_detail_retur' => 'nullable|array',
            'id_detail_retur.*' => 'nullable|integer|exists:detail_retur,id_detail_retur',

        ];

        if ($this->isMethod('POST')) {
            // Aturan untuk Store
            $rules['no_registrasi'] = 'required|unique:penerimaan,no_registrasi';
            $rules['tanggal_masuk'] = 'required|date|after_or_equal:today';
        } else {
            $penerimaan = Penerimaan::findOrFail($id_penerimaan);
            // Aturan untuk Update (Mengabaikan ID penerimaan yang sedang diedit)
            $rules['no_registrasi'] = 'required|unique:penerimaan,no_registrasi,' . $id_penerimaan . ',id_penerimaan';
            $rules['tanggal_masuk'] = 'required|date|after_or_equal:' . $penerimaan->tanggal_masuk;
        }

        return $rules;
    }

    protected function validateRetur($validator)
    {
        $idRetur = $this->input('id_retur');
        $idBarang = $this->input('id_barang', []);
        $jumlahMasuk = $this->input('jumlah_masuk', []);
        $jumlahDitolak = $this->input('jumlah_ditolak', []);
        $idDetailRetur = $this->input('id_detail_retur', []);

        if (!$idRetur || empty($idBarang)) {
            return;
        }

        $retur = Retur::with('detailRetur')->find($idRetur);

        if (!$retur) {
            $validator->errors()->add('id_retur', 'Data retur tidak ditemukan.');

            return;
        }

        // Cek apakah sudah ada penerimaan untuk retur ini
        $sudahDigunakan = Penerimaan::where('id_retur', $idRetur)->exists();

        if ($sudahDigunakan) {
            $validator->errors()->add('id_retur', 'Retur ini sudah memiliki penerimaan bahan baku pengganti.');
            return;
        }

        // Pastikan pembelian dan supplier sesuai dengan Retur
        if (
            (int) $this->input('id_pembelian')
            !== (int) $retur->id_pembelian
        ) {

            $validator->errors()->add(
                'id_pembelian',
                'Pembelian tidak sesuai dengan Retur.'
            );
        }

        if (
            (int) $this->input('id_supplier') !== (int) $retur->id_supplier
        ) {

            $validator->errors()->add('id_supplier', 'Supplier tidak sesuai dengan Retur.');
        }

        // Ambil detail retur dan keyBy id_detail_retur untuk memudahkan pencarian
        $detailRetur = $retur->detailRetur->keyBy('id_detail_retur');

        foreach ($idBarang as $index => $idBarangItem) {
            $idDetailReturItem = $idDetailRetur[$index] ?? null;
            if (!$idDetailReturItem) {

                $validator->errors()->add("id_detail_retur.{$index}", 'Detail Retur harus dipilih.');

                continue;
            }

            $detail = $detailRetur->get($idDetailReturItem);

            if (!$detail) {
                $validator->errors()->add("id_detail_retur.{$index}", 'Detail Retur tidak valid.');

                continue;
            }

            // Ambil batch barang dari detail retur
            $batch = BatchBarang::find($detail->id_batch);

            if (!$batch || (int) $batch->id_barang !== (int) $idBarangItem) {
                $validator->errors()->add("id_barang.{$index}", 'Bahan baku tidak sesuai dengan detail Retur.');
                continue;
            }

            $jumlahMasukItem = (float) ($jumlahMasuk[$index] ?? 0);
            $jumlahDitolakItem = (float) ($jumlahDitolak[$index] ?? 0);
            $jumlahSekarang =
                $jumlahMasukItem + $jumlahDitolakItem;
            $jumlahRetur = (float) $detail->jumlah_retur;

            // jumlah penerimaan tidak boleh melebihi jumlah retur
            if ($jumlahSekarang > $jumlahRetur) {

                $validator->errors()->add(
                    "jumlah_masuk.{$index}",
                    "Jumlah penerimaan melebihi jumlah Retur. " .
                        "Jumlah Retur: {$jumlahRetur}, " .
                        "jumlah yang dimasukkan: {$jumlahSekarang}."
                );
            }

            // harus ada barang diterima
            if ($jumlahSekarang <= 0) {

                $validator->errors()->add(
                    "jumlah_masuk.{$index}",
                    'Jumlah masuk atau jumlah ditolak harus lebih dari 0.'
                );
            }

            // Cek jika jumlah masuk lebih dari 0, maka expired date harus diisi
            if (
                $jumlahMasukItem > 0 && empty($this->input("expired_date.{$index}"))
            ) {

                $validator->errors()->add(
                    "expired_date.{$index}",
                    'Tanggal kedaluwarsa wajib diisi.'
                );
            }
        }
    }

    public function messages(): array
    {
        return [
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
            'tanggal_masuk.date' => 'Format tanggal masuk tidak valid',
            'tanggal_masuk.after_or_equal' => 'Tanggal masuk minimal hari ini',
            'no_nota.required' => 'Nomor nota harus diisi',
            'no_nota.exists' => 'Nomor nota tidak valid',
            'id_pembelian.required' => 'ID pembelian harus diisi',
            'id_pembelian.exists' => 'ID pembelian tidak valid',
            'no_faktur.max' => 'Nomor faktur maksimal 25 karakter',
            'surat_jalan.max' => 'Nomor surat jalan maksimal 25 karakter',
            'id_supplier.required' => 'ID supplier harus diisi',
            'id_supplier.exists' => 'ID supplier tidak valid',
            'jenis_penerimaan.required' => 'Jenis penerimaan harus diisi',
            'jenis_penerimaan.in' => 'Jenis penerimaan tidak valid',
            'id_barang.*.required' => 'ID bahan baku harus diisi',
            'id_barang.*.exists' => 'ID bahan baku tidak valid',
            'jumlah_masuk.*.required' => 'Jumlah masuk harus diisi',
            'jumlah_masuk.*.numeric' => 'Jumlah masuk harus berupa angka',
            'jumlah_masuk.*.min' => 'Jumlah masuk tidak boleh kurang dari 0',
            'jumlah_ditolak.*.numeric' => 'Jumlah ditolak harus berupa angka',
            'jumlah_ditolak.*.min' => 'Jumlah ditolak tidak boleh kurang dari 0',
            'alasan_penolakan.*.string' => 'Alasan penolakan harus berupa teks',
            'alasan_penolakan.*.max' => 'Alasan penolakan maksimal 255 karakter',
            'deskripsi.*.string' => 'Deskripsi harus berupa teks',
            'deskripsi.*.max' => 'Deskripsi maksimal 100 karakter',
            'id_detail_penerimaan.*.exists' => 'ID detail penerimaan tidak valid',
            'expired_date.*.required' => 'Tanggal kedaluwarsa wajib diisi.',
            'expired_date.*.date' => 'Format tanggal tidak valid.',
            'expired_date.*.after_or_equal' => 'Tanggal minimal harus setelah 4 bulan dari hari ini (minimal tanggal ' . date('d-m-Y', strtotime('+4 months')) . ').',
        ];
    }
}
