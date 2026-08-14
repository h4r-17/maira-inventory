<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use SoftDeletes;
    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    protected $guarded = ['id_barang'];
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'id_satuan',
    ];

    public static function generateKodeBarang()
    {
        $last = self::withTrashed()->where('kode_barang', 'like', 'BB-' . '%')->orderBy('id_barang', 'desc')->first();
        $number = $last ? ((int) substr($last->kode_barang, -4)) + 1 : 1;

        return 'BB-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }

    public function konversiBarang()
    {
        return $this->hasOne(KonversiBarang::class, 'id_barang', 'id_barang');
    }

    public function batchBarang()
    {
        return $this->hasMany(BatchBarang::class, 'id_barang', 'id_barang');
    }

    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaan::class, 'id_barang', 'id_barang');
    }
}
