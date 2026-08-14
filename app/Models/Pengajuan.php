<?php

namespace App\Models;

use App\Models\DetailPengajuan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';
    protected $primaryKey = 'id_pengajuan';
    protected $guarded = ['id_pengajuan'];
    protected $fillable = [
        'no_pengajuan',
        'tanggal_pengajuan',
        'status',
    ];

    public function detailPengajuan()
    {
        return $this->hasMany(DetailPengajuan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public static function generateKode()
    {
        $today = now()->format('dmY');

        $last = DB::table('pengajuan')->where('no_pengajuan', 'like', 'REQ-' . $today . '-%')->orderBy('id_pengajuan', 'desc')->first();

        $number = $last
            ? ((int) substr($last->no_pengajuan, -4)) + 1
            : 1;

        return 'REQ-' . $today . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
