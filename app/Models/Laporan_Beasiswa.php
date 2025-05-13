<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan_Beasiswa extends Model
{
    protected $connection = 'mysql_crud'; // arahkan ke MySQL
    protected $table = 'laporan_beasiswa';
    protected $fillable = ['nama_laporan', 'file_path', 'beasiswa_id', 'data_mahasiswa_id'];

    public function Beasiswa()
    {
        return $this->belongsTo(Beasiswa::class);
    }

    public function Data_Mahasiswa()
    {
        return $this->belongsTo(Data_Mahasiswa::class);
    }
}
