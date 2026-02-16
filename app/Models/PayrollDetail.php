<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    use HasFactory;

    protected $table = 'tb_payroll_detail';
    protected $primaryKey = 'id_payroll_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_payroll',
        'potongan_absen',
        'gaji_pokok',
        'gaji_lembur',
        'bonus',
    ];

    // Relasi ke Payroll (many-to-one)
    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'id_payroll', 'id_payroll');
    }
}
