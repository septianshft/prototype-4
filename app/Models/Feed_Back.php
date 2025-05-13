<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feed_Back extends Model
{
    protected $connection = 'mysql_crud'; // arahkan ke MySQL
    protected $table = 'feed_back';
    protected $fillable = ['feedback', 'laporan_id', 'beasiswa_id', 'mahasiswa_id'];

    public function Beasiswa()
    {
        return $this->belongsTo(Beasiswa::class);
    }

    public function Data_Mahasiswa()
    {
        return $this->belongsTo(Data_Mahasiswa::class);
    }

    public function Laporan_Beasiswa()
    {
        return $this->belongsTo(Laporan_Beasiswa::class);
    }
}
