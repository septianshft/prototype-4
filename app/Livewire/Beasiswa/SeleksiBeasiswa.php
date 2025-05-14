<?php

namespace App\Livewire\Beasiswa;

use App\Models\Data_Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SeleksiBeasiswa extends Component
{
    public function accept($id)
    {
        if (Auth::user()->role === 'dosen') {
            Data_Mahasiswa::find($id)?->update(['status_seleksi' => 'diterima']);
        }
    }

    public function reject($id)
    {
        if (Auth::user()->role === 'dosen') {
            Data_Mahasiswa::find($id)?->update(['status_seleksi' => 'ditolak']);
        }
    }

    public function render()
    {
        return view('livewire.beasiswa.seleksi-beasiswa', [
            'mahasiswa' => Data_Mahasiswa::all(),
            'role' => Auth::user()->role
        ]);
    }
}
