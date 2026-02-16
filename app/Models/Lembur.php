<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'tb_lembur';
    protected $primaryKey = 'id_lembur';
    public $timestamps = false;

    protected $fillable = [
        'id_pegawai',
        'date',
        'waktu_lembur',
        'status'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public static function cekLemburById($pegawaiId)
    {
        $tglSkrng = Carbon::today()->toDateString();

        return self::where('id_pegawai', $pegawaiId)
            ->whereDate('date', $tglSkrng)
            ->first();
    }

    public function present()
    {
        return $this->hasOne(Present::class, 'id_lembur', 'id_lembur');
    }
}
