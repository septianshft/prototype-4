<?php

namespace App\Livewire\Beasiswa\Partials;

use App\Models\Laporan_Beasiswa;
use App\Livewire\Beasiswa\Partials\LaporanBeasiswaTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class LaporanBeasiswaForm extends Component
{
    use WithFileUploads;

    public $nama_laporan;
    public $file;
    public $showModal = false;
    public $isEdit = false;
    public $selectedId;
    protected $listeners = ['editLaporan' => 'editLaporan'];

    protected function rules()
    {
        return [
            'nama_laporan' => 'required|string|min:3',
            'file' => $this->isEdit ? 'nullable|file|mimes:pdf,doc,docx|max:2048' : 'required|file|mimes:pdf,doc,docx|max:2048',
        ];
    }

    public function openModal()
    {
        $mahasiswa = Auth::user()->dataMahasiswa;

        if (! $mahasiswa || $mahasiswa->status_seleksi !== 'diterima') {
            session()->flash('error', 'Kamu belum bisa mengisi laporan karena belum diterima beasiswa.');
            return;
        }

        $this->reset(['nama_laporan', 'file', 'isEdit', 'selectedId']);
        $this->showModal = true;
    }


    public function closeModal()
    {
        $this->showModal = false;
    }

    // store()
    public function store()
    {
        $mahasiswa = Auth::user()->dataMahasiswa;

        if (! $mahasiswa || $mahasiswa->status_seleksi !== 'diterima') {
            session()->flash('error', 'Kamu belum bisa mengirim laporan karena belum diterima beasiswa.');
            return;
        }

        $this->validate();

        $path = $this->file->store('laporan', 'public');

        Laporan_Beasiswa::create([
            'nama_laporan' => $this->nama_laporan,
            'file_path' => $path,
            'beasiswa_id' => $mahasiswa->beasiswa_id,
            'user_id' => Auth::id(),
        ]);

        $this->closeModal();
        session()->flash('message', 'Laporan berhasil disimpan.');
        $this->dispatch('laporanUpdated');
    }


    public function editLaporan($id)
    {
        $laporan = Laporan_Beasiswa::findOrFail($id);
        $this->selectedId = $laporan->id;
        $this->nama_laporan = $laporan->nama_laporan;
        $this->isEdit = true;
        $this->showModal = true;
    }

    // update()
    public function update()
    {
        $this->validate();

        $laporan = Laporan_Beasiswa::findOrFail($this->selectedId);
        $laporan->nama_laporan = $this->nama_laporan;

        if ($this->file) {
            if (Storage::exists($laporan->file_path)) {
                Storage::delete($laporan->file_path);
            }
            $laporan->file_path = $this->file->store('laporan');
        }

        $laporan->save();

        $this->closeModal();
        session()->flash('message', 'Laporan berhasil diperbarui.');

        // GANTI emit DENGAN dispatch
        $this->dispatch('laporanUpdated');
    }

    public function render()
    {
        return view('livewire.beasiswa.partials.laporan-beasiswa-form');
    }
}
