<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    protected $table = 'penerimaan';
    protected $primaryKey = 'id_penerimaan';
    protected $guarded = ['id_penerimaan'];
    protected $fillable = [
        'no_registrasi',
        'tanggal_masuk',
        'jenis_penerimaan',
        'id_pembelian',
        'id_retur',
        'surat_jalan',
        'no_faktur',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'id_pembelian', 'id_pembelian');
    }

    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaan::class, 'id_penerimaan', 'id_penerimaan');
    }

    public function retur()
    {
        return $this->belongsTo(Retur::class, 'id_retur', 'id_retur');
    }

    public static function generateNoRegis()
    {
        $today = now()->format('dmY');

        $last = self::where('no_registrasi', 'like', 'REG-' . $today . '-%')->orderBy('id_penerimaan', 'desc')->first();

        $number = $last
            ? ((int) substr($last->no_registrasi, -4)) + 1
            : 1;

        return 'REG-' . $today . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
