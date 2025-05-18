<?php

namespace App\Livewire\Beasiswa;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Beasiswa;
use App\Models\ApplyBeasiswa;
use App\Models\Data_Mahasiswa;
use Illuminate\Support\Facades\Auth;

class ManajemenBeasiswa extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedBeasiswa = null;  // untuk detail apply
    public $role;
    public $showModal = false;
    public $isEdit = false;
    public $deleteId;

    // Form fields (contoh untuk create/update)
    public $beasiswaId;
    public $nama_beasiswa;
    public $nama_penyelenggara;
    public $periode;
    public $kuota;
    public $deskripsi;

    protected $rules = [
        'nama_beasiswa' => 'required|string|max:255',
        'nama_penyelenggara' => 'required|string|max:255',
        'periode' => 'required|string|max:255',
        'kuota' => 'required|integer',
        'deskripsi' => 'nullable|string',
    ];

    public function mount()
    {
        $this->role = Auth::user()->role;

        // Jika role dosen, otomatis isi penyelenggara dengan nama user
        if ($this->role === 'dosen') {
            $this->nama_penyelenggara = Auth::user()->name;
        }
    }

    public function render()
    {
        $user = Auth::user();

        $query = Beasiswa::query()
            ->where('nama_beasiswa', 'like', '%' . $this->search . '%');

        // Filter khusus untuk dosen
        if ($user->role === 'dosen') {
            $query->where('dosen_id', $user->id);
        }

        // Jika perlu: Mahasiswa hanya bisa lihat yang statusnya open
        if ($user->role === 'mahasiswa') {
            $query->where('status', 'open');
        }

        $data = $query->paginate(10);

        return view('livewire.beasiswa.manajemen-beasiswa', [
            'beasiswas' => $data,
            'role' => $this->role,
        ]);
    }


    // CRUD admin/dosen
    public function edit($id)
    {
        $beasiswa = Beasiswa::findOrFail($id);
        $this->beasiswaId = $beasiswa->id;
        $this->nama_beasiswa = $beasiswa->nama_beasiswa;
        $this->nama_penyelenggara = $beasiswa->nama_penyelenggara;
        $this->periode = $beasiswa->periode;
        $this->kuota = $beasiswa->kuota;
        $this->deskripsi = $beasiswa->deskripsi;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function openModal()
    {
        $this->resetForm();
        $this->resetValidation();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }


    public function update()
    {
        $this->validate();

        $beasiswa = Beasiswa::findOrFail($this->beasiswaId);
        $beasiswa->update([
            'nama_beasiswa' => $this->nama_beasiswa,
            'nama_penyelenggara' => $this->nama_penyelenggara,
            'periode' => $this->periode,
            'kuota' => $this->kuota,
            'deskripsi' => $this->deskripsi,
        ]);

        $this->resetForm();
        $this->closeModal();
        session()->flash('message', 'Beasiswa berhasil diupdate!');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('showModal', [
            'title' => 'Hapus Data Beasiswa',
            'message' => 'Apakah Anda yakin ingin menghapus beasiswa ini?',
            'confirmText' => 'Hapus',
            'cancelText' => 'Batal',
            'onConfirm' => 'deleteConfirmed',
        ]);
    }

    #[\Livewire\Attributes\On('deleteConfirmed')]
    public function deleteConfirmed()
    {
        Beasiswa::findOrFail($this->deleteId)->delete();
        session()->flash('message', 'Beasiswa berhasil dihapus!');
        $this->deleteId = null;
    }

    public function resetForm()
    {
        $this->beasiswaId = null;
        $this->nama_beasiswa = '';
        $this->nama_penyelenggara = $this->role === 'dosen' ? Auth::user()->name : '';
        $this->periode = '';
        $this->kuota = '';
        $this->deskripsi = '';
    }

    // Apply beasiswa mahasiswa
    public function apply($beasiswaId)
    {
        if ($this->role !== 'mahasiswa') {
            session()->flash('error', 'Hanya mahasiswa yang bisa apply.');
            return;
        }

        $mahasiswa = Data_Mahasiswa::where('user_id', Auth::id())->first();

        if (!$mahasiswa) {
            session()->flash('error', 'Data mahasiswa tidak ditemukan.');
            return;
        }

        // Cek apakah sudah diterima beasiswa sebelumnya
        if ($mahasiswa->status_seleksi === 'diterima') {
            session()->flash('error', 'Kamu sudah diterima beasiswa, tidak bisa apply lagi.');
            return;
        }

        // Cek apakah sudah apply beasiswa ini
        $exists = ApplyBeasiswa::where('user_id', Auth::id())
            ->where('beasiswa_id', $beasiswaId)
            ->exists();

        if ($exists) {
            session()->flash('error', 'Kamu sudah mengajukan beasiswa ini sebelumnya.');
            return;
        }

        // Cek kuota beasiswa
        $beasiswa = Beasiswa::find($beasiswaId);
        if (!$beasiswa) {
            session()->flash('error', 'Beasiswa tidak ditemukan.');
            return;
        }

        $jumlahDiterima = ApplyBeasiswa::where('beasiswa_id', $beasiswaId)
            ->where('status', 'diterima')
            ->count();

        if ($jumlahDiterima >= $beasiswa->kuota) {
            session()->flash('error', 'Kuota beasiswa sudah penuh.');
            return;
        }

        // Apply beasiswa (status pending)
        ApplyBeasiswa::create([
            'user_id' => Auth::id(),
            'beasiswa_id' => $beasiswaId,
            'status' => 'pending',
        ]);

        session()->flash('success', 'Berhasil mengajukan beasiswa, tunggu persetujuan.');
    }

    public function pilih()
    {
        if (!$this->selectedBeasiswa) {
            session()->flash('error', 'Beasiswa tidak ditemukan');
            return;
        }

        // Cek apakah sudah apply sebelumnya
        $exists = ApplyBeasiswa::where('user_id', Auth::id())
            ->where('beasiswa_id', $this->selectedBeasiswa->id)
            ->exists();

        if ($exists) {
            session()->flash('error', 'Anda sudah mengajukan beasiswa ini.');
        } else {
            ApplyBeasiswa::create([
                'user_id' => Auth::id(),
                'beasiswa_id' => $this->selectedBeasiswa->id,
            ]);
            session()->flash('message', 'Beasiswa berhasil diajukan!');
        }

        $this->selectedBeasiswa = null;
    }

    public function batal()
    {
        $this->selectedBeasiswa = null;
    }

    public function store()
    {
        $this->validate();

        Beasiswa::create([
            'nama_beasiswa' => $this->nama_beasiswa,
            'nama_penyelenggara' => $this->nama_penyelenggara,
            'periode' => $this->periode,
            'kuota' => $this->kuota,
            'deskripsi' => $this->deskripsi,
            'dosen_id' => Auth::id(),
        ]);

        $this->resetForm();
        $this->closeModal();
        session()->flash('message', 'Beasiswa berhasil ditambahkan!');
    }

    public function create()
    {
        $this->openModal();
    }
}
