<?php

namespace App\Livewire\Beasiswa\Partials;

use App\Models\Laporan_Beasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class LaporanBeasiswaTable extends Component
{
    use WithPagination;

    public $selectedLaporan = null;
    public $isEditMode = false;  // Menentukan apakah form dalam mode edit
    public $laporanId, $nama_laporan, $file_path;  // Properti untuk form edit

    public function delete($id)
    {
        $laporan = Laporan_Beasiswa::findOrFail($id);
        if (Storage::exists($laporan->file_path)) {
            Storage::delete($laporan->file_path);
        }
        $laporan->delete();
        session()->flash('message', 'Laporan berhasil dihapus.');
    }

    public function showDetail($id)
    {
        $this->selectedLaporan = Laporan_Beasiswa::with(['user', 'beasiswa'])->findOrFail($id);
    }

    public function closeDetail()
    {
        $this->selectedLaporan = null;
    }

    // Fungsi untuk memulai proses edit
    public function edit($id)
    {
        $laporan = Laporan_Beasiswa::findOrFail($id);

        // Isi properti form dengan data yang ingin diedit
        $this->laporanId = $laporan->id;
        $this->nama_laporan = $laporan->nama_laporan;
        $this->file_path = $laporan->file_path;

        // Aktifkan mode edit
        $this->isEditMode = true;
    }

    // Fungsi untuk menyimpan perubahan (update)
    public function update()
    {
        $laporan = Laporan_Beasiswa::findOrFail($this->laporanId);

        // Update data
        $laporan->update([
            'nama_laporan' => $this->nama_laporan,
            'file_path' => $this->file_path,  // Update file_path jika ada perubahan file
        ]);

        // Matikan mode edit setelah update
        $this->isEditMode = false;

        session()->flash('message', 'Laporan berhasil diperbarui.');
    }

    public function render()
    {
        $user = Auth::user();

        $query = Laporan_Beasiswa::with('user', 'beasiswa')->latest();

        if ($user->role === 'mahasiswa') {
            $query->where('user_id', $user->id);
        }

        $laporans = $query->paginate(10);

        return view('livewire.beasiswa.partials.laporan-beasiswa-table', compact('laporans'));
    }
}
