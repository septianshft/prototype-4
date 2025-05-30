<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan_Beasiswa extends Model
{
    protected $connection = 'mysql_crud';
    protected $table = 'laporan_beasiswa';

    protected $fillable = ['nama_laporan', 'user_id', 'file_path', 'beasiswa_id', 'jenis_laporan'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function beasiswa()
    {
        return $this->belongsTo(Beasiswa::class, 'beasiswa_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
