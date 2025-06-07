<?php

namespace App\Livewire\Beasiswa;

use App\Models\ApplyBeasiswa;
use Livewire\Component;

class SeleksiBeasiswaDetail extends Component
{
    public $apply;
    public $feedback;
    public $applyId;
    public $showFeedbackInput = false;




    public function mount($apply)
    {
        // Simpan ID
        $this->applyId = $apply;

        // Ambil data ApplyBeasiswa
        $this->apply = ApplyBeasiswa::with(['user.dataMahasiswa', 'beasiswa'])->findOrFail($apply);
    }

    public function simpanFeedback()
    {
        $this->apply->feedback = $this->feedback;
        $this->apply->save();

        session()->flash('message', 'Feedback berhasil disimpan.');

        // Tutup input setelah simpan
        $this->showFeedbackInput = false;
    }

    public function updateFileStatus($status)
    {
        if ($this->apply->status === 'ditolak') {
            session()->flash('message', 'Status pengajuan sudah ditolak, tidak bisa diubah lagi.');
            return;
        }

        $this->apply->file_status = $status;

        // Simpan
        $this->apply->save();

        session()->flash('message', 'Status file berhasil diperbarui.');
    }


    public function batal()
    {
        $this->dispatch('navigate-back');
    }

    public function render()
    {
        return view('livewire.beasiswa.seleksi-beasiswa-detail', [
            'apply' => $this->apply,  // <== penting!! ini yg bikin Blade tahu $apply
        ]);
    }
}
