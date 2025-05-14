<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Data_Mahasiswa extends Model
{
    protected $connection = 'mysql_crud'; // arahkan ke MySQL
    protected $table = 'data_mahasiswa';
    protected $fillable = ['nama_mahasiswa', 'nim', 'ipk', 'program_studi', 'user_id'];

    public function Status()
    {
        return $this->hasMany(Status::class);
    }

    public function Feed_Back()
    {
        return $this->hasMany(Feed_Back::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
