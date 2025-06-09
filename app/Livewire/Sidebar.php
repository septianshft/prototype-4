<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Laporan_Beasiswa;

class Sidebar extends Component
{
    public function render()
    {
        $jumlahPendingLaporan = Laporan_Beasiswa::where('status_acc', 'pending')->count();

        return view('livewire.sidebar', [
            'jumlahPendingLaporan' => $jumlahPendingLaporan
        ]);
    }
}
