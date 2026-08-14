<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailRetur extends Model
{
    protected $table = 'detail_retur';
    protected $primaryKey = 'id_detail_retur';
    protected $guarded = ['id_detail_retur'];
    protected $fillable = [
        'id_retur',
        'id_batch',
        'id_satuan',
        'nilai_konversi',
        'jumlah_retur',
        'deskripsi',
    ];

    public function retur()
    {
        return $this->belongsTo(Retur::class, 'id_retur', 'id_retur');
    }

    public function batch()
    {
        return $this->belongsTo(BatchBarang::class, 'id_batch', 'id_batch');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }
}
