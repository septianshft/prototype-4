<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $connection = 'mysql_crud'; // arahkan ke MySQL
    protected $table = 'status';
    protected $fillable = ['status', 'beasiswa_id', 'Data_Mahasiswa_id'];

    public function Beasiswa()
    {
        return $this->belongsTo(Beasiswa::class);
    }

    public function Data_mahasiswa()
    {
        return $this->belongsTo(Data_mahasiswa::class);
    }
}
