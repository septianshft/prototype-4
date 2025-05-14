<div>
    <div class="p-6 bg-gray-100 min-h-screen">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Manajemen Pengguna</h2>

        {{-- Tombol Tambah & Pencarian --}}
        <div class="flex justify-between items-center mb-4">
            <button wire:click="openModal"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded shadow">
                + Tambah Pengguna
            </button>
            <input type="text" wire:model="search" placeholder="🔍 Cari email/role"
                class="border border-gray-300 rounded px-3 py-2 text-sm w-64 shadow-sm" />
        </div>

        {{-- Tabel Pengguna --}}
        <div class="overflow-auto bg-white rounded shadow">
            <table class="min-w-full text-sm text-left text-gray-800">
                <thead class="bg-gray-200 text-gray-700 font-semibold">
                    <tr>
                        <th class="px-4 py-3 border-b">Nama</th>
                        <th class="px-4 py-3 border-b">Email</th>
                        <th class="px-4 py-3 border-b">Role</th>
                        <th class="px-4 py-3 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border-b">{{ $user->name }}</td>
                            <td class="px-4 py-2 border-b">{{ $user->email }}</td>
                            <td class="px-4 py-2 border-b capitalize">{{ $user->role }}</td>
                            <td class="px-4 py-2 border-b space-x-2">
                                <button wire:click="edit({{ $user->id }})"
                                    class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded shadow">✏️</button>
                                <button wire:click="delete({{ $user->id }})"
                                    class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded shadow">🗑️</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada data ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $users->links() }}
        </div>

        {{-- Modal Form --}}
        @if ($showModal)
            <div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        {{ $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna' }}
                    </h3>

                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" wire:model="name"
                                class="w-full border px-3 py-2 rounded shadow-sm focus:outline-none focus:ring focus:border-blue-400  dark:text-black"" />
                            @error('name')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" wire:model="email"
                                class="w-full border px-3 py-2 rounded shadow-sm focus:outline-none focus:ring focus:border-blue-400  dark:text-black"" />
                            @error('email')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Role</label>
                            <select wire:model="role"
                                class="w-full border px-3 py-2 rounded shadow-sm focus:outline-none focus:ring focus:border-blue-400 dark:text-black">
                                <option value="">-- Pilih Role --</option>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                                <option value="vicedirector">Vice Director</option>
                            </select>
                            @error('role')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="text-right">
                            <button type="button" wire:click="closeModal"
                                class="bg-gray-300 px-4 py-2 rounded shadow mr-2 hover:bg-gray-400">Batal</button>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                                {{ $isEdit ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
