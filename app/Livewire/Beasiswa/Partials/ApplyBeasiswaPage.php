<?php

namespace App\Livewire\Beasiswa\Partials;

use App\Models\ApplyBeasiswa;
use App\Models\Beasiswa;
use App\Models\Data_Mahasiswa;
use App\Models\Laporan_Beasiswa;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ApplyBeasiswaPage extends Component
{
    public $laporan;
    public $feedback;

    public function mount($id)
    {
        $laporan = Laporan_Beasiswa::with(['user.dataMahasiswa', 'beasiswa'])->findOrFail($id);

        if (!$laporan->beasiswa) {
            $apply = ApplyBeasiswa::where('user_id', $laporan->user_id)
                ->where('status', 'diterima')
                ->first();

            if ($apply) {
                $laporan->setRelation('beasiswa', $apply->beasiswa);
                $laporan->beasiswa_id = $apply->beasiswa_id; 
            }
        }

        if (Auth::user()->role === 'mahasiswa' && $laporan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan melihat laporan ini.');
        }

        $this->laporan = $laporan;
        $this->feedback = $laporan->feedback;
    }

    public function apply($beasiswaId)
    {
        $mahasiswa = Data_Mahasiswa::where('user_id', Auth::id())->first();

        if (!$mahasiswa) {
            session()->flash('error', 'Data mahasiswa tidak ditemukan.');
            return;
        }

        $sudahDiterima = $mahasiswa->beasiswas()
            ->wherePivot('status', 'diterima')
            ->exists();

        if ($sudahDiterima) {
            session()->flash('error', 'Kamu sudah diterima beasiswa lain, tidak bisa apply beasiswa baru.');
            return;
        }

        if ($mahasiswa->beasiswas()->wherePivot('beasiswa_id', $beasiswaId)->exists()) {
            session()->flash('error', 'Kamu sudah mengajukan beasiswa ini sebelumnya.');
            return;
        }

        $beasiswa = Beasiswa::findOrFail($beasiswaId);

        $jumlahDiterima = $beasiswa->mahasiswas()
            ->wherePivot('status', 'diterima')
            ->count();

        if ($jumlahDiterima >= $beasiswa->kuota) {
            session()->flash('error', 'Kuota beasiswa sudah penuh, tidak bisa apply.');
            return;
        }

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
