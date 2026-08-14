<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResepProduksi extends Model
{
    protected $table = 'resep_produksi';
    protected $primaryKey = 'id_resep';
    protected $guarded = ['id_resep'];
    protected $fillable = [
        'id_produk',
        'id_barang',
        'standar_kuantitas',
    ];

    public function barangJadi()
    {
        return $this->belongsTo(BarangJadi::class, 'id_produk', 'id_produk');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
