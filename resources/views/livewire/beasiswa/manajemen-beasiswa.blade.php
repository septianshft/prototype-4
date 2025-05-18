<div class="p-6 bg-white rounded shadow-md">
    <h2 class="text-2xl font-bold text-gray-700 mb-4">Manajemen Beasiswa</h2>
    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-3 bg-red-200 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if ($role === 'admin' || $role === 'dosen')
        <button wire:click="openModal" class="bg-blue-600 text-white px-4 py-2 rounded mb-4">
            + Data Beasiswa
        </button>
    @endif

    {{-- Pencarian --}}
    <div class="mb-4">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Cari nama beasiswa..."
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" />
    </div>

    {{-- Tabel Beasiswa --}}
    <table class="min-w-full text-sm text-left text-gray-700 bg-white rounded shadow-md">
        <thead class="bg-gray-200 text-gray-700">
            <tr>
                <th class="px-4 py-2">Nama Beasiswa</th>
                <th class="px-4 py-2">Penyelenggara</th>
                <th class="px-4 py-2">Periode</th>
                <th class="px-4 py-2">Kuota</th>
                <th class="px-4 py-2">Deskripsi</th>
                @if ($role !== 'mahasiswa')
                    <th class="px-4 py-2">Aksi</th>
                @else
                    <th class="px-4 py-2">Apply</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($beasiswas as $beasiswa)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $beasiswa->nama_beasiswa }}</td>
                    <td class="px-4 py-2">{{ $beasiswa->nama_penyelenggara }}</td>
                    <td class="px-4 py-2">{{ $beasiswa->periode }}</td>
                    <td class="px-4 py-2">
                        {{ $beasiswa->kuota }}
                        @if ($beasiswa->status === 'full')
                            <span class="ml-2 text-sm text-red-600 font-semibold">(Penuh)</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $beasiswa->deskripsi }}</td>

                    @if ($role === 'admin' || $role === 'dosen')
                        <td class="px-4 py-2 space-x-2">
                            <button wire:click="edit({{ $beasiswa->id }})"
                                class="bg-blue-600 hover:bg-blue-800 text-white px-3 py-1 rounded">Edit</button>
                            <button wire:click="confirmDelete({{ $beasiswa->id }})"
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                                Hapus</button>
                        </td>
                    @elseif($role === 'mahasiswa')
                        <td class="px-4 py-2">
                            @if ($beasiswa->status === 'full')
                                <span class="text-red-500 font-semibold">Pendaftaran Ditutup</span>
                            @else
                                <button wire:click="apply({{ $beasiswa->id }})"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">Apply</button>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center text-gray-500">Data beasiswa tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $beasiswas->links() }}
    </div>

    {{-- Modal Apply (hanya tampil untuk mahasiswa saat klik Apply) --}}
    @if ($selectedBeasiswa && $role === 'mahasiswa')
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-md w-96">
                <h3 class="text-lg font-semibold mb-4">Beasiswa</h3>
                <div class="mb-2"><strong>Nama Beasiswa</strong> : {{ $selectedBeasiswa->nama_beasiswa }}</div>
                <div class="mb-2"><strong>Penyelenggara</strong> : {{ $selectedBeasiswa->nama_penyelenggara }}</div>
                <div class="mb-2"><strong>Periode</strong> : {{ $selectedBeasiswa->periode }}</div>
                <div class="mb-2"><strong>Kuota</strong> : {{ $selectedBeasiswa->kuota }}</div>
                <div class="mb-4"><strong>Deskripsi</strong> : {{ $selectedBeasiswa->deskripsi }}</div>

                <div class="flex justify-end space-x-2">
                    <button wire:click="pilih"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Pilih</button>
                    <button wire:click="batal" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Batal</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Form Edit (tampil untuk admin/dosen jika mengedit) --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">
                <h2 class="text-lg font-semibold mb-4">
                    {{ $isEdit ? 'Edit Beasiswa' : 'Tambah Beasiswa' }}
                </h2>

                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                    <div class="grid grid-cols-5 gap-4 mb-3 items-center">
                        <label class="col-span-1 text-sm font-medium">Nama Beasiswa :</label>
                        <input type="text" wire:model="nama_beasiswa"
                            class="col-span-4 border rounded px-2 py-1 w-full" />
                        @error('nama_beasiswa')
                            <span class="text-red-500 text-xs col-span-5">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-5 gap-4 mb-3 items-center">
                        <label class="col-span-1 text-sm font-medium">Penyelenggara :</label>
                        <input type="text" wire:model="nama_penyelenggara"
                            class="col-span-4 border rounded px-2 py-1 w-full"
                            {{ $role === 'dosen' ? 'readonly' : '' }} />
                        @error('nama_penyelenggara')
                            <span class="text-red-500 text-xs col-span-5">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-5 gap-4 mb-3 items-center">
                        <label class="col-span-1 text-sm font-medium">Periode :</label>
                        <input type="text" wire:model="periode" class="col-span-4 border rounded px-2 py-1 w-full" />
                        @error('periode')
                            <span class="text-red-500 text-xs col-span-5">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-5 gap-4 mb-3 items-center">
                        <label class="col-span-1 text-sm font-medium">Kuota :</label>
                        <input type="number" wire:model="kuota" class="col-span-4 border rounded px-2 py-1 w-full" />
                        @error('kuota')
                            <span class="text-red-500 text-xs col-span-5">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-5 gap-4 mb-3 items-start">
                        <label class="col-span-1 text-sm font-medium mt-1">Deskripsi :</label>
                        <textarea wire:model="deskripsi" rows="3" class="col-span-4 border rounded px-2 py-1 w-full"></textarea>
                        @error('deskripsi')
                            <span class="text-red-500 text-xs col-span-5">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" wire:click="closeModal"
                            class="bg-gray-400 hover:bg-gray-500 text-white text-sm px-4 py-2 rounded">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    <livewire:beasiswa.partials.confirmation-modal />

</div>
