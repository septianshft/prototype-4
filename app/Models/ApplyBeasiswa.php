<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplyBeasiswa extends Model
{
    use HasFactory;

    protected $table = 'apply_beasiswa';

    protected $fillable = [
        'user_id',
        'beasiswa_id',
        'status',
    ];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Beasiswa
     */
    public function beasiswa()
    {
        return $this->belongsTo(Beasiswa::class);
    }

    // public function dataMahasiswa()
    // {
    //     return $this->hasMany(Data_Mahasiswa::class);
    // }
}