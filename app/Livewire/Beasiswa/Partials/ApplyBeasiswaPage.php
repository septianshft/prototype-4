<?php

namespace App\Livewire\Beasiswa\Partials;

use App\Models\Beasiswa;
use App\Models\Data_Mahasiswa;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ApplyBeasiswaPage extends Component
{
    public function apply($beasiswaId)
    {
        $mahasiswa = Data_Mahasiswa::where('user_id', Auth::id())->first();

        if (!$mahasiswa) {
            session()->flash('error', 'Data mahasiswa tidak ditemukan.');
            return;
        }

        // 1. Cek apakah mahasiswa sudah pernah diterima beasiswa manapun
        $sudahDiterima = $mahasiswa->beasiswas()
            ->wherePivot('status', 'diterima')
            ->exists();

        if ($sudahDiterima) {
            session()->flash('error', 'Kamu sudah diterima beasiswa lain, tidak bisa apply beasiswa baru.');
            return;
        }

        // 2. Cek apakah mahasiswa sudah apply beasiswa ini sebelumnya (status apapun)
        if ($mahasiswa->beasiswas()->wherePivot('beasiswa_id', $beasiswaId)->exists()) {
            session()->flash('error', 'Kamu sudah mengajukan beasiswa ini sebelumnya.');
            return;
        }

        // 3. Cek kuota beasiswa sudah penuh atau belum (hitung jumlah yang diterima)
        $beasiswa = Beasiswa::findOrFail($beasiswaId);

        $jumlahDiterima = $beasiswa->mahasiswas()
            ->wherePivot('status', 'diterima')
            ->count();

        if ($jumlahDiterima >= $beasiswa->kuota) {
            session()->flash('error', 'Kuota beasiswa sudah penuh, tidak bisa apply.');
            return;
        }

        // 4. Kalau lolos semua cek, maka apply (status pending)
        $mahasiswa->beasiswas()->attach($beasiswaId, ['status' => 'pending']);

        session()->flash('success', 'Berhasil mengajukan beasiswa.');
    }


    public function render()
    {
        return view('livewire.beasiswa.partials.apply-beasiswa-page', [
            'beasiswas' => Beasiswa::all(),
        ]);
    }
}