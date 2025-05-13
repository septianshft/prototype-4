<div class="p-6 bg-gray-100 min-h-screen">
    <h2 class="text-2xl font-bold text-gray-700 mb-4">Manajemen Laporan Beasiswa</h2>

    {{-- Tombol Tambah --}}
    <div class="flex justify-between mb-4">
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            + Tambah Laporan
        </button>
    </div>

    {{-- Tabel --}}
    <table class="w-full bg-white text-left text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-4 py-2">Nama Laporan</th>
                <th class="px-4 py-2">Nama Mahasiswa</th>
                <th class="px-4 py-2">File</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporans as $laporan)
                <tr>
                    <td>{{ $laporan->nama_laporan }}</td>
                    <td>{{ $laporan->dataMahasiswa->nama_mahasiswa ?? '-' }}</td>
                    <td><a href="{{ Storage::url($laporan->file_path) }}" target="_blank">Lihat File</a></td>
                    <td>{{ $laporan->created_at->format('d M Y') }}</td>
                    <td>
                        <button wire:click="edit({{ $laporan->id }})">✏️</button>
                        <button wire:click="delete({{ $laporan->id }})">🗑️</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $laporans->links() }}
    </div>

    {{-- MODAL FORM --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
                <h3 class="text-xl font-semibold mb-4">
                    {{ $isEdit ? 'Edit Laporan' : 'Tambah Laporan' }}
                </h3>

                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="space-y-4">
                    <div>
                        <label class="block text-sm">Nama Laporan</label>
                        <input type="text" wire:model="nama_laporan" class="w-full border rounded px-3 py-2" />
                        @error('nama_laporan')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700">Nama Mahasiswa</label>
                        <p class="px-3 py-2 rounded bg-gray-100">
                            {{ auth()->user()->dataMahasiswa->nama_mahasiswa ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm">Beasiswa</label>
                        <select wire:model="beasiswa_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Pilih --</option>
                            @foreach ($beasiswas as $bsw)
                                <option value="{{ $bsw->id }}">{{ $bsw->nama_beasiswa }}</option>
                            @endforeach
                        </select>
                        @error('beasiswa_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm">Upload File</label>
                        <input type="file" wire:model="file_path" class="w-full border rounded px-3 py-2" />
                        @error('file_path')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="text-right">
                        <button type="button" wire:click="closeModal"
                            class="bg-gray-300 px-4 py-2 rounded mr-2">Batal</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
