<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    protected $connection = 'mysql_crud';
    protected $table = 'program_studi';

    protected $fillable = ['program_studi', 'jenjang'];

    public function mahasiswa()
    {
        return $this->hasMany(Data_Mahasiswa::class, 'program_studi_id');
    }
}

