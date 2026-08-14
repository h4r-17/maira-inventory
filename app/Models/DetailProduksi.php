<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailProduksi extends Model
{
    protected $table = 'detail_produksi';
    protected $primaryKey = 'id_detail_produksi';
    protected $guarded = ['id_detail_produksi'];
    protected $fillable = [
        'id_produksi',
        'id_batch',
        'jumlah_keluar',
        'deskripsi',
    ];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'id_produksi', 'id_produksi');
    }

    public function batch()
    {
        return $this->belongsTo(BatchBarang::class, 'id_batch', 'id_batch');
    }
}
