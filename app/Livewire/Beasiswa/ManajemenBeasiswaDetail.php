<?php

namespace App\Livewire\Beasiswa;

use Livewire\Component;
use App\Models\Beasiswa;
use App\Models\ApplyBeasiswa;
use App\Models\Laporan_Beasiswa;
use Illuminate\Support\Facades\Storage;

class ManajemenBeasiswaDetail extends Component
{
    public $beasiswaId;
    public $beasiswa;
    public $showLaporanModal = false;
    public $laporanDetail = [];
    public $mahasiswas = [];
    public $hideTableMahasiswa = false;


    public function mount(Beasiswa $beasiswa)
    {
        $this->beasiswaId = $beasiswa->id;
        $this->beasiswa = $beasiswa;
        $this->loadMahasiswas();


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
            ->where('apply_beasiswa.beasiswa_id', $this->beasiswaId)
            ->where('apply_beasiswa.status', 'diterima')
            ->get();
    }



    public function showLaporan($userId)
    {
        $laporans = Laporan_Beasiswa::where('user_id', $userId)
            ->where('beasiswa_id', $this->beasiswaId)
            ->get();

        $this->laporanDetail = $laporans->map(fn($laporan) => [
            'nama_laporan' => $laporan->nama_laporan,
            'jenis_laporan' => $laporan->jenis_laporan,
            'file_path' => $laporan->file_path ? Storage::url($laporan->file_path) : null,
        ])->toArray();

        $this->hideTableMahasiswa = true;
        // ⬅️ ketika klik, sembunyikan table mahasiswa
    }

    public function loadMahasiswas()
    {
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
            ->where('apply_beasiswa.beasiswa_id', $this->beasiswaId)
            ->where('apply_beasiswa.status', 'diterima')
            ->get();
    }



    public function closeLaporanModal()
    {
        $this->showLaporanModal = false;
        $this->laporanDetail = [];
    }

    public function hideLaporan()
    {
        $this->laporanDetail = null;
        $this->hideTableMahasiswa = false;
        $this->loadMahasiswas();
    }

    public function updatedShowLaporanModal() {}


    public function render()
    {
        return view('livewire.beasiswa.manajemen-beasiswa-detail', [
            'mahasiswas' => $this->mahasiswas
        ]);
    }
}
