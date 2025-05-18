<div>
    @php
        $mahasiswa = Auth::user()->dataMahasiswa;
    @endphp

    {{-- Status Seleksi --}}
    @if ($mahasiswa)
        <div class="mb-2 text-sm text-gray-600">
            Status Seleksi: <strong>{{ ucfirst($mahasiswa->status_seleksi) }}</strong>
        </div>
    @endif

    {{-- Tombol Tambah jika diterima --}}
    @if ($mahasiswa && $mahasiswa->status_seleksi === 'diterima')
        <button wire:click="openModal"
            class="mb-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">
            + Data Laporan
        </button>
    @else
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded border border-yellow-300 mb-4">
            Kamu belum diterima beasiswa. Laporan tidak dapat dikirim.
        </div>
    @endif

    {{-- Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
            <div class="bg-white w-full max-w-3xl p-6 rounded-lg shadow-lg transform scale-100 transition-transform">
                <h2 class="text-xl font-bold mb-4 text-gray-800">
                    {{ $isEdit ? 'Edit' : 'Tambah' }} Laporan
                </h2>

                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="space-y-4">

                    {{-- Nama Laporan --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-700">Nama Laporan:</label>
                        <input type="text" wire:model="nama_laporan"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300" />
                        @error('nama_laporan')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- File Upload --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-700">Unggah File:</label>
                        <input type="file" wire:model="file"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm text-black bg-white focus:outline-none focus:ring-2 focus:ring-blue-500" />

                        @if ($file)
                            <p class="mt-2 text-green-600 text-sm">
                                File dipilih: <strong>{{ $file->getClientOriginalName() }}</strong>
                            </p>
                        @endif

                        <div wire:loading wire:target="file" class="mt-2 text-sm text-blue-500">
                            Mengunggah file...
                        </div>

                        @error('file')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="text-right space-x-2">
                        <button type="button" wire:click="closeModal"
                            class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
