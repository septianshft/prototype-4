<div>
    @php
        $mahasiswa = Auth::user()->dataMahasiswa;
    @endphp

    {{-- Debug untuk melihat kondisi --}}
    @if ($mahasiswa)
        <div class="mb-2 text-sm text-gray-600">
            Status Seleksi: <strong>{{ $mahasiswa->status_seleksi }}</strong>
        </div>
    @endif

    {{-- Jika mahasiswa diterima, tampilkan tombol --}}
    @if ($mahasiswa && $mahasiswa->status_seleksi === 'diterima')
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mb-4">
            + Data Laporan
        </button>
    @else
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded border border-yellow-300 mb-4">
            Kamu belum diterima beasiswa. Laporan tidak dapat dikirim.
        </div>
    @endif

    {{-- Modal input laporan --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl p-6 transition-transform transform scale-100">
                <h2 class="text-xl font-bold mb-4">
                    {{ $isEdit ? 'Edit' : 'Tambah' }} Laporan
                </h2>

                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">

                    {{-- Input nama laporan --}}
                    <div class="mb-4">
                        <label class="block font-medium mb-1 dark:text-black">Nama Laporan:</label>
                        <input type="text" wire:model="nama_laporan" class="w-full border rounded px-3 py-2 dark:text-black" />
                        @error('nama_laporan')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Input file --}}
                    <div class="mb-4">
                        <label class="block font-medium mb-1 text-black dark:text-black">Unggah File:</label>
                        <input type="file" wire:model="file"
                            class="block w-full text-sm text-black bg-white border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />

                        @if ($file)
                            <p class="mt-2 text-green-600 text-sm">File dipilih:
                                <strong>{{ $file->getClientOriginalName() }}</strong>
                            </p>
                        @endif

                        <div wire:loading wire:target="file" class="text-sm text-blue-500 mt-2">Mengunggah file...</div>

                        @error('file')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="text-right space-x-2">
                        <button type="button" wire:click="closeModal" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
