<?php

namespace App\Livewire\Beasiswa;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Beasiswa;
use App\Models\ApplyBeasiswa;
use App\Models\Data_Mahasiswa;
use App\Models\ProgramStudi;
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
    public $deadline_pendaftaran;
    public $program_studi_id;
    public $programStudis = [];


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
        'deadline_pendaftaran' => 'required|date',
        'program_studi_id' => 'required|exists:program_studi,id',
    ];


    public function mount()
    {
        $this->role = Auth::user()->role;

        $this->programStudis = ProgramStudi::orderBy('program_studi')->get();

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
        $this->deadline_pendaftaran = $beasiswa->deadline_pendaftaran ? $beasiswa->deadline_pendaftaran->format('Y-m-d') : null;
        $this->program_studi_id = $beasiswa->program_studi_id;

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
            'deadline_pendaftaran' => $this->deadline_pendaftaran,
            'program_studi_id' => $this->program_studi_id,
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
        $this->deadline_pendaftaran = null;
        $this->program_studi_id = null;
    }


    public function apply($beasiswaId)
    {
        if ($this->role !== 'mahasiswa') {
            session()->flash('error', 'Hanya mahasiswa yang bisa apply.');
            return;
        }

        $beasiswa = Beasiswa::with('programStudi')->find($beasiswaId);
        if (!$beasiswa) {
            session()->flash('error', 'Beasiswa tidak ditemukan.');
            return;
        }

        if ($beasiswa->deadline_pendaftaran && now()->gt($beasiswa->deadline_pendaftaran)) {
            session()->flash('error', 'Pendaftaran beasiswa sudah ditutup.');
            return;
        }

        $mahasiswa = Data_Mahasiswa::where('user_id', Auth::id())->with('programStudi')->first();
        if (!$mahasiswa) {
            session()->flash('error', 'Data mahasiswa tidak ditemukan.');
            return;
        }

        $mahasiswaPS = $mahasiswa->programStudi;  
        $beasiswaPS = $beasiswa->programStudi;    

        if (!$mahasiswaPS || !$beasiswaPS) {
            session()->flash('error', 'Program studi tidak ditemukan.');
            return;
        }

        if ($mahasiswaPS->id !== $beasiswaPS->id) {
            if ($mahasiswaPS->jenjang === $beasiswaPS->jenjang) {
                session()->flash('error', 'Program studi Anda tidak cocok dengan beasiswa ini.');
                return;
            }
        }

        if ($mahasiswaPS->id === $beasiswaPS->id && $mahasiswaPS->jenjang !== $beasiswaPS->jenjang) {
            session()->flash('error', 'Jenjang program studi Anda tidak cocok dengan beasiswa ini.');
            return;
        }

        if ($mahasiswa->status_seleksi === 'diterima') {
            session()->flash('error', 'Kamu sudah diterima beasiswa, tidak bisa apply lagi.');
            return;
        }

        $exists = ApplyBeasiswa::where('user_id', Auth::id())
            ->where('beasiswa_id', $beasiswaId)
            ->exists();

        if ($exists) {
            session()->flash('error', 'Kamu sudah mengajukan beasiswa ini sebelumnya.');
            return;
        }

        $jumlahDiterima = ApplyBeasiswa::where('beasiswa_id', $beasiswaId)
            ->where('status', 'diterima')
            ->count();

        if ($jumlahDiterima >= $beasiswa->kuota) {
            session()->flash('error', 'Kuota beasiswa sudah penuh.');
            return;
        }

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

        $dosenId = null;

        $dosen = \App\Models\User::where('name', $this->nama_penyelenggara)
            ->where('role', 'dosen')
            ->first();

        if ($dosen) {
            $dosenId = $dosen->id;
        }

        Beasiswa::create([
            'nama_beasiswa' => $this->nama_beasiswa,
            'nama_penyelenggara' => $this->nama_penyelenggara,
            'periode' => $this->periode,
            'kuota' => $this->kuota,
            'deskripsi' => $this->deskripsi,
            'deadline_pendaftaran' => $this->deadline_pendaftaran,
            'dosen_id' => $dosenId,
            'program_studi_id' => $this->program_studi_id,
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
