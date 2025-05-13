<?php

namespace App\Livewire\Beasiswa;

use App\Models\Laporan_Beasiswa;
use App\Models\Data_Mahasiswa;
use App\Models\Beasiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class LaporanBeasiswa extends Component
{
    use WithFileUploads, WithPagination;

    public $nama_laporan, $file_path, $beasiswa_id, $data_mahasiswa_id, $laporan_id;
    public $isEdit = false;
    public $showModal = false;

    protected $rules = [
        'nama_laporan' => 'required|string|max:255',
        'file_path' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        'beasiswa_id' => 'required|exists:beasiswa,id',
        'data_mahasiswa_id' => 'required|exists:data_mahasiswa,id',
    ];

    public function render()
    {
        return view('livewire.beasiswa.laporan-beasiswa', [
            'laporans' => Laporan_Beasiswa::with(['beasiswa', 'dataMahasiswa'])->paginate(10),
            'mahasiswas' => Data_Mahasiswa::all(),
            'beasiswas' => Beasiswa::all()
        ]);
    }

    public function openModal()
    {
        $this->resetInput();
        $this->showModal = true;
        $this->isEdit = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function store()
    {
        $this->validate([
            'nama_laporan' => 'required|string|max:255',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Ambil user yang sedang login
        $user = Auth::user();

        // Ambil data_mahasiswa_id dari user
        $mahasiswa = $user->dataMahasiswa;

        if (!$mahasiswa) {
            session()->flash('error', 'Data mahasiswa tidak ditemukan untuk akun ini.');
            return;
        }

        $path = $this->file_path ? $this->file_path->store('laporan', 'public') : null;

        Laporan_Beasiswa::create([
            'nama_laporan' => $this->nama_laporan,
            'file_path' => $path,
            'beasiswa_id' => $this->beasiswa_id,
            'mahasiswa_id' => Auth::user()->dataMahasiswa->id,
        ]);

        $this->resetInput();
        $this->showModal = false;
    }
    public function edit($id)
    {
        $laporan = Laporan_Beasiswa::findOrFail($id);
        $this->laporan_id = $laporan->id;
        $this->nama_laporan = $laporan->nama_laporan;
        $this->beasiswa_id = $laporan->beasiswa_id;
        $this->data_mahasiswa_id = $laporan->data_mahasiswa_id;
        $this->file_path = null;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
        $laporan = Laporan_Beasiswa::findOrFail($this->laporan_id);

        $this->validate();

        $path = $this->file_path ? $this->file_path->store('laporan', 'public') : $laporan->file_path;

        $laporan->update([
            'nama_laporan' => $this->nama_laporan,
            'file_path' => $path,
            'beasiswa_id' => $this->beasiswa_id,
            'data_mahasiswa_id' => $this->data_mahasiswa_id,
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        $laporan = Laporan_Beasiswa::findOrFail($id);
        if ($laporan->file_path && Storage::disk('public')->exists($laporan->file_path)) {
            Storage::disk('public')->delete($laporan->file_path);
        }
        $laporan->delete();
    }

    public function resetInput()
    {
        $this->nama_laporan = '';
        $this->file_path = null;
        $this->beasiswa_id = '';
        $this->data_mahasiswa_id = '';
        $this->laporan_id = null;
    }
}
