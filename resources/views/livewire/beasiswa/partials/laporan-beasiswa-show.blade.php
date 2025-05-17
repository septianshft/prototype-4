<div class="p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Laporan Beasiswa</h2>

    <div class="space-y-3">
        <p><strong>Nama Laporan</strong> : {{ $laporan->nama_laporan }}</p>
        <p><strong>Nama Mahasiswa</strong> : {{ $laporan->user->dataMahasiswa->nama_mahasiswa ?? '-' }}</p>
        <p><strong>NIM</strong> : {{ $laporan->user->dataMahasiswa->nim ?? '-' }}</p>
        <p><strong>Program Studi</strong> : {{ $laporan->user->dataMahasiswa->program_studi ?? '-' }}</p>
        <p><strong>File Laporan</strong> :
            <a href="{{ Storage::url($laporan->file_path) }}" target="_blank" class="text-blue-600 underline">File</a>
        </p>

        {{-- Jika role dosen, tampilkan form feedback --}}
        @if (Auth::user()->role === 'dosen')
            <div>
                <label for="feedback" class="block font-semibold mb-1">Feedback:</label>
                <textarea id="feedback" wire:model.defer="feedback" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                <div class="mt-2">
                    <button wire:click="simpanFeedback" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ url()->previous() }}" class="bg-gray-400 text-white px-4 py-2 rounded ml-2">Batal</a>
                </div>
            </div>
        @else
            <p><strong>Feedback</strong> : {{ $laporan->feedback ?? '-' }}</p>
            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="bg-gray-400 text-white px-4 py-2 rounded">Kembali</a>
            </div>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="mt-4 text-green-600">{{ session('message') }}</div>
    @endif
</div>
