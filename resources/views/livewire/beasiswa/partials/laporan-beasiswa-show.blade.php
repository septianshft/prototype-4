<div class="p-6 bg-white rounded shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Laporan Beasiswa</h2>

    <div class="space-y-3 text-gray-700">
        <p><strong>Nama Laporan:</strong> {{ $laporan->nama_laporan }}</p>
        <p><strong>Nama Mahasiswa:</strong> {{ $laporan->user->dataMahasiswa->nama_mahasiswa ?? '-' }}</p>
        <p><strong>NIM:</strong> {{ $laporan->user->dataMahasiswa->nim ?? '-' }}</p>
        <p><strong>Program Studi:</strong> {{ $laporan->user->dataMahasiswa->program_studi ?? '-' }}</p>
        <p>
            <strong>File Laporan:</strong>
            @if ($laporan->file_path)
                <a href="{{ Storage::url($laporan->file_path) }}" target="_blank" class="text-blue-600 underline hover:text-blue-800 transition">
                    Lihat File
                </a>
            @else
                <span class="text-gray-500">-</span>
            @endif
        </p>

        @if (Auth::user()->role === 'dosen')
            <div>
                <label for="feedback" class="block font-semibold mb-1">Feedback:</label>
                <textarea id="feedback" wire:model.defer="feedback" rows="4"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 transition"></textarea>

                <div class="mt-3 space-x-2">
                    <button wire:click="simpanFeedback"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition">
                         Simpan
                    </button>
                    <a href="{{ url()->previous() }}"
                        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded transition">
                        Batal
                    </a>
                </div>
            </div>
        @else
            <p><strong>Feedback:</strong> {{ $laporan->feedback ?? '-' }}</p>
            <div class="mt-4">
                <a href="{{ url()->previous() }}"
                    class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded transition">
                    Kembali
                </a>
            </div>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="mt-4 text-green-600 font-semibold">
            {{ session('message') }}
        </div>
    @endif
</div>
