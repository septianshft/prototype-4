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
                            <td class="px-5 py-3">
                                {{ $mhs->programStudi->program_studi ?? '-' }}
                                ({{ strtoupper($mhs->programStudi->jenjang ?? '-') }})
                            </td>
                            <td class="px-5 py-3 space-x-2">
                                <button wire:click="edit({{ $mhs->id }})"
                                    class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded transition text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button wire:click="confirmDelete({{ $mhs->id }})"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
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
                            {{-- Dropdown Program Studi --}}
                            <select wire:model="program_studi_id"
                                class="w-full border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring focus:border-blue-400">
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach ($listProgramStudi as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->program_studi }}
                                        ({{ $prodi->jenjang }})
                                    </option>
                                @endforeach
                            </select>

                            {{-- Di tabel --}}
                            <td class="px-5 py-3">{{ $mhs->programStudi->program_studi ?? '-' }}</td>

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
