<?php

namespace App\Livewire\Beasiswa;

use App\Models\Beasiswa;
use Livewire\Component;
use Livewire\WithPagination;

class ManajemenBeasiswa extends Component
{
    use WithPagination;

    public $nama_beasiswa, $nama_penyelenggara, $periode, $kuota, $deskripsi;
    public $beasiswa_id;
    public $isEdit = false;
    public $showModal = false;
    public $search = '';

    protected $rules = [
        'nama_beasiswa' => 'required|string|max:255',
        'nama_penyelenggara' => 'required|string|max:255',
        'periode' => 'required|date',
        'kuota' => 'required|numeric|min:0|max:1000',
        'deskripsi' => 'nullable|string',
    ];

    public function render()
    {
        $data = Beasiswa::where('nama_beasiswa', 'like', '%' . $this->search . '%')->paginate(10);
        return view('livewire.beasiswa.manajemen-beasiswa', [
            'beasiswas' => $data
        ]);
    }

    public function openModal()
    {
        $this->resetInput();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
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
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $beasiswa = Beasiswa::findOrFail($id);
        $this->beasiswa_id = $id;
        $this->nama_beasiswa = $beasiswa->nama_beasiswa;
        $this->nama_penyelenggara = $beasiswa->nama_penyelenggara;
        $this->periode = $beasiswa->periode;
        $this->kuota = $beasiswa->kuota;
        $this->deskripsi = $beasiswa->deskripsi;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $beasiswa = Beasiswa::findOrFail($this->beasiswa_id);
        $beasiswa->update([
            'nama_beasiswa' => $this->nama_beasiswa,
            'nama_penyelenggara' => $this->nama_penyelenggara,
            'periode' => $this->periode,
            'kuota' => $this->kuota,
            'deskripsi' => $this->deskripsi,
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        Beasiswa::findOrFail($id)->delete();
    }


    public function resetInput()
    {
        $this->nama_beasiswa = '';
        $this->nama_penyelenggara = '';
        $this->periode = '';
        $this->kuota = '';
        $this->deskripsi = '';
        $this->beasiswa_id = null;
    }
}
