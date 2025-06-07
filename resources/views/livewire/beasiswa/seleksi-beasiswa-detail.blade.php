<div class="p-6 bg-white rounded shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Detail Seleksi Beasiswa</h2>

    <div class="space-y-3 text-gray-700">
        <p><strong>Nama Mahasiswa:</strong> {{ $apply->user->dataMahasiswa->nama_mahasiswa ?? '-' }}</p>
        <p><strong>NIM:</strong> {{ $apply->user->dataMahasiswa->nim ?? '-' }}</p>

        <p>
            <strong>Program Studi:</strong>
            {{ $apply->user->dataMahasiswa->programStudi->program_studi ?? '-' }}
            ({{ $apply->user->dataMahasiswa->programStudi->jenjang ?? '-' }})
        </p>

        <p><strong>Nama Beasiswa:</strong> {{ $apply->beasiswa->nama_beasiswa ?? '-' }}</p>

        <p>
            <strong>File Persyaratan:</strong>
            @if ($apply->file_persyaratan_path)
                <a href="{{ Storage::url($apply->file_persyaratan_path) }}" target="_blank"
                    class="text-blue-600 underline hover:text-blue-800 transition">
                    Lihat File
                </a>
            @else
                <span class="text-gray-500">-</span>
            @endif
        </p>

        <p><strong>Feedback:</strong></p>

        @if (Auth::user()->role === 'dosen')
            @if ($showFeedbackInput)
                {{-- Form Input Feedback --}}
                <textarea wire:model.defer="feedback" rows="4"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 transition"></textarea>

                <div class="mt-2 space-x-2">
                    <button wire:click="simpanFeedback"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                        Simpan Feedback
                    </button>

                    <button wire:click="$set('showFeedbackInput', false)"
                        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded transition">
                        Batal
                    </button>
                </div>
            @else
                {{-- Tampilan feedback --}}
                <p class="text-gray-700 italic">
                    {{ $apply->feedback ? $apply->feedback : 'Belum ada feedback.' }}
                </p>

                <button wire:click="$set('showFeedbackInput', true)"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition mt-2">
                    Edit Feedback
                </button>
            @endif
        @else
            {{-- Mahasiswa hanya bisa lihat --}}
            <p class="text-gray-700 italic">
                {{ $apply->feedback ? $apply->feedback : 'Belum ada feedback.' }}
            </p>
        @endif


        @if (Auth::user()->role === 'dosen')

            <div class="mt-4 space-x-2">

                <button wire:click="updateFileStatus('diterima')"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition
        @if (in_array($apply->file_status, ['diterima', 'ditolak'])) opacity-50 cursor-not-allowed @endif"
                    @if (in_array($apply->file_status, ['diterima', 'ditolak'])) disabled @endif>
                    ACC
                </button>

                <button wire:click="updateFileStatus('ditolak')"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition
        @if (in_array($apply->file_status, ['diterima', 'ditolak'])) opacity-50 cursor-not-allowed @endif"
                    @if (in_array($apply->file_status, ['diterima', 'ditolak'])) disabled @endif>
                    Revisi File
                </button>

                <button wire:click="batal"
                    class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded transition">
                    Kembali
                </button>

            </div>


            <hr class="my-6">
            <p class="font-semibold text-red-600 text-lg">
                Status File:
                <span @class([
                    'text-green-600' => $apply->file_status === 'diterima',
                    'text-red-600' => $apply->file_status === 'ditolak',
                    'text-yellow-600' => $apply->file_status === 'pending',
                ]) class="ml-2 font-bold">
                    @if ($apply->file_status === 'diterima')
                        Disetujui
                    @elseif ($apply->file_status === 'ditolak')
                        Revisi
                    @else
                        Pending
                    @endif
                </span>
            </p>
    </div>
@elseif (Auth::user()->role === 'mahasiswa')
    <hr class="my-6">

    <p class="font-semibold text-red-600 text-lg">
        Status File:
        <span @class([
            'text-green-600' => $apply->file_status === 'diterima',
            'text-red-600' => $apply->file_status === 'ditolak',
            'text-yellow-600' => $apply->file_status === 'pending',
        ]) class="ml-2 font-bold">
            @if ($apply->file_status === 'diterima')
                Disetujui
            @elseif ($apply->file_status === 'ditolak')
                Revisi
            @else
                Pending
            @endif
        </span>
    </p>



    <div class="mt-4">
        <button wire:click="batal" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded transition">
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
