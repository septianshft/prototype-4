<div>
    <div class="p-6 bg-gray-100 min-h-screen">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Manajemen Beasiswa</h2>

        {{-- Tombol Tambah --}}
        <div class="flex justify-between items-center mb-4">
            <button wire:click="openModal"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
                + Data Beasiswa
            </button>
            <input type="text" wire:model="search" placeholder="🔍 Pencarian"
                class="border border-gray-300 rounded px-3 py-1.5 text-sm w-64" />
        </div>

        {{-- Tabel --}}
        <table class="min-w-full text-sm text-left text-gray-700 bg-white rounded shadow-md">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="px-4 py-2">Nama Beasiswa</th>
                    <th class="px-4 py-2">Penyelenggara</th>
                    <th class="px-4 py-2">Periode</th>
                    <th class="px-4 py-2">Kuota</th>
                    <th class="px-4 py-2">Deskripsi</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($beasiswas as $data)
                    <tr>
                        <td class="px-4 py-2">{{ $data->nama_beasiswa }}</td>
                        <td class="px-4 py-2">{{ $data->nama_penyelenggara }}</td>
                        <td class="px-4 py-2">{{ $data->periode }}</td>
                        <td class="px-4 py-2">{{ $data->kuota }}</td>
                        <td class="px-4 py-2">{{ $data->deskripsi }}</td>
                        <td class="px-4 py-2 space-x-2">
                            <button wire:click="edit({{ $data->id }})"
                                class="bg-yellow-300 px-2 py-1 rounded">✏️</button>
                            <button wire:click="delete({{ $data->id }})"
                                class="bg-red-600 text-white px-2 py-1 rounded">🗑️</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Modal Form --}}
        @if ($showModal)
            <div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        {{ $isEdit ? 'Edit Beasiswa' : 'Tambah Beasiswa' }}
                    </h3>

                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-700">Nama Beasiswa</label>
                            <input type="text" wire:model="nama_beasiswa"
                                class="w-full border px-3 py-2 rounded  dark:bg-gray-600 dark:text-black" />
                            @error('nama_beasiswa')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Penyelenggara</label>
                            <input type="text" wire:model="nama_penyelenggara"
                                class="w-full border px-3 py-2 rounded  dark:bg-gray-600 dark:text-black" />
                            @error('nama_penyelenggara')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Periode</label>
                            <input type="date" wire:model="periode"
                                class="w-full border px-3 py-2 rounded  dark:bg-gray-600 dark:text-black" />
                            @error('periode')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Kuota</label>
                            <input type="number" wire:model="kuota"
                                class="w-full border px-3 py-2 rounded  dark:bg-gray-600 dark:text-black" min="0"
                                max="1000" />
                            @error('kuota')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Deskripsi</label>
                            <textarea wire:model="deskripsi" rows="3"
                                class="w-full border px-3 py-2 rounded  dark:bg-gray-600 dark:text-black"></textarea>
                            @error('deskripsi')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="text-right">
                            <button type="button" wire:click="closeModal"
                                class="bg-gray-300 px-4 py-2 rounded mr-2">Batal</button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
