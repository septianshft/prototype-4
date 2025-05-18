<div class="p-6 bg-white min-h-screen"> {{-- Ganti jadi putih --}}
    <h2 class="text-2xl font-bold text-gray-700 mb-6">Manajemen Beasiswa</h2>

    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-10">
        @if (session()->has('message'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded shadow">
                {{ session('message') }}
            </div>
        @endif

        @if (!$mahasiswa)
            <div class="text-center text-gray-500 text-lg">Tidak ada data beasiswa yang diterima.</div>
        @else
            <div class="bg-white rounded-xl shadow p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 text-gray-800">

                    {{-- Kolom Kiri --}}
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Nama Mahasiswa</p>
                            <p class="text-2xl font-bold">{{ $mahasiswa->nama_mahasiswa ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">NIM</p>
                            <p class="text-2xl font-bold">{{ $mahasiswa->nim ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Program Studi</p>
                            <p class="text-2xl font-bold">{{ $mahasiswa->program_studi ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Beasiswa</p>
                            <p class="text-2xl font-bold">
                                {{ $mahasiswa->status === 'diterima' ? ($mahasiswa->nama_beasiswa ?? '-') : '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Dosen Pembimbing</p>
                            <p class="text-2xl font-bold">
                                {{ $mahasiswa->status === 'diterima' ? ($mahasiswa->nama_dosen ?? '-') : '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Kolom Kanan --}}
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Status Beasiswa</p>
                            <p class="text-2xl font-bold mb-2">
                                {{ ucfirst($mahasiswa->status ?? '-') }}
                            </p>

                        </div>
                    </div>

                </div>
            </div>
        @endif
    </div>
</div>
