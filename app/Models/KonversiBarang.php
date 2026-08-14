<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonversiBarang extends Model
{
    protected $table = 'konversi_barang';
    protected $primaryKey = 'id_konversi';
    protected $guarded = ['id_konversi'];
    protected $fillable = [
        'id_barang',
        'id_satuan',
        'nilai_konversi',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }
}
