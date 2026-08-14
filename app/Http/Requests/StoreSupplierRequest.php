<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
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
        $id_supplier = $this->route('supplier');
        $rules = [
            'nama_supplier' => 'required|min:3|max:100',
            'telepon_supplier' => 'required|numeric',
            'alamat_supplier' => 'required|min:5|max:255',
            'sales' => 'required|min:4|max:50',
        ];

        if ($this->isMethod('POST')) {
            // Aturan untuk Store
            $rules['kode_supplier'] = 'required|unique:supplier,kode_supplier';
        } else {
            // Aturan untuk Update (Mengabaikan ID_SUPPLIER supplier yang sedang diedit)
            $rules['kode_supplier'] = 'required|unique:supplier,kode_supplier,' . $id_supplier . ',id_supplier';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'kode_supplier.required' => 'Kode supplier harus diisi',
            'kode_supplier.unique' => 'Kode supplier sudah ada',
            'kode_supplier.min' => 'Kode supplier minimal 3 karakter',
            'kode_supplier.max' => 'Kode supplier maksimal 30 karakter',
            'nama_supplier.required' => 'Nama supplier harus diisi',
            'nama_supplier.min' => 'Nama supplier minimal 3 karakter',
            'nama_supplier.max' => 'Nama supplier maksimal 100 karakter',
            'telepon_supplier.required' => 'Nomor telepon harus diisi',
            'telepon_supplier.numeric' => 'Nomor telepon harus berupa angka',
            'alamat_supplier.required' => 'Alamat supplier harus diisi',
            'alamat_supplier.min' => 'Alamat supplier minimal 5 karakter',
            'alamat_supplier.max' => 'Alamat supplier maksimal 255 karakter',
            'sales.required' => 'Sales harus diisi',
            'sales.min' => 'Sales minimal 4 karakter',
            'sales.max' => 'Sales maksimal 50 karakter',
        ];
    }
}
