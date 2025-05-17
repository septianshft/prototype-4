<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $connection = 'mysql_crud'; // arahkan ke MySQL
    protected $table = 'status';
    protected $fillable = ['status', 'beasiswa_id', 'Data_Mahasiswa_id'];

    public function mahasiswa()
    {
        return $this->belongsTo(Data_Mahasiswa::class, 'data_mahasiswa_id');
    }

    public function beasiswa()
    {
        return $this->belongsTo(Beasiswa::class, 'beasiswa_id');
    }
}
