<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penolakan extends Model
{
    protected $table = 'penolakan';
    protected $primaryKey = 'id_penolakan';
    protected $guarded = ['id_penolakan'];
    protected $fillable = [
        'no_penolakan',
        'tanggal_penolakan',
        'id_produksi',
        'status',
    ];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'id_produksi', 'id_produksi');
    }

    public function detailPenolakan()
    {
        return $this->hasMany(DetailPenolakan::class, 'id_penolakan', 'id_penolakan');
    }

    public function retur()
    {
        return $this->hasOne(Retur::class, 'id_penolakan', 'id_penolakan');
    }

    public static function generateKodePenolakan()
    {
        $today = now()->format('dmY');
        $last = self::where('no_penolakan', 'like', 'RJK-' . $today . '-%')->orderBy('id_penolakan', 'desc')->first();
        $number = $last ? ((int) substr($last->no_penolakan, -4)) + 1 : 1;

        return 'RJK' . '-' . $today . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
