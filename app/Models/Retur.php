<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    protected $table = 'retur';
    protected $primaryKey = 'id_retur';
    protected $guarded = ['id_retur'];
    protected $fillable = [
        'no_retur',
        'tanggal_retur',
        'id_penolakan',
        'id_pembelian',
        'id_penerimaan',
        'status',
    ];

    public function penolakan()
    {
        return $this->belongsTo(Penolakan::class, 'id_penolakan', 'id_penolakan');
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'id_pembelian', 'id_pembelian');
    }

    public function detailRetur()
    {
        return $this->hasMany(DetailRetur::class, 'id_retur', 'id_retur');
    }

    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class, 'id_penerimaan', 'id_penerimaan');
    }

    public function penerimaanPengganti()
    {
        return $this->hasOne(Penerimaan::class, 'id_retur', 'id_retur');
    }

    public static function generateNoRetur()
    {
        $today = now()->format('dmY');

        $last = self::where('no_retur', 'like', 'RTR-' . $today . '-%')->orderBy('id_retur', 'desc')->first();

        $number = $last
            ? ((int) substr($last->no_retur, -4)) + 1
            : 1;

        return 'RTR-' . $today . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
