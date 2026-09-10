<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $table = 'produksi';
    protected $primaryKey = 'id_produksi';
    protected $guarded = ['id_produksi'];
    protected $fillable = [
        'batch_produk',
        'tanggal_produksi',
        'id_produk',
        'hasil_produksi',
        'hasil_qc',
        'produk_expired',
        'tujuan_produksi',
    ];

    public function barangJadi()
    {
        return $this->belongsTo(BarangJadi::class, 'id_produk', 'id_produk');
    }

    public function detailProduksi()
    {
        return $this->hasMany(DetailProduksi::class, 'id_produksi', 'id_produksi');
    }

    public static function generateNoRegis()
    {
        $last = self::where('batch_produk', 'like', 'REG-' . '-%')->orderBy('id_produksi', 'desc')->first();

        $number = $last
            ? ((int) substr($last->batch_produk, -4)) + 1
            : 1;

        return 'REG-' . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
