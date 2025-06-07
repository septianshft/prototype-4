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
        'file_persyaratan_path',
        'feedback',
        'file_status',
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

    public function dataMahasiswa()
    {
        return $this->hasOne(Data_Mahasiswa::class);
    }

    public function laporan()
    {
        return $this->hasMany(Laporan_Beasiswa::class, 'user_id', 'user_id')
            ->where('beasiswa_id', $this->beasiswa_id);
    }
}
