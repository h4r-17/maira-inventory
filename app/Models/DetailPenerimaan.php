<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenerimaan extends Model
{
    protected $table = 'detail_penerimaan';
    protected $primaryKey = 'id_detail_penerimaan';
    protected $guarded = ['id_detail_penerimaan'];
    protected $fillable = [
        'id_penerimaan',
        'id_batch',
        'jumlah_masuk',
        'jumlah_ditolak',
        'alasan_penolakan',
        'rasio_konversi',
        'deskripsi',
    ];

    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class, 'id_penerimaan', 'id_penerimaan');
    }

    public function batch()
    {
        return $this->belongsTo(BatchBarang::class, 'id_batch', 'id_batch');
    }
}
