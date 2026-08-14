<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;
    protected $table = 'supplier';
    protected $primaryKey = 'id_supplier';
    protected $guarded = ['id_supplier'];
    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'telepon_supplier',
        'alamat_supplier',
        'sales',
    ];

    public static function generateKodeSupplier()
    {
        $last = self::withTrashed()->where('kode_supplier', 'like', 'SP-' . '%')->orderBy('id_supplier', 'desc')->first();
        $number = $last ? ((int) substr($last->kode_supplier, -4)) + 1 : 1;
        return 'SP-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
