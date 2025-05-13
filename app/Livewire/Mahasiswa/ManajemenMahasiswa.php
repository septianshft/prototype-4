<?php

namespace App\Livewire\Mahasiswa;

use App\Models\Data_Mahasiswa;
use Livewire\Component;
use Livewire\WithPagination;

class ManajemenMahasiswa extends Component
{
    use WithPagination;

    public $nama_mahasiswa, $nim, $ipk, $email, $role, $program_studi;
    public $data_mahasiswa_id;
    public $isEdit = false;
    public $showModal = false;
    public $search = '';

    protected $rules = [
        'nama_mahasiswa' => 'required|string|max:255',
        'nim' => 'required|unique:data_mahasiswa,nim',
        'ipk' => 'required|numeric|min:0|max:4',
        'email' => 'required|email',
        'role' => 'required|string|max:50',
        'program_studi' => 'required|string|max:100',
    ];

    public function render()
    {
        return view('livewire.mahasiswa.manajemen-mahasiswa', [
            'mahasiswa' => Data_Mahasiswa::where('nama_mahasiswa', 'like', '%' . $this->search . '%')->paginate(10)
        ]);
    }

    public function openModal()
    {
        $this->resetInput();
        $this->resetValidation();
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

        Data_Mahasiswa::create([
            'nama_mahasiswa' => $this->nama_mahasiswa,
            'nim' => $this->nim,
            'ipk' => $this->ipk,
            'email' => $this->email,
            'role' => $this->role,
            'program_studi' => $this->program_studi,
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $mahasiswa = Data_Mahasiswa::findOrFail($id);
        $this->data_mahasiswa_id = $id;
        $this->nama_mahasiswa = $mahasiswa->nama_mahasiswa;
        $this->nim = $mahasiswa->nim;
        $this->ipk = $mahasiswa->ipk;
        $this->email = $mahasiswa->email;
        $this->role = $mahasiswa->role;
        $this->program_studi = $mahasiswa->program_studi;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $data = Data_Mahasiswa::findOrFail($this->data_mahasiswa_id);
        $data->update([
            'nama_mahasiswa' => $this->nama_mahasiswa,
            'nim' => $this->nim,
            'ipk' => $this->ipk,
            'email' => $this->email,
            'role' => $this->role,
            'program_studi' => $this->program_studi,
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        Data_Mahasiswa::findOrFail($id)->delete();
    }

    public function resetInput()
    {
        $this->nama_mahasiswa = '';
        $this->nim = '';
        $this->ipk = '';
        $this->email = '';
        $this->role = '';
        $this->program_studi = '';
        $this->data_mahasiswa_id = null;
    }
}
