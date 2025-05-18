<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Manajemen Mahasiswa</h2>

    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-md shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    {{-- Tombol Tambah --}}
    <div class="flex justify-between items-center mb-4">
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow">
            + Data Mahasiswa
        </button>

        {{-- Pencarian --}}
        <input type="text" wire:model.debounce.300ms="search" placeholder="Cari mahasiswa..."
            class="w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-400" />
    </div>

    {{-- Tabel Data Mahasiswa --}}
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-md text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold">Nama</th>
                    <th class="px-5 py-3 text-left font-semibold">NIM</th>
                    <th class="px-5 py-3 text-left font-semibold">IPK</th>
                    <th class="px-5 py-3 text-left font-semibold">Program Studi</th>
                    <th class="px-5 py-3 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse ($mahasiswa as $mhs)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-5 py-3">{{ $mhs->user->name }}</td>
                        <td class="px-5 py-3">{{ $mhs->nim }}</td>
                        <td class="px-5 py-3">{{ $mhs->ipk }}</td>
                        <td class="px-5 py-3">{{ $mhs->program_studi }}</td>
                        <td class="px-5 py-3 space-x-2">
                            <button wire:click="edit({{ $mhs->id }})"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md">
                                Edit
                            </button>
                            <button wire:click="confirmDelete({{ $mhs->id }})"
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-3 text-center text-gray-500 italic">
                            Tidak ada data mahasiswa ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah/Edit --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    {{ $isEdit ? 'Edit Mahasiswa' : 'Tambah Mahasiswa' }}
                </h3>

                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" wire:model="nama_mahasiswa"
                            class="w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring focus:border-blue-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIM</label>
                        <input type="text" wire:model="nim"
                            class="w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring focus:border-blue-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">IPK</label>
                        <input type="number" step="0.01" wire:model="ipk"
                            class="w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring focus:border-blue-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Program Studi</label>
                        <input type="text" wire:model="program_studi"
                            class="w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring focus:border-blue-400" />
                    </div>
                    <div class="text-right space-x-2">
                        <button type="button" wire:click="closeModal"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi --}}
    <livewire:beasiswa.partials.confirmation-modal />
</div>
    