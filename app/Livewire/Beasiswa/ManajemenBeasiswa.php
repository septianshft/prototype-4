<?php

namespace App\Livewire\Beasiswa;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Beasiswa;
use App\Models\ApplyBeasiswa;
use App\Models\Data_Mahasiswa;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class ManajemenBeasiswa extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $selectedBeasiswa = null;  // untuk detail apply
    public $role;
    public $showModal = false;
    public $isEdit = false;
    public $deleteId;
    public $deadline_pendaftaran;
    public $program_studi_id;
    public $programStudis = [];
    public $require_file = 0;
    public $persyaratan_file_name = '';
    public $showApplyModal = false;
    public $applyFile;
    public $showApplyDetailModal = false;
    public $applyDetail = null;

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
        'require_file' => 'required|boolean',
        'persyaratan_file_name' => 'required_if:require_file,1|string|max:255',
    ];

    public function updatedRequireFile($value)
    {
        $this->require_file = (int) $value;

        // Optional: reset nama file kalau tidak perlu upload
        if ($this->require_file === 0) {
            $this->persyaratan_file_name = '';
        }
    }


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
        $this->require_file = $beasiswa->require_file;
        $this->persyaratan_file_name = $beasiswa->persyaratan_file_name;


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

    public function showApplyDetail($beasiswaId)
    {
        $apply = ApplyBeasiswa::where('user_id', Auth::id())
            ->where('beasiswa_id', $beasiswaId)
            ->first();

        if (!$apply) {
            session()->flash('error', 'Belum ada pengajuan untuk beasiswa ini.');
            return;
        }

        $this->applyDetail = $apply;
        $this->showApplyDetailModal = true;
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
            'require_file' => $this->require_file,
            'persyaratan_file_name' => $this->persyaratan_file_name,
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
        $this->require_file = 0;
        $this->persyaratan_file_name = '';
    }

    public function resetApplyModal()
    {
        $this->showApplyModal = false;
        $this->selectedBeasiswa = null;
        $this->applyFile = null;
    }

    public function submitApply()
    {
        // Cek lagi biar aman
        $exists = \App\Models\ApplyBeasiswa::where('user_id', Auth::id())
            ->where('beasiswa_id', $this->selectedBeasiswa->id)
            ->where(function ($q) {
                $q->where('status', 'pending')
                    ->orWhere('status', 'diterima');
            })
            ->where('file_status', '!=', 'ditolak')
            ->exists();

        if ($exists) {
            session()->flash('error', 'Kamu sudah mengajukan beasiswa ini sebelumnya.');
            $this->resetApplyModal();
            return;
        }

        // Jika require file, wajib isi applyFile
        if ($this->selectedBeasiswa->require_file && !$this->applyFile) {
            session()->flash('error', 'Harap upload file persyaratan.');
            return;
        }

        // Simpan Apply
        $apply = new \App\Models\ApplyBeasiswa();
        $apply->user_id = Auth::id();
        $apply->beasiswa_id = $this->selectedBeasiswa->id;
        $apply->status = 'pending';

        // Simpan file jika ada
        if ($this->selectedBeasiswa->require_file && $this->applyFile) {
            $path = $this->applyFile->store('uploads/persyaratan', 'public');
            $apply->file_persyaratan_path = $path;
        }

        $apply->save();

        session()->flash('success', 'Berhasil mengajukan beasiswa.');
        $this->resetApplyModal();
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

        if ($mahasiswaPS->id !== $beasiswaPS->id || $mahasiswaPS->jenjang !== $beasiswaPS->jenjang) {
            session()->flash('error', 'Program studi atau jenjang Anda tidak cocok dengan beasiswa ini.');
            return;
        }

        // BENAR:
        $exists = ApplyBeasiswa::where('user_id', Auth::id())
            ->where('beasiswa_id', $beasiswaId)
            ->where(function ($q) {
                $q->where('status', 'pending')
                    ->orWhere('status', 'diterima');
            })
            ->where('file_status', '!=', 'ditolak')
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

        // Jika semua valid, simpan selectedBeasiswa & buka modal
        $this->selectedBeasiswa = $beasiswa;

        if ($this->selectedBeasiswa->require_file) {
            // Kalau butuh upload, tampilkan modal upload
            $this->showApplyModal = true;
            $this->applyFile = null;
        } else {
            // Tidak perlu upload, langsung apply
            $this->submitApply();
        }
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
            ->where(function ($q) {
                $q->where('status', 'pending')
                    ->orWhere('status', 'diterima');
            })
            ->where('file_status', '!=', 'ditolak')
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
            'require_file' => $this->require_file,
            'persyaratan_file_name' => $this->persyaratan_file_name,

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
