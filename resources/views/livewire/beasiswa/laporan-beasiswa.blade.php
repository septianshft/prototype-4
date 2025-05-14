<div class="p-6 bg-gray-100 min-h-screen">
    <h2 class="text-2xl font-bold text-gray-700 mb-4">Laporan Beasiswa</h2>

    {{-- Tombol Tambah --}}
    <div class="mb-4">
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            + Data Laporan
        </button>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="w-full text-sm text-left border border-gray-300">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="px-4 py-2 border-b">Nama Laporan</th>
                    <th class="px-4 py-2 border-b">Nama Mahasiswa</th>
                    <th class="px-4 py-2 border-b">File</th>
                    <th class="px-4 py-2 border-b">Tanggal</th>
                    <th class="px-4 py-2 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporans as $laporan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border-b dark:text-black">{{ $laporan->nama_laporan }}</td>
                        <td class="px-4 py-2 border-b dark:text-black">{{ $laporan->user->name ?? '-' }}</td>
                        <td class="px-4 py-2 border-b">
                            @if ($laporan->file_path)
                                <a href="{{ Storage::url($laporan->file_path) }}" target="_blank"
                                    class="text-blue-600 underline">Lihat File</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-2 border-b dark:text-black">{{ $laporan->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2 border-b">
                            <div class="flex space-x-2">
                                <button wire:click="edit({{ $laporan->id }})"
                                    class="bg-yellow-400 hover:bg-yellow-500 text-black p-1 rounded"
                                    title="Edit">✏️</button>
                                <button wire:click="delete({{ $laporan->id }})"
                                    class="bg-red-600 hover:bg-red-700 text-white p-1 rounded"
                                    title="Hapus">🗑️</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data laporan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $laporans->links() }}
    </div>

    {{-- Modal Form --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6">
                <h2 class="text-xl font-bold mb-4">Tambah Laporan</h2>

                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                    {{-- Nama Laporan --}}
                    <div class="flex items-center mb-4">
                        <label class="w-1/4 text-right pr-4 font-medium dark:text-black">Nama Laporan :</label>
                        <div class="w-3/4">
                            <input type="text" wire:model="nama_laporan"
                                class="w-full border rounded px-3 py-2 dark:text-black" />
                            @error('nama_laporan')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Unggah File --}}
                    <div class="flex items-start mb-6">
                        <label class="w-1/4 text-right pr-4 font-medium pt-3 dark:text-black">Unggah File :</label>
                        <div class="w-3/4">
                            <div class="border-2 border-dashed border-gray-300 rounded p-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-500 mb-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a1 1 0 001 1h14a1 1 0 001-1v-1m-5-4l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="dark:text-black">
                                        Seret & Jatuhkan Berkas atau
                                        <label for="fileInput"
                                            class="text-blue-600 cursor-pointer underline">Jelajahi</label>
                                    </p>
                                    <input id="fileInput" type="file" wire:model="file" class="hidden" />

                                    {{-- ✅ Menampilkan nama file yang dipilih --}}
                                    @if ($file)
                                        <p class="mt-2 text-green-600 text-sm">File dipilih:
                                            <strong>{{ $file->getClientOriginalName() }}</strong>
                                        </p>
                                    @endif

                                    {{-- ✅ Menampilkan error --}}
                                    @error('file')
                                        <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="text-right space-x-2">
                        <button type="button" wire:click="closeModal"
                            class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded text-gray-700">Batal</button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
