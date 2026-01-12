<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Nama tabel
     */
    protected $table = 'user';

    /**
     * Primary key
     */
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * Timestamp
     */
    public $timestamps = false;

    /**
     * Mass assignment
     */
    protected $fillable = [
        'kode',
        'name',
        'email',
        'image',
        'password',
        'role_id',
        'is_active',
        'date_created',
    ];

    /**
     * Hidden field (keamanan)
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Cast tipe data (INI PENTING)
     */
    protected $casts = [
        'id'         => 'integer',
        'role_id'    => 'integer',
        'is_active'  => 'integer',
        'date_created' => 'datetime',
    ];

    /* ================= RELATION ================= */

    /**
     * Relasi ke tabel user_role
     */
    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id', 'id');
    }

    /**
     * Relasi ke tabel pegawai
     */
    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'id_user', 'id');
    }

    /* ================= HELPER ================= */

    /**
     * Cek apakah user admin
     */
    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }

    /**
     * Cek apakah user aktif
     */
    public function isActive(): bool
    {
        return $this->is_active === 1;
    }
}
