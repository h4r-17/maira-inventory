<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenolakan extends Model
{
    protected $table = 'detail_penolakan';
    protected $primaryKey = 'id_detail_penolakan';
    protected $guarded = ['id_detail_penolakan'];
    protected $fillable = [
        'id_penolakan',
        'id_batch',
        'jumlah_ditolak',
        'alasan_penolakan',
        'deskripsi',
    ];

    public function penolakan()
    {
        return $this->belongsTo(Penolakan::class, 'id_penolakan', 'id_penolakan');
    }

    public function batch()
    {
        return $this->belongsTo(BatchBarang::class, 'id_batch', 'id_batch');
    }
}
