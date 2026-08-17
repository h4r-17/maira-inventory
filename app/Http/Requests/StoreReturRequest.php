<?php

namespace App\Http\Requests;

use App\Models\DetailPenerimaan;
use App\Models\DetailPenolakan;
use App\Models\Pembelian;
use App\Models\Retur;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreReturRequest extends FormRequest
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
        $id_retur = $this->route('id_retur') ?? $this->route('retur');

        $this->merge([
            'id_pembelian' => $this->input('id_pembelian')
                ?: $this->resolvePembelianIdFromNoNota(),

            'id_supplier' => $this->input('id_supplier')
                ?: $this->resolveSupplierIdFromPembelian(),
        ]);

        $rules = [
            'tanggal_retur' => 'required|date',
            'id_penolakan' => 'nullable|exists:penolakan,id_penolakan',
            'id_penerimaan' => 'nullable|exists:penerimaan,id_penerimaan',

            'no_nota' => 'required|exists:pembelian,no_nota',
            'id_pembelian' => 'required|exists:pembelian,id_pembelian',
            'id_supplier' => 'required|exists:supplier,id_supplier',

            'id_detail_penolakan' => 'nullable|array',
            'id_detail_penolakan.*' =>
            'nullable|integer|exists:detail_penolakan,id_detail_penolakan',

            'id_detail_penerimaan' => 'nullable|array',
            'id_detail_penerimaan.*' =>
            'nullable|integer|exists:detail_penerimaan,id_detail_penerimaan',

            'id_batch' => 'nullable|array',
            'id_batch.*' =>
            'nullable|integer|exists:batch_barang,id_batch',

            'id_barang' => 'required|array|min:1',
            'id_barang.*' =>
            'required|integer|exists:barang,id_barang',

            'id_satuan' => 'required|array',
            'id_satuan.*' =>
            'required|integer|exists:satuan,id_satuan',

            'nilai_konversi' => 'required|array',
            'nilai_konversi.*' =>
            'required|numeric|min:1',

            'jumlah_ditolak' => 'nullable|array',
            'jumlah_ditolak.*' =>
            'nullable|numeric|min:0',

            'jumlah_retur' => 'required|array|min:1',
            'jumlah_retur.*' =>
            'required|integer|min:1',

            'batch_barang' => 'nullable|array',
            'batch_barang.*' =>
            'nullable|string|max:100',

            'expired_date' => 'nullable|array',
            'expired_date.*' =>
            'nullable|date',

            'deskripsi' => 'nullable|array',
            'deskripsi.*' => 'nullable|string|max:100',
        ];

        if ($this->isMethod('POST')) {
            $rules['no_retur'] = 'required|unique:retur,no_retur';
            $rules['tanggal_retur'] = 'required|date|after_or_equal:today';
        } else {
            $retur = Retur::findOrFail($id_retur);
            $rules['no_retur'] = 'required|unique:retur,no_retur,' . $id_retur . ',id_retur';
            $rules['tanggal_retur'] = 'required|date|after_or_equal:' . $retur->tanggal_retur;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'no_retur.required' => 'Nomor retur wajib diisi',
            'no_retur.unique' => 'Nomor retur sudah digunakan',
            'tanggal_retur.required' => 'Tanggal retur wajib diisi',
            'tanggal_retur.date' => 'Format tanggal retur tidak valid',
            'id_penolakan.exists' => 'Data penolakan tidak valid',
            'no_nota.required' => 'Nomor nota wajib diisi',
            'no_nota.exists' => 'Nomor nota tidak ditemukan',
            'id_pembelian.required' => 'Data pembelian tidak ditemukan dari nomor nota',
            'id_pembelian.exists' => 'Data pembelian tidak valid',

            'id_barang.required' => 'Minimal satu bahan baku harus diisi',
            'id_barang.min' => 'Minimal satu bahan baku harus diisi',
            'id_barang.*.required' => 'Bahan baku pada baris ke-:position wajib dipilih',
            'id_barang.*.exists' => 'Bahan baku pada baris ke-:position tidak valid',

            'batch_barang.*.required' => 'Batch bahan baku pada baris ke-:position wajib diisi',
            'id_satuan.*.required' => 'Satuan pada baris ke-:position wajib dipilih',
            'id_satuan.*.exists' => 'Satuan pada baris ke-:position tidak valid',
            'jumlah_retur.*.required' => 'Jumlah retur pada baris ke-:position wajib diisi',
            'jumlah_retur.*.integer' => 'Jumlah retur pada baris ke-:position harus berupa angka bulat',
            'jumlah_retur.*.min' => 'Jumlah retur pada baris ke-:position minimal :min',
            'expired_date.*.required' => 'Tanggal kedaluwarsa pada baris ke-:position wajib diisi',
            'expired_date.*.date' => 'Tanggal kedaluwarsa pada baris ke-:position tidak valid',
        ];
    }

    protected function resolvePembelianIdFromNoNota(): ?int
    {
        $noNota = $this->input('no_nota');

        if (empty($noNota)) {
            return null;
        }

        $pembelian = Pembelian::where('no_nota', $noNota)->first();

        return $pembelian?->id_pembelian;
    }

    protected function resolveSupplierIdFromPembelian(): ?int
    {
        $idPembelian = $this->input('id_pembelian') ?: $this->resolvePembelianIdFromNoNota();

        if (empty($idPembelian)) {
            return null;
        }

        $pembelian = Pembelian::find($idPembelian);

        return $pembelian?->id_supplier;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            $idPenolakan = $this->input('id_penolakan');
            $idPenerimaan = $this->input('id_penerimaan');

            // hanya satu sumber
            if (empty($idPenolakan) && empty($idPenerimaan)) {
                $validator->errors()->add(
                    'id_penolakan',
                    'Sumber retur harus dipilih.'
                );
            }

            if (!empty($idPenolakan) && !empty($idPenerimaan)) {
                $validator->errors()->add(
                    'id_penolakan',
                    'Retur tidak boleh berasal dari penolakan dan penerimaan sekaligus.'
                );
            }

            // validasi detail
            $detailPenolakan = $this->input('id_detail_penolakan', []);
            $detailPenerimaan = $this->input('id_detail_penerimaan', []);
            $jumlahRetur = $this->input('jumlah_retur', []);
            $nilaiKonversi = $this->input('nilai_konversi', []);

            foreach ($jumlahRetur as $index => $jumlah) {
                $jumlahReturItem = (int) $jumlah;

                $konversi = (int) ($nilaiKonversi[$index] ?? 0);

                if ($konversi <= 0) {
                    $validator->errors()->add(
                        "nilai_konversi.$index",
                        'Nilai konversi tidak valid.'
                    );

                    continue;
                }

                // Hitung jumlah retur dalam satuan pembelian
                $idDetailPenolakan =
                    $detailPenolakan[$index] ?? null;

                $idDetailPenerimaan =
                    $detailPenerimaan[$index] ?? null;

                if ($idDetailPenolakan && $idDetailPenerimaan) {

                    $validator->errors()->add(
                        "jumlah_retur.$index",
                        'Satu detail retur tidak boleh berasal dari dua sumber.'
                    );

                    continue;
                }

                if (!$idDetailPenolakan && !$idDetailPenerimaan) {

                    $validator->errors()->add(
                        "jumlah_retur.$index",
                        'Detail sumber retur tidak ditemukan.'
                    );

                    continue;
                }

                if ($idDetailPenolakan) {
                    $detail = DetailPenolakan::find(
                        $idDetailPenolakan
                    );
                } else {

                    $detail = DetailPenerimaan::find($idDetailPenerimaan);
                }

                if (!$detail) {
                    continue;
                }

                if ($idDetailPenolakan) {
                    if ((int) $detail->id_penolakan !== (int) $idPenolakan) {

                        $validator->errors()->add(
                            "id_detail_penolakan.$index",
                            'Detail penolakan tidak sesuai dengan dokumen penolakan yang dipilih.'
                        );
                        continue;
                    }
                }

                if ($idDetailPenerimaan) {
                    if ((int) $detail->id_penerimaan !== (int) $idPenerimaan) {

                        $validator->errors()->add(
                            "id_detail_penerimaan.$index",
                            'Detail penerimaan tidak sesuai dengan dokumen penerimaan yang dipilih.'
                        );
                        continue;
                    }
                }

                $jumlahDitolak =
                    (int) $detail->jumlah_ditolak;

                // validasi jumlah retur tidak boleh melebihi jumlah ditolak
                if ($jumlahReturItem > $jumlahDitolak) {

                    $validator->errors()->add(
                        "jumlah_retur.$index",
                        "Jumlah retur tidak boleh melebihi jumlah yang ditolak ({$jumlahDitolak})."
                    );
                }
            }
        });
    }
}
