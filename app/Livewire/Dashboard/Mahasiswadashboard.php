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

        $this->mahasiswa = DB::table('data_mahasiswa as dm')
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
                'dm.status_seleksi as status',
                'ab.id as apply_id'
            )
            ->first(); // Ambil 1 data karena 1 user hanya punya 1 apply
    }

    public function updateStatus($newStatus)
    {
        if (!$this->mahasiswa?->apply_id) {
            return;
        }

        DB::table('apply_beasiswa')
            ->where('id', $this->mahasiswa->apply_id)
            ->update(['status' => $newStatus]);

        $this->loadMahasiswa(); // refresh data agar tidak dobel
        session()->flash('message', 'Status beasiswa berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.dashboard.mahasiswadashboard');
    }
}
