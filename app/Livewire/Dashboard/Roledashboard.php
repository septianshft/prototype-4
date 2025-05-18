<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Beasiswa;
use App\Models\ApplyBeasiswa;

class Roledashboard extends Component
{
    public $data = [];

    public function mount()
    {

        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
            case 'vicedirector':
                $this->data = [
                    'penerimaBeasiswa' => ApplyBeasiswa::where('status', 'diterima')->count(),
                    'jumlahDosen' => User::where('role', 'dosen')->count(),
                    'jumlahBeasiswa' => Beasiswa::count(),
                ];
                $chartData = ApplyBeasiswa::selectRaw("SUBSTRING(beasiswa.periode, 1, 4) as tahun, COUNT(*) as total")
                    ->join('beasiswa', 'apply_beasiswa.beasiswa_id', '=', 'beasiswa.id')
                    ->where('apply_beasiswa.status', 'diterima')
                    ->groupBy('tahun')
                    ->orderBy('tahun')
                    ->get();

                $this->data['chart'] = $chartData;
                break;

            case 'dosen':
                $this->data = [
                    'penerimaBeasiswa' => ApplyBeasiswa::whereHas('beasiswa', fn($q) => $q->where('dosen_id', $user->id))
                        ->where('status', 'diterima')->count(),
                    'mahasiswaPending' => ApplyBeasiswa::whereHas('beasiswa', fn($q) => $q->where('dosen_id', $user->id))
                        ->where('status', 'pending')->count(),
                    'beasiswaKelola' => Beasiswa::where('dosen_id', $user->id)->count(),
                ];
                $chartData = ApplyBeasiswa::selectRaw("SUBSTRING(beasiswa.periode, 1, 4) as tahun, COUNT(*) as total")
                    ->join('beasiswa', 'apply_beasiswa.beasiswa_id', '=', 'beasiswa.id')
                    ->where('apply_beasiswa.status', 'diterima')
                    ->groupBy('tahun')
                    ->orderBy('tahun')
                    ->get();

                $this->data['chart'] = $chartData;
                break;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.roledashboard', [
            'data' => $this->data,
            'chartLabels' => collect($this->data['chart'])->pluck('tahun'),
            'chartData' => collect($this->data['chart'])->pluck('total'),
        ]);
    }
}
