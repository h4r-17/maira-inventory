<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangJadi extends Model
{
    use SoftDeletes;
    protected $table = 'barang_jadi';
    protected $primaryKey = 'id_produk';
    protected $guarded = ['id_produk'];
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'id_satuan',
    ];

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }

    public function resepProduksi()
    {
        return $this->hasMany(ResepProduksi::class, 'id_produk', 'id_produk');
    }

    public function produksi()
    {
        return $this->hasMany(Produksi::class, 'id_produk', 'id_produk');
    }

    public static function generateKodeProduk()
    {
        $last = self::withTrashed()->where('kode_produk', 'like', 'M' . '%')->orderBy('id_produk', 'desc')->first();
        $number = $last ? ((int) substr($last->kode_produk, -2)) + 1 : 1;

        return 'M' . str_pad($number, 2, '0', STR_PAD_LEFT);
    }
}
