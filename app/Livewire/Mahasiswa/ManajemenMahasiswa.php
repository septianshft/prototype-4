<?php

namespace App\Livewire\Mahasiswa;

use App\Models\Data_Mahasiswa;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ManajemenMahasiswa extends Component
{
    use WithPagination;

    public $nim, $ipk, $program_studi;
    public $nama_mahasiswa, $email, $role;
    public $data_mahasiswa_id;
    public $isEdit = false;
    public $showModal = false;
    public $search = '';

    public function render()
    {
        return view('livewire.mahasiswa.manajemen-mahasiswa', [
            'mahasiswa' => Data_Mahasiswa::with('user')->get(),
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
        $this->validate([
            'nim' => 'required|string|unique:data_mahasiswa,nim',
            'ipk' => 'required|numeric|min:0|max:4',
            'program_studi' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        $nim = $this->nim ?? 'default-nim';

        Data_Mahasiswa::create([
            'user_id' => $user->id,
            'nama_mahasiswa' => $user->name,
            'nim' => $this->null,
            'ipk' => $this->null,
            'program_studi' => $this->null,
        ]);

        $this->resetInput();
        $this->closeModal();
        session()->flash('message', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $mahasiswa = Data_Mahasiswa::with('user')->findOrFail($id);

        $this->data_mahasiswa_id = $id;
        $this->nim = $mahasiswa->nim;
        $this->ipk = $mahasiswa->ipk;
        $this->program_studi = $mahasiswa->program_studi;

        $this->nama_mahasiswa = $mahasiswa->user->name ?? '';
        $this->email = $mahasiswa->user->email ?? '';
        $this->role = $mahasiswa->user->role ?? '';

        $this->isEdit = true;
        $this->showModal = true;
    }


    public function update()
    {
        $this->validate([
            'nim' => 'required|string|unique:data_mahasiswa,nim,' . $this->data_mahasiswa_id,
            'ipk' => 'required|numeric|min:0|max:4',
            'program_studi' => 'required|string|max:100',
        ]);

        $mahasiswa = Data_Mahasiswa::findOrFail($this->data_mahasiswa_id);
        $mahasiswa->update([
            'nim' => $this->nim,
            'ipk' => $this->ipk,
            'program_studi' => $this->program_studi,
        ]);

        $this->closeModal();
        session()->flash('message', 'Data mahasiswa berhasil diperbarui.');
    }

    public function delete($id)
    {
        $mahasiswa = Data_Mahasiswa::findOrFail($id);

        if ($mahasiswa->user) {
            $mahasiswa->user->delete();
        }

        $mahasiswa->delete();
    }

    public function resetInput()
    {
        $this->nim = '';
        $this->ipk = '';
        $this->program_studi = '';
        $this->data_mahasiswa_id = null;
    }
}
