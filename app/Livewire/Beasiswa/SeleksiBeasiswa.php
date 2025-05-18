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
        $user = Auth::user();

        if (!in_array($user->role, ['dosen', 'admin'])) {
            session()->flash('error', 'Hanya dosen atau admin yang dapat melakukan aksi ini.');
            return;
        }

        $apply = ApplyBeasiswa::with('beasiswa')->find($applyId);
        if (!$apply || !$apply->beasiswa) {
            session()->flash('error', 'Data tidak ditemukan.');
            return;
        }

        if ($user->role === 'dosen' && $apply->beasiswa->dosen_id !== $user->id) {
            session()->flash('error', 'Anda tidak memiliki akses ke beasiswa ini.');
            return;
        }

        if ($apply->status !== 'pending') {
            session()->flash('error', 'Status sudah ditentukan sebelumnya.');
            return;
        }

        $jumlahDiterima = ApplyBeasiswa::where('beasiswa_id', $apply->beasiswa_id)
            ->where('status', 'diterima')->count();

        if ($jumlahDiterima >= $apply->beasiswa->kuota) {
            session()->flash('error', 'Kuota beasiswa sudah penuh.');
            return;
        }

        $apply->update(['status' => 'diterima']);

        // Update status_seleksi di data_mahasiswa
        Data_Mahasiswa::where('user_id', $apply->user_id)
            ->update(['status_seleksi' => 'diterima']);

        // Periksa apakah kuota sudah penuh
        $jumlahDiterimaBaru = ApplyBeasiswa::where('beasiswa_id', $apply->beasiswa_id)
            ->where('status', 'diterima')->count();

        if ($jumlahDiterimaBaru >= $apply->beasiswa->kuota) {
            $apply->beasiswa->update(['status' => 'full']);
        }

        session()->flash('success', 'Mahasiswa berhasil diterima.');
        $this->dispatch('refreshComponent');
    }

    public function reject($applyId)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['dosen', 'admin'])) {
            session()->flash('error', 'Hanya dosen atau admin yang dapat melakukan aksi ini.');
            return;
        }

        $apply = ApplyBeasiswa::with('beasiswa')->find($applyId);

        if (!$apply) {
            session()->flash('error', 'Data tidak ditemukan.');
            return;
        }

        if ($user->role === 'dosen' && $apply->beasiswa->dosen_id !== $user->id) {
            session()->flash('error', 'Anda tidak memiliki akses ke beasiswa ini.');
            return;
        }

        if ($apply->status !== 'pending') {
            session()->flash('error', 'Status sudah ditentukan sebelumnya.');
            return;
        }

        $apply->update(['status' => 'ditolak']);

        Data_Mahasiswa::where('user_id', $apply->user_id)
            ->update(['status_seleksi' => 'ditolak']);

        session()->flash('success', 'Pengajuan ditolak.');
        $this->dispatch('refreshComponent');
    }

    public function render()
    {
        $user = Auth::user();

        // Untuk mahasiswa: ambil dari data_mahasiswa sendiri
        if ($user->role === 'mahasiswa') {
            $query = Data_Mahasiswa::query()
                ->join('apply_beasiswa', 'apply_beasiswa.user_id', '=', 'data_mahasiswa.user_id')
                ->join('beasiswa', 'beasiswa.id', '=', 'apply_beasiswa.beasiswa_id')
                ->where('data_mahasiswa.user_id', $user->id)
                ->select(
                    'data_mahasiswa.nama_mahasiswa',
                    'data_mahasiswa.nim',
                    'data_mahasiswa.ipk',
                    'data_mahasiswa.program_studi',
                    'data_mahasiswa.status_seleksi',
                    'apply_beasiswa.status as apply_status',
                    'apply_beasiswa.id as apply_id',
                    'apply_beasiswa.beasiswa_id'
                );

            if ($this->sortStatus !== 'all') {
                $query->where('data_mahasiswa.status_seleksi', $this->sortStatus);
            }

            $pendaftar = $query->get();
        }
        // Untuk dosen dan admin: ambil semua pendaftar sesuai akses
        else {
            $query = ApplyBeasiswa::query()
                ->with(['beasiswa', 'user'])
                ->join('data_mahasiswa', 'data_mahasiswa.user_id', '=', 'apply_beasiswa.user_id')
                ->join('beasiswa', 'beasiswa.id', '=', 'apply_beasiswa.beasiswa_id')
                ->select(
                    'apply_beasiswa.id as apply_id',
                    'apply_beasiswa.status',
                    'data_mahasiswa.nama_mahasiswa',
                    'data_mahasiswa.nim',
                    'data_mahasiswa.ipk',
                    'data_mahasiswa.program_studi',
                    'data_mahasiswa.status_seleksi',
                    'apply_beasiswa.user_id',
                    'apply_beasiswa.beasiswa_id'
                );

            if ($user->role === 'dosen') {
                $query->where('beasiswa.dosen_id', $user->id);
            }

            if ($this->sortStatus !== 'all') {
                $query->where('data_mahasiswa.status_seleksi', $this->sortStatus);
            }

            $pendaftar = $query->get();
        }

        return view('livewire.beasiswa.seleksi-beasiswa', [
            'pendaftar' => $pendaftar,
            'role' => $user->role,
        ]);
    }
}
