<?php

namespace App\Livewire\Beasiswa\Partials;

use App\Models\ApplyBeasiswa;
use App\Models\Laporan_Beasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class LaporanBeasiswaTable extends Component
{
    use WithPagination;

    public $selectedMahasiswaId = null;
    public $selectedMahasiswaName = null;
    public $selectedMahasiswaProdi;


    public $selectedLaporan = null;
    public $isEditMode = false;
    public $laporanId, $nama_laporan, $file_path;
    public $deleteId;

    protected $listeners = ['deleteConfirmed' => 'performDelete', 'laporanUpdated' => '$refresh'];

    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        $this->dispatch('showModal', [
            'title' => 'Hapus Pengguna',
            'message' => 'Apakah Anda yakin ingin menghapus laporan ini?',
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

    public function selectMahasiswa($userId)
    {
        $this->selectedMahasiswaId = $userId;

        $mahasiswa = DB::table('data_mahasiswa as dm')
            ->join('program_studi as ps', 'dm.program_studi_id', '=', 'ps.id')
            ->where('dm.user_id', $userId)
            ->select('dm.nama_mahasiswa', 'ps.program_studi', 'ps.jenjang')
            ->first();

        $this->selectedMahasiswaName = $mahasiswa->nama_mahasiswa;
        $this->selectedMahasiswaProdi = "{$mahasiswa->program_studi} ({$mahasiswa->jenjang})";
    }


    public function triggerEdit($id)
    {
        $this->dispatch('editLaporan', $id);
    }

    public function emitEditLaporan($id)
    {
        $this->dispatch('editLaporan', $id);
    }

    public function render()
    {
        $user = Auth::user();

        if (($user->role === 'dosen' || $user->role === 'admin') && is_null($this->selectedMahasiswaId)) {
            // STEP 1: Tampilkan daftar mahasiswa
            $mahasiswas = DB::table('apply_beasiswa as ab')
                ->join('data_mahasiswa as dm', 'ab.user_id', '=', 'dm.user_id')
                ->leftJoin('program_studi as ps', 'dm.program_studi_id', '=', 'ps.id')
                ->whereIn('ab.beasiswa_id', function ($query) use ($user) {
                    $query->select('id')
                        ->from('beasiswa')
                        // untuk admin → ambil semua beasiswa
                        ->when($user->role === 'dosen', function ($q) use ($user) {
                            $q->where('dosen_id', $user->id);
                        });
                })
                ->where('ab.status', 'diterima')
                ->groupBy('ab.user_id', 'dm.nama_mahasiswa', 'dm.nim', 'ps.program_studi', 'ps.jenjang')
                ->select(
                    'ab.user_id',
                    'dm.nama_mahasiswa',
                    'dm.nim',
                    'ps.program_studi',
                    'ps.jenjang'
                )
                ->get();

            return view('livewire.beasiswa.partials.laporan-beasiswa-table', [
                'mahasiswas' => $mahasiswas,
                'laporans' => null,
            ]);
        } else {
            // STEP 2: Tampilkan laporan mahasiswa terpilih (atau mahasiswa/admin/vice_director)
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
                ->when($user->role === 'dosen' || $user->role === 'admin', function ($query) {
                    if ($this->selectedMahasiswaId) {
                        $query->where('user_id', $this->selectedMahasiswaId);
                    } else {
                        $query->whereNull('id');
                    }
                })
                ->latest()
                ->paginate(10);

            return view('livewire.beasiswa.partials.laporan-beasiswa-table', [
                'mahasiswas' => null,
                'laporans' => $laporans,
            ]);
        }
    }
}
