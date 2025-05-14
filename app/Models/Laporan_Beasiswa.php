<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan_Beasiswa extends Model
{
    protected $connection = 'mysql_crud'; // arahkan ke MySQL
    protected $table = 'laporan_beasiswa';
    protected $fillable = ['nama_laporan', 'user_id', 'file_path'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    }
