<div class="p-6 bg-white rounded shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Laporan Beasiswa</h2>

    <div class="space-y-3 text-gray-700">
        <p><strong>Nama Laporan:</strong> {{ $laporan->nama_laporan }}</p>
        <p><strong>Nama Mahasiswa:</strong> {{ $laporan->user->dataMahasiswa->nama_mahasiswa ?? '-' }}</p>
        <p><strong>NIM:</strong> {{ $laporan->user->dataMahasiswa->nim ?? '-' }}</p>
        <p>
            <strong>Program Studi:</strong>
            {{ $laporan->user->dataMahasiswa->programStudi->program_studi ?? '-' }}
            ({{ $laporan->user->dataMahasiswa->programStudi->jenjang ?? '-' }})
        </p>

        <p>
            <strong>File Laporan:</strong>
            @if ($laporan->file_path)
                <a href="{{ Storage::url($laporan->file_path) }}" target="_blank"
                    class="text-blue-600 underline hover:text-blue-800 transition">
                    Lihat File
                </a>
            @else
                <span class="text-gray-500">-</span>
            @endif
        </p>
        <p><strong>Feedback:</strong> {{ $laporan->feedback ?? '-' }}</p>

        @if (Auth::user()->role === 'dosen')
            <div>
                @if ($isEditingFeedback)
                    <textarea wire:model.defer="feedback" rows="4"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 transition"></textarea>

                    <button wire:click="simpanFeedback"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition mt-2">
                        Simpan Feedback
                    </button>
                @else
                    <button wire:click="startEditingFeedback"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition mt-2">
                        Edit Feedback
                    </button>
                @endif

                <div class="mt-4 space-x-2">
                    @if ($laporan->acc_count < 6 && $laporan->status_acc !== 'rejected')
                        <button wire:click="accLaporan('approved')"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">
                            ACC Laporan
                        </button>

                        <button wire:click="accLaporan('rejected')"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition">
                            Tolak Laporan
                        </button>
                    @endif

                    <button wire:click="batal"
                        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded transition">
                        Batal
                    </button>
                </div>

                <p class="mt-4 font-semibold"><strong>Status Laporan:</strong>
                    @if ($laporan->status_acc === 'approved')
                        Disetujui
                    @elseif ($laporan->status_acc === 'rejected')
                        Ditolak
                    @else
                        Menunggu ACC
                    @endif
                </p>
            </div>
        @elseif(Auth::user()->role === 'mahasiswa')
            <p><strong>Status Laporan:</strong>
                <span @class([
                    'text-green-600' => $laporan->status_acc === 'approved',
                    'text-red-600' => $laporan->status_acc === 'rejected',
                    'text-yellow-600' => $laporan->status_acc === 'pending',
                ])>
                    @if ($laporan->status_acc === 'approved')
                        Disetujui
                    @elseif ($laporan->status_acc === 'rejected')
                        Ditolak
                    @else
                        Pending
                    @endif
                </span>
            </p>

            <div class="mt-4">
                <button wire:click="batal"
                    class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded transition">
                    Kembali
                </button>
            </div>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="mt-4 text-green-600 font-semibold">
            {{ session('message') }}
        </div>
    @endif
</div>

<script>
    window.addEventListener('navigate-back', () => {
        history.back();
    });
</script>
