<?php

namespace App\Livewire\Beasiswa;

use Livewire\Component;
use App\Models\ApplyBeasiswa;
use App\Models\Data_Mahasiswa;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Auth;

class LaporanBeasiswaDetail extends Component
{
    public $mahasiswas = [];

    public function mount()
    {
        $user = Auth::user();

        if (in_array($user->role, ['dosen', 'admin'])) {
            $this->mahasiswas = ApplyBeasiswa::select(
                'apply_beasiswa.id AS apply_id',
                'data_mahasiswa.nama_mahasiswa',
                'data_mahasiswa.nim',
                'program_studi.program_studi',
                'program_studi.jenjang',
                'apply_beasiswa.user_id'
            )
                ->join('data_mahasiswa', 'data_mahasiswa.user_id', '=', 'apply_beasiswa.user_id')
                ->join('program_studi', 'program_studi.id', '=', 'data_mahasiswa.program_studi_id')
                ->where('apply_beasiswa.status', 'diterima')
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.beasiswa.laporan-beasiswa-detail', [
            'mahasiswas' => $this->mahasiswas,
        ]);
    }
}
