<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramStudiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('program_studi')->insert([
            ['program_studi' => 'Teknik Industri', 'jenjang' => 'S1'],
            ['program_studi' => 'Sistem Informasi', 'jenjang' => 'S1'],
            ['program_studi' => 'Digital Supply Chain', 'jenjang' => 'S1'],
            ['program_studi' => 'Manajemen Rekayasa Industri', 'jenjang' => 'S1'],
            ['program_studi' => 'Teknik Industri', 'jenjang' => 'S2'],
            ['program_studi' => 'Sistem Informasi', 'jenjang' => 'S2'],
        ]);
    }
}
