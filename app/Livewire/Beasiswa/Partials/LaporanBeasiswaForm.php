<?php

namespace App\Livewire\Beasiswa\Partials;

use App\Models\Laporan_Beasiswa;
use App\Models\ApplyBeasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class LaporanBeasiswaForm extends Component
{
    use WithFileUploads;

    public $nama_laporan;
    public $file;
    public $beasiswa_id;
    public $beasiswa_nama;
    public $jenis_laporan = 'progress';  // Default jenis laporan is progress
    public $showModal = false;
    public $isEdit = false;
    public $selectedId;
    public $laporanTeracc = 0;  // Public property to hold the count of approved reports

    protected $listeners = ['editLaporan' => 'editLaporan'];

    protected function rules()
    {
        return [
            'nama_laporan' => 'required|string|min:3',
            'file' => $this->isEdit
                ? 'nullable|file|mimes:pdf,doc,docx|max:2048'
                : 'required|file|mimes:pdf,doc,docx|max:2048',
            'jenis_laporan' => 'required|string|in:progress,final', // Validate jenis_laporan
        ];
    }

    public function openModal()
    {
        $userId = Auth::id();

        // Fetching the accepted beasiswa for the student
        $diterima = ApplyBeasiswa::where('user_id', $userId)
            ->where('status', 'diterima')
            ->with('beasiswa')
            ->first();

        if (!$diterima) {
            session()->flash('error', 'Kamu belum bisa mengisi laporan karena belum diterima beasiswa.');
            return;
        }

        $this->reset(['nama_laporan', 'file', 'isEdit', 'selectedId']);
        $this->beasiswa_id = $diterima->beasiswa_id;
        $this->beasiswa_nama = $diterima->beasiswa->nama_beasiswa ?? '-';

        // Calculate the count of approved reports for this beasiswa
        $this->laporanTeracc = Laporan_Beasiswa::where('beasiswa_id', $this->beasiswa_id)
            ->where('user_id', $userId)
            ->where('status_acc', 'approved')
            ->count();

        // Hanya set default jenis_laporan jika belum diset sebelumnya
        if (!$this->jenis_laporan) {
            $this->jenis_laporan = $this->laporanTeracc >= 6 ? 'final' : 'progress';
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function store()
    {
        $userId = Auth::id();
        $diterima = ApplyBeasiswa::where('user_id', $userId)
            ->where('status', 'diterima')
            ->first();

        if (!$diterima) {
            session()->flash('error', 'Kamu belum bisa mengirim laporan karena belum diterima beasiswa.');
            return;
        }

        if ($this->jenis_laporan === 'final' && $this->laporanTeracc < 6) {
            session()->flash('error', 'Kamu belum memenuhi syarat untuk mengunggah laporan final.');
            return;
        }

        $this->validate();

        $path = $this->file->store('laporan', 'public');

        Laporan_Beasiswa::create([
            'nama_laporan' => $this->nama_laporan,
            'file_path' => $path,
            'beasiswa_id' => $this->beasiswa_id,
            'jenis_laporan' => $this->jenis_laporan,  // Store jenis_laporan
            'user_id' => $userId,
        ]);

        $this->closeModal();
        session()->flash('message', 'Laporan berhasil disimpan.');
        $this->dispatch('laporanUpdated');
    }

    public function editLaporan($id)
    {
        $laporan = Laporan_Beasiswa::findOrFail($id);

        $this->selectedId = $laporan->id;
        $this->nama_laporan = $laporan->nama_laporan;
        $this->beasiswa_id = $laporan->beasiswa_id;
        $this->jenis_laporan = $laporan->jenis_laporan;  // Get jenis_laporan when editing

        $this->beasiswa_nama = $laporan->beasiswa->nama_beasiswa ?? '-';

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $laporan = Laporan_Beasiswa::findOrFail($this->selectedId);
        $laporan->nama_laporan = $this->nama_laporan;

        if ($this->file) {
            if (Storage::exists($laporan->file_path)) {
                Storage::delete($laporan->file_path);
            }
            $laporan->file_path = $this->file->store('laporan', 'public');
        }

        // Don't update jenis_laporan when updating
        $laporan->jenis_laporan = $this->jenis_laporan;

        $laporan->save();

        $this->closeModal();
        session()->flash('message', 'Laporan berhasil diperbarui.');
        $this->dispatch('laporanUpdated');
    }

    public function render()
    {
        return view('livewire.beasiswa.partials.laporan-beasiswa-form');
    }
}
