<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPengajuan extends Model
{
    protected $table = 'detail_pengajuan';
    protected $primaryKey = 'id_detail_pengajuan';
    protected $guarded = ['id_detail_pengajuan'];
    protected $fillable = [
        'id_pengajuan',
        'id_barang',
        'id_satuan',
        'kuantitas',
        'harga',
        'deskripsi',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }
}
