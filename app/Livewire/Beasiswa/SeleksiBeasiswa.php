<?php

namespace App\Livewire\Beasiswa;

use App\Models\ApplyBeasiswa;
use App\Models\Data_Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SeleksiBeasiswa extends Component
{
    protected $listeners = ['refreshComponent' => '$refresh'];
    public $sortStatus = 'all';

    public function accept($applyId)
    {
        if (Auth::user()->role !== 'dosen') {
            session()->flash('error', 'Hanya dosen yang dapat melakukan aksi ini.');
            return;
        }

        $apply = ApplyBeasiswa::with('beasiswa')->find($applyId);

        if (!$apply || !$apply->beasiswa) {
            session()->flash('error', 'Data apply beasiswa tidak ditemukan.');
            return;
        }

        $beasiswa = $apply->beasiswa;

        if ($beasiswa->dosen_id !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki akses ke beasiswa ini.');
            return;
        }

        // Cek kuota
        $jumlahDiterima = ApplyBeasiswa::where('beasiswa_id', $beasiswa->id)
            ->where('status', 'diterima')
            ->count();

        if ($jumlahDiterima >= $beasiswa->kuota) {
            session()->flash('error', 'Kuota beasiswa sudah penuh.');
            return;
        }

        // Update status apply beasiswa jadi diterima
        $apply->update(['status' => 'diterima']);

        // Update status mahasiswa
        $mahasiswa = Data_Mahasiswa::where('user_id', $apply->user_id)->first();
        if ($mahasiswa) {
            $mahasiswa->status_seleksi = 'diterima';
            $mahasiswa->save();
        }

        // Jika kuota sudah penuh, update status beasiswa
        $jumlahDiterimaBaru = ApplyBeasiswa::where('beasiswa_id', $beasiswa->id)
            ->where('status', 'diterima')
            ->count();

        if ($jumlahDiterimaBaru >= $beasiswa->kuota) {
            $beasiswa->update(['status' => 'full']);
        }

        session()->flash('success', 'Mahasiswa berhasil diterima.');

        $this->dispatch('refreshComponent');
    }



    public function reject($applyId)
    {
        if (Auth::user()->role !== 'dosen') {
            session()->flash('error', 'Hanya dosen yang dapat melakukan aksi ini.');
            return;
        }

        $apply = ApplyBeasiswa::find($applyId);

        if (!$apply) {
            session()->flash('error', 'Data apply beasiswa tidak ditemukan.');
            return;
        }

        $apply->update(['status' => 'ditolak']);

        session()->flash('success', 'Pengajuan beasiswa mahasiswa ditolak.');

        $this->dispatch('refreshComponent');
    }

    public function render()
    {

        $user = Auth::user();

        $query = ApplyBeasiswa::query()
            ->with('beasiswa', 'user')
            ->join('data_mahasiswa', 'data_mahasiswa.user_id', '=', 'apply_beasiswa.user_id')
            ->join('beasiswa', 'beasiswa.id', '=', 'apply_beasiswa.beasiswa_id')
            ->select(
                'apply_beasiswa.id as apply_id',
                'apply_beasiswa.status',
                'data_mahasiswa.nama_mahasiswa',
                'data_mahasiswa.nim',
                'data_mahasiswa.ipk',
                'data_mahasiswa.program_studi',
                'apply_beasiswa.user_id',
                'apply_beasiswa.beasiswa_id'
            );

        // Filter role
        if ($user->role === 'dosen') {
            $query->where('beasiswa.dosen_id', $user->id);
        } elseif ($user->role === 'mahasiswa') {
            $query->where('apply_beasiswa.user_id', $user->id);
        }

        // ✅ Filter status
        if ($this->sortStatus !== 'all') {
            $query->where('apply_beasiswa.status', '=', $this->sortStatus);
        }   

        $pendaftar = $query->get();

        return view('livewire.beasiswa.seleksi-beasiswa', [
            'pendaftar' => $pendaftar,
            'role' => $user->role,
        ]);
    }
}
