<?php

namespace App\Livewire\Beasiswa\Partials;

use App\Models\Laporan_Beasiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LaporanBeasiswaShow extends Component
{
    public $laporan;
    public $feedback;
    public $isEditingFeedback = false; // for edit feedback toggle

    public function mount($id)
    {
        $laporan = Laporan_Beasiswa::with([
            'user.dataMahasiswa.programStudi',
            'beasiswa'
        ])->findOrFail($id);

        if (Auth::user()->role === 'mahasiswa' && $laporan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan melihat laporan ini.');
        }

        $this->laporan = $laporan;
        $this->feedback = $laporan->feedback;
    }

    public function startEditingFeedback()
    {
        if (Auth::user()->role !== 'dosen') {
            abort(403, 'Hanya dosen yang dapat memberikan feedback.');
        }
        $this->isEditingFeedback = true;
    }

    public function simpanFeedback()
    {
        if (Auth::user()->role !== 'dosen') {
            abort(403, 'Hanya dosen yang dapat memberikan feedback.');
        }

        $this->laporan->feedback = $this->feedback;
        $this->laporan->save();

        session()->flash('message', 'Feedback berhasil disimpan.');
        $this->isEditingFeedback = false;
    }

    public function accLaporan($status)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['dosen', 'admin'])) {
            session()->flash('error', 'Hanya dosen atau admin yang dapat melakukan aksi ini.');
            return;
        }

        $laporan = $this->laporan;

        if (!$laporan->beasiswa) {
            session()->flash('error', 'Data beasiswa tidak ditemukan.');
            return;
        }

        if ($user->role === 'dosen' && $laporan->beasiswa->dosen_id !== $user->id) {
            session()->flash('error', 'Anda bukan dosen penyelenggara beasiswa ini.');
            return;
        }

        if ($laporan->status_acc === 'approved') {
            session()->flash('error', 'Laporan sudah disetujui, tidak bisa diubah.');
            return;
        }

        if ($laporan->status_acc === 'rejected') {
            session()->flash('error', 'Laporan sudah ditolak, tidak bisa diubah.');
            return;
        }

        if ($status === 'approved') {
            if ($laporan->acc_count < 6) {
                $laporan->acc_count += 1;
            }
            $laporan->status_acc = 'approved';
            $laporan->approved_by = $user->id;
            $laporan->approved_at = now();
            $laporan->feedback = $this->feedback;
            $laporan->save();
            session()->flash('message', 'Laporan berhasil di-acc (' . $laporan->acc_count . '/6).');
        } elseif ($status === 'rejected') {
            $laporan->status_acc = 'rejected';
            $laporan->feedback = $this->feedback;
            $laporan->approved_by = $user->id;
            $laporan->approved_at = now();
            $laporan->save();
            session()->flash('message', 'Laporan ditolak.');
        }

        $this->dispatch('laporanUpdated');
    }

    public function batal()
    {
        $this->dispatch('navigate-back');
    }

    public function render()
    {
        return view('livewire.beasiswa.partials.laporan-beasiswa-show');
    }
}
