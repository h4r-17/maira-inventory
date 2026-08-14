<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Satuan extends Model
{
    use SoftDeletes;
    protected $table = 'satuan';
    protected $primaryKey = 'id_satuan';
    protected $guarded = ['id_satuan'];
    protected $fillable = [
        'kode_satuan',
        'nama_satuan',
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_satuan', 'id_satuan');
    }

    public function barangJadi()
    {
        return $this->hasMany(BarangJadi::class, 'id_satuan', 'id_satuan');
    }
}
