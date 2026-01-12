<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'tb_pegawai';
    protected $primaryKey = 'id_pegawai';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_pegawai',
        'id_user',
        'id_jabatan',
        'nama_pegawai',
        'jekel',
        'pendidikan',
        'status_kepegawaian',
        'agama',
        'no_hp',
        'alamat',
        'foto',
        'ktp',
        'tanggal_masuk'
    ];

    public function relasiJabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    public function lembur()
    {
        return $this->hasMany(Lembur::class, 'id_pegawai', 'id_pegawai');
    }

    public function presents()
    {
        return $this->hasMany(Present::class, 'id_pegawai', 'id_pegawai');
    }

    public function payroll()
    {
        return $this->hasMany(Payroll::class, 'id_pegawai', 'id_pegawai');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public static function pegawaiById($userId)
    {
        return self::with('relasiJabatan')
            ->where('id_user', $userId)
            ->first();
    }
}
