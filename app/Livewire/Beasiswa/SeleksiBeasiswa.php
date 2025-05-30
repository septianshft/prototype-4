<?php

namespace App\Livewire\Beasiswa;

use App\Models\ApplyBeasiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SeleksiBeasiswa extends Component
{
    protected $listeners = ['refreshComponent' => '$refresh'];
    public $sortStatus = 'all';
    public $search = '';

    public function updatedSearch()
    {
        // Force refresh query when search updated
    }

    public function updatedSortStatus()
    {
        // Livewire akan otomatis re-render
    }

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

        $apply->update(['status' => 'ditolak']);

        session()->flash('success', 'Pengajuan ditolak.');
        $this->dispatch('refreshComponent');
    }

    public function render()
    {
        $user = Auth::user();

        // === ROLE MAHASISWA ===
        if ($user->role === 'mahasiswa') {
            $query = ApplyBeasiswa::query()
                ->leftJoin('beasiswa', 'beasiswa.id', '=', 'apply_beasiswa.beasiswa_id')
                ->leftJoin('data_mahasiswa', 'data_mahasiswa.user_id', '=', 'apply_beasiswa.user_id')
                ->leftJoin('program_studi', 'program_studi.id', '=', 'data_mahasiswa.program_studi_id')
                ->where('apply_beasiswa.user_id', $user->id)
                ->select(
                    'apply_beasiswa.id as apply_id',
                    'apply_beasiswa.status', // TANPA alias apply_status, supaya seragam
                    'data_mahasiswa.nama_mahasiswa',
                    'data_mahasiswa.nim',
                    'data_mahasiswa.ipk',
                    'program_studi.program_studi as nama_program_studi',
                    'beasiswa.nama_beasiswa',
                    'apply_beasiswa.user_id',
                    'apply_beasiswa.beasiswa_id'
                );

            if ($this->sortStatus !== 'all') {
                $query->where('apply_beasiswa.status', $this->sortStatus);
            }

            if (!empty($this->search)) {
                $query->where(function ($q) {
                    $q->where('apply_beasiswa.status', 'like', "%{$this->search}%")
                        ->orWhere('data_mahasiswa.nama_mahasiswa', 'like', "%{$this->search}%")
                        ->orWhere('data_mahasiswa.nim', 'like', "%{$this->search}%")
                        ->orWhere('program_studi.program_studi', 'like', "%{$this->search}%")
                        ->orWhere('beasiswa.nama_beasiswa', 'like', "%{$this->search}%");
                });
            }

            $pendaftar = $query->paginate(10);

            return view('livewire.beasiswa.seleksi-beasiswa', [
                'pendaftar' => $pendaftar,
                'role' => $user->role,
            ]);
        }

        // === ROLE DOSEN / ADMIN ===
        $query = ApplyBeasiswa::query()
            ->leftJoin('beasiswa', 'beasiswa.id', '=', 'apply_beasiswa.beasiswa_id')
            ->leftJoin('data_mahasiswa', 'data_mahasiswa.user_id', '=', 'apply_beasiswa.user_id')
            ->leftJoin('program_studi', 'program_studi.id', '=', 'data_mahasiswa.program_studi_id')
            ->select(
                'apply_beasiswa.id as apply_id',
                'apply_beasiswa.status',
                'data_mahasiswa.nama_mahasiswa',
                'data_mahasiswa.nim',
                'data_mahasiswa.ipk',
                'program_studi.program_studi as nama_program_studi',
                'beasiswa.nama_beasiswa',
                'apply_beasiswa.user_id',
                'apply_beasiswa.beasiswa_id'
            );

        if ($user->role === 'dosen') {
            $query->where('beasiswa.dosen_id', $user->id);
        }

        if ($this->sortStatus !== 'all') {
            $query->where('apply_beasiswa.status', $this->sortStatus);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('apply_beasiswa.status', 'like', "%{$this->search}%")
                    ->orWhere('data_mahasiswa.nama_mahasiswa', 'like', "%{$this->search}%")
                    ->orWhere('data_mahasiswa.nim', 'like', "%{$this->search}%")
                    ->orWhere('program_studi.program_studi', 'like', "%{$this->search}%")
                    ->orWhere('beasiswa.nama_beasiswa', 'like', "%{$this->search}%");
            });
        }

        $pendaftar = $query->paginate(10);

        foreach ($pendaftar as $item) {
            $item->hasAccepted = ApplyBeasiswa::where('user_id', $item->user_id)
                ->where('status', 'diterima')
                ->exists();
        }

        return view('livewire.beasiswa.seleksi-beasiswa', [
            'pendaftar' => $pendaftar,
            'role' => $user->role,
        ]);
    }
}
