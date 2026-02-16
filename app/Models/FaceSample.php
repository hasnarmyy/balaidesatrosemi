<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceSample extends Model
{
    use HasFactory;

    protected $table = 'tb_face_samples';
    protected $primaryKey = 'id_face_sample';
    public $timestamps = true;

    protected $fillable = [
        'id_pegawai',
        'image_path',
        'embedding',
        'model_version',
        'detected_gender',
    ];

    protected $casts = [
        'embedding' => 'array',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}
