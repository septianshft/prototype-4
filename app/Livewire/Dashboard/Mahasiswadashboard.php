<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MahasiswaDashboard extends Component
{
    public $mahasiswa;

    public function mount()
    {
        $this->loadMahasiswa();
    }

    public function loadMahasiswa()
    {
        $userId = Auth::id();

        $mahasiswaBase = DB::table('data_mahasiswa as dm')
            ->leftJoin('program_studi as ps', 'dm.program_studi_id', '=', 'ps.id')
            ->where('dm.user_id', $userId)
            ->select('dm.nama_mahasiswa', 'dm.nim', 'ps.program_studi as program_studi', 'ps.jenjang as jenjang')
            ->first();

        $beasiswaDiterima = DB::table('apply_beasiswa as ab')
            ->join('beasiswa as b', 'ab.beasiswa_id', '=', 'b.id')
            ->leftJoin('users as dsn', 'b.dosen_id', '=', 'dsn.id')
            ->where('ab.user_id', $userId)
            ->where('ab.status', 'diterima')
            ->select(
                'b.nama_beasiswa',
                'b.require_file',
                'b.persyaratan_file_name',
                'dsn.name as nama_dosen',
                DB::raw("'diterima' as status"),
                'ab.id as apply_id'
            )
            ->first();


        $this->mahasiswa = (object) array_merge(
            (array) $mahasiswaBase,
            (array) ($beasiswaDiterima ?? [
                'status' => null,
                'nama_beasiswa' => null,
                'require_file' => null,
                'persyaratan_file_name' => null,
                'nama_dosen' => null,
                'apply_id' => null,
            ])
        );
    }



    public function render()
    {
        return view('livewire.dashboard.mahasiswadashboard');
    }
}
