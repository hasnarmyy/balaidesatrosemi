<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Present extends Model
{
    use HasFactory;

    protected $table = 'tb_presents';
    protected $primaryKey = 'id_presents';
    public $timestamps = false;

    protected $fillable = [
        'id_pegawai',
        'tanggal',
        'waktu',
        'keterangan',
        'foto_selfie_masuk',
        'foto_selfie_pulang',
        'keterangan_izin',
        'id_lembur',
        'status',
        'jam_masuk',
        'keterangan_msk',
        'jam_pulang',
        'latitude',
        'longitude'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function lembur()
    {
        return $this->belongsTo(Lembur::class, 'id_lembur', 'id_lembur');
    }
}
