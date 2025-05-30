<?php

namespace App\Livewire\Beasiswa\Partials;

use App\Models\ApplyBeasiswa;
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
    protected $listeners = ['deleteConfirmed' => 'performDelete', 'laporanUpdated' => '$refresh',];
    public $deleteId;

    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        $this->dispatch('showModal', [
            'title' => 'Hapus Pengguna',
            'message' => 'Apakah Anda yakin ingin menghapus pengguna ini?',
            'confirmText' => 'Hapus',
            'cancelText' => 'Batal',
            'onConfirm' => 'deleteConfirmed',
        ]);
    }

    public function performDelete()
    {
        $this->delete($this->deleteId);
    }

    public function delete($id)
    {
        $laporan = Laporan_Beasiswa::findOrFail($id);
        if (Storage::exists($laporan->file_path)) {
            Storage::delete($laporan->file_path);
        }
        $laporan->delete();
        session()->flash('message', 'Laporan berhasil dihapus.');
    }

    public function triggerEdit($id)
    {
        $this->dispatch('editLaporan', $id);
    }

    public function showDetail($id)
    {
        $this->selectedLaporan = Laporan_Beasiswa::with(['user', 'beasiswa'])->findOrFail($id);
    }

    public function closeDetail()
    {
        $this->selectedLaporan = null;
    }

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

    public function emitEditLaporan($id)
    {
        $this->dispatch('editLaporan', $id);
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

        $laporans = Laporan_Beasiswa::query()
            ->when($user->role === 'mahasiswa', function ($query) use ($user) {
                $diterima = ApplyBeasiswa::where('user_id', $user->id)
                    ->where('status', 'diterima')
                    ->exists();

                if ($diterima) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->whereNull('id');
                }
            })
            ->when($user->role === 'dosen', function ($query) use ($user) {
                $query->whereIn('user_id', function ($subquery) use ($user) {
                    $subquery->select('user_id')
                        ->from('apply_beasiswa')
                        ->whereIn('beasiswa_id', function ($subsub) use ($user) {
                            $subsub->select('id')
                                ->from('beasiswa')
                                ->where('dosen_id', $user->id);
                        });
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.beasiswa.partials.laporan-beasiswa-table', [
            'laporans' => $laporans
        ]);
    }
}
