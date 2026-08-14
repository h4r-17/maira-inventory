<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchBarang extends Model
{
    protected $table = 'batch_barang';
    protected $primaryKey = 'id_batch';
    protected $guarded = ['id_batch'];
    protected $fillable = [
        'kode_batch',
        'kode_lot_supplier',
        'id_barang',
        'expired_date',
        'sisa_persediaan',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaan::class, 'id_batch', 'id_batch');
    }
}
