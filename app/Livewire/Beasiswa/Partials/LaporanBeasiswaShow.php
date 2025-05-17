<?php

// LaporanBeasiswaShow.php

namespace App\Livewire\Beasiswa\partials;

use App\Models\Laporan_Beasiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LaporanBeasiswaShow extends Component
{
    public $laporan;
    public $feedback;

    public function mount($id)
    {
        $this->laporan = Laporan_Beasiswa::with(['user', 'beasiswa'])->findOrFail($id);
        $this->feedback = $this->laporan->feedback;
    }

    public function simpanFeedback()
    {
        // Cek role dosen
        if (Auth::user()->role !== 'dosen') {
            abort(403, 'Hanya dosen yang dapat memberikan feedback.');
        }

        $this->laporan->feedback = $this->feedback;
        $this->laporan->save();

        session()->flash('message', 'Feedback berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.beasiswa.partials.laporan-beasiswa-show');
    }
}
