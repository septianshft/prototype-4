<div class="p-6 bg-white rounded shadow-md">
    <h2 class="text-2xl font-bold text-gray-700 mb-4">Manajemen Mahasiswa</h2>

    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- Tombol Tambah --}}
    <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mb-4">
        + Data Mahasiswa
    </button>

    {{-- Pencarian --}}
    <div class="mb-4">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Cari mahasiswa..."
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" />
    </div>

    {{-- Tabel Data Mahasiswa --}}
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700 bg-white rounded shadow-md">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">NIM</th>
                    <th class="px-4 py-2">IPK</th>
                    <th class="px-4 py-2">Program Studi</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mahasiswa as $mhs)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $mhs->user->name }}</td>
                        <td class="px-4 py-2">{{ $mhs->nim }}</td>
                        <td class="px-4 py-2">{{ $mhs->ipk }}</td>
                        <td class="px-4 py-2">{{ $mhs->program_studi }}</td>
                        <td class="px-4 py-2 space-x-2">
                            <button wire:click="edit({{ $mhs->id }})"
                                class="bg-blue-600 hover:bg-blue-800 text-white px-3 py-1 rounded">
                                Edit
                            </button>
                            <button wire:click="confirmDelete({{ $mhs->id }})"
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center px-4 py-2 text-gray-500">Tidak ada data mahasiswa ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Edit / Tambah --}}
    @if ($showModal)
        @include('livewire.mahasiswa.form-modal')
    @endif

    {{-- Modal Konfirmasi --}}
    <livewire:beasiswa.partials.confirmation-modal />
</div>
