<?php

namespace App\Livewire\Beasiswa\partials;

use App\Models\Laporan_Beasiswa;
use Livewire\Component;

class LaporanBeasiswaShow extends Component
{
    public $laporan;

    public function mount($id)
    {
        // Pastikan Laporan_Beasiswa ada dan relasi user, beasiswa valid
        $this->laporan = Laporan_Beasiswa::with(['user', 'beasiswa'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.beasiswa.partials.laporan-beasiswa-show');
    }
}
