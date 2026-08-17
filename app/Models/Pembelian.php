<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian';
    protected $primaryKey = 'id_pembelian';
    protected $guarded = ['id_pembelian'];
    protected $fillable = [
        'no_nota',
        'tanggal_pembelian',
        'id_supplier',
        'id_pengajuan',
        'cara_bayar',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'id_pembelian', 'id_pembelian');
    }

    public function penerimaan()
    {
        return $this->hasMany(Penerimaan::class, 'id_pembelian', 'id_pembelian');
    }

    public static function generateNoNota()
    {
        $today = now()->format('dmY');
        $last = self::where('no_nota', 'like', 'POWMF-' . $today . '-C%')->orderBy('id_pembelian', 'desc')->first();
        $number = $last ? ((int) substr($last->no_nota, -4)) + 1 : 1;

        return 'POWMF-' . $today . '-' . 'C' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
