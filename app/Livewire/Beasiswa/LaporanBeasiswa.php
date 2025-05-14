<?php

namespace App\Livewire\Beasiswa;

use App\Models\Laporan_Beasiswa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class LaporanBeasiswa extends Component
{
    use WithFileUploads, WithPagination;

    public $nama_laporan;
    public $file;
    public $showModal = false;
    public $isEdit = false;
    public $selectedId;


    protected $rules = [
        'nama_laporan' => 'required|string|min:3',
        'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
    ];

    public function store()
    {
        $this->validate();

        $filePath = $this->file->store('laporan');

        Laporan_Beasiswa::create([
            'nama_laporan' => $this->nama_laporan,
            'file_path' => $filePath,
            'user_id' => Auth::id(),
        ]);

        $this->reset(['nama_laporan', 'file', 'showModal', 'isEdit']);
        session()->flash('message', 'Laporan berhasil disimpan.');
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function delete($id)
    {
        $laporan = Laporan_Beasiswa::findOrFail($id);

        // Hapus file fisik
        if ($laporan->file_path && Storage::exists($laporan->file_path)) {
            Storage::delete($laporan->file_path);
        }

        $laporan->delete();

        session()->flash('message', 'Laporan berhasil dihapus.');
    }

    public function edit($id)
    {
        $this->resetErrorBag(); // bersihkan error sebelumnya
        $this->isEdit = true;
        $this->showModal = true;

        $laporan = Laporan_Beasiswa::findOrFail($id);
        $this->selectedId = $id;
        $this->nama_laporan = $laporan->nama_laporan;
    }


    public function update()
    {
        $this->validate([
            'nama_laporan' => 'required|string|min:3',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $laporan = Laporan_Beasiswa::findOrFail($this->selectedId);
        $laporan->nama_laporan = $this->nama_laporan;

        if ($this->file) {
            if ($laporan->file_path && Storage::exists($laporan->file_path)) {
                Storage::delete($laporan->file_path);
            }

            $filePath = $this->file->store('laporan');
            $laporan->file_path = $filePath;
        }

        $laporan->save();

        $this->reset(['nama_laporan', 'file', 'selectedId', 'isEdit', 'showModal']);
        session()->flash('message', 'Laporan berhasil diperbarui.');
    }



    public function render()
    {
        $userId = Auth::id();

        $laporans = Laporan_Beasiswa::with('user')
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);

        return view('livewire.beasiswa.laporan-beasiswa', [
            'laporans' => $laporans
        ]);
    }
}
