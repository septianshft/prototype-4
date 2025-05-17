<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beasiswa extends Model
{
   protected $connection = 'mysql_crud'; // arahkan ke MySQL
   protected $table = 'beasiswa';
   protected $fillable = ['nama_beasiswa', 'nama_penyelenggara', 'periode', 'kuota', 'deskripsi', 'dosen_id'];

   public function Aksi()
   {
      return $this->hasMany(Status::class);
   }

   public function Feed_Back()
   {
      return $this->hasMany(Feed_Back::class);
   }

   public function laporan_beasiswa()
   {
      return $this->hasMany(Laporan_Beasiswa::class);
   }

   public function appliedUsers()
   {
      return $this->hasMany(ApplyBeasiswa::class);
   }

   public function mahasiswas()
   {
      return $this->belongsToMany(Data_Mahasiswa::class, 'status')
         ->withPivot('status')
         ->withTimestamps();
   }
}