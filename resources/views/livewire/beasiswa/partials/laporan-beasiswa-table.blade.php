<div class="p-6 bg-white rounded shadow-md overflow-x-auto">
    <table class="w-full text-sm text-left border border-gray-300">
        <thead class="bg-gray-200 text-gray-700">
            <tr>
                <th class="px-6 py-4 border-b">Nama Laporan</th>
                <th class="px-6 py-4 border-b">Nama Mahasiswa</th>
                <th class="px-6 py-4 border-b">File</th>
                <th class="px-6 py-4 border-b">Tanggal</th>
                @if (in_array(auth()->user()->role, ['mahasiswa', 'admin', 'dosen', 'vice_director']))
                    <th class="px-6 py-4 border-b text-center">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($laporans as $laporan)
                <tr class="hover:bg-gray-50 transition-all">
                    <td class="px-6 py-4 border-b text-gray-800">{{ $laporan->nama_laporan }}</td>
                    <td class="px-6 py-4 border-b text-gray-800">{{ $laporan->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 border-b">
                        @if ($laporan->file_path)
                            <a href="{{ Storage::url($laporan->file_path) }}" target="_blank"
                                class="text-blue-600 underline hover:text-blue-800 transition">Lihat File</a>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 border-b text-gray-800">
                        {{ $laporan->created_at->format('d M Y') }}
                    </td>

                    {{-- Aksi --}}
                    @if (auth()->user()->role === 'mahasiswa')
                        <td class="px-6 py-4 border-b text-center space-x-2">
                            <button wire:click="triggerEdit({{ $laporan->id }})"
                                class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded transition">
                                Edit
                            </button>
                            <button wire:click="confirmDelete({{ $laporan->id }})"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition">
                                🗑️
                            </button>
                        </td>
                    @elseif(in_array(auth()->user()->role, ['admin', 'dosen', 'vice_director']))
                        <td class="px-6 py-4 border-b text-center">
                            <a href="{{ route('laporan.beasiswa.show', $laporan->id) }}"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center justify-center transition"
                                title="Lihat Detail">Detail</a>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-sm text-gray-500">
                        Tidak ada data laporan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if ($laporans instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">
            {{ $laporans->links() }}
        </div>
    @endif

    {{-- Modal Konfirmasi --}}
    <livewire:beasiswa.partials.confirmation-modal />
</div>
