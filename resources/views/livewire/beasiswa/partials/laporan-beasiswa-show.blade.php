<div class="p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Laporan Beasiswa</h2>

    <div class="space-y-3">
        <p><strong>Nama Beasiswa</strong> : {{ $laporan->beasiswa->nama_beasiswa ?? '-' }}</p>
        <p><strong>Nama Laporan</strong> : {{ $laporan->nama_laporan }}</p>
        <p><strong>Nama Mahasiswa</strong> : {{ $laporan->user->name ?? '-' }}</p>
        <p><strong>NIM</strong> : {{ $laporan->user->nim ?? '-' }}</p>
        <p><strong>Program Studi</strong> : {{ $laporan->user->prodi ?? '-' }}</p>
        <p><strong>File Laporan</strong> :
            <a href="{{ Storage::url($laporan->file_path) }}" target="_blank" class="text-blue-600 underline">File</a>
        </p>
        <p><strong>Feedback</strong> : {{ $laporan->feedback ?? '-' }}</p>
    </div>

    <div class="mt-4">
        <a href="{{ url()->previous() }}" class="bg-gray-400 text-white px-4 py-2 rounded">Kembali</a>
    </div>
</div>
