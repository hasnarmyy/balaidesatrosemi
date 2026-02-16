<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $table = 'tb_payroll';
    protected $primaryKey = 'id_payroll';
    public $timestamps = false;

    protected $fillable = [
        'id_pegawai',
        'id_jabatan',
        'periode',
        'tanggal',
        'keterangan',
        'gaji_bersih'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    public function relasiJabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }

    public function details()
    {
        return $this->hasMany(PayrollDetail::class, 'id_payroll', 'id_payroll');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($payroll) {
            $payroll->details()->delete();
        });
    }
}
