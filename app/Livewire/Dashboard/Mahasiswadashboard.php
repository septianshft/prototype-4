<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Mahasiswadashboard extends Component
{
    public $mahasiswas;

    public function mount()
    {
        $userId = Auth::id();

        $this->mahasiswas = DB::table('apply_beasiswa')
            ->where('user_id', $userId)
            ->get();


        $this->mahasiswas = DB::table('data_mahasiswa as dm')
            ->leftJoin('apply_beasiswa as ab', 'ab.user_id', '=', 'dm.user_id')
            ->leftJoin('beasiswa as b', 'ab.beasiswa_id', '=', 'b.id')
            ->leftJoin('users as dsn', 'b.dosen_id', '=', 'dsn.id')
            ->where('dm.user_id', $userId)
            ->select(
                'dm.nama_mahasiswa',
                'dm.nim',
                'dm.program_studi',
                'b.nama_beasiswa',
                'dsn.name as nama_dosen',
                'ab.status'
            )
            ->get();



        // dd($this->mahasiswas);
    }

    public function render()
    {
        return view('livewire.dashboard.mahasiswadashboard');
    }
}
