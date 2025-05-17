<div class="overflow-x-auto bg-white rounded-lg shadow-md">
    {{-- Table --}}
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
                    <td class="px-6 py-4 border-b text-black">{{ $laporan->nama_laporan }}</td>
                    <td class="px-6 py-4 border-b text-black">{{ $laporan->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 border-b">
                        @if ($laporan->file_path)
                            <a href="{{ Storage::url($laporan->file_path) }}" target="_blank"
                                class="text-blue-600 underline hover:text-blue-800 transition-all">Lihat File</a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 border-b text-black">{{ $laporan->created_at->format('d M Y') }}</td>
                    @if (auth()->user()->role === 'mahasiswa')
                        <td class="px-6 py-4 border-b space-x-2 text-center">
                            <button wire:click="triggerEdit({{ $laporan->id }})"
                                class="bg-yellow-400 hover:bg-yellow-500 text-black px-4 py-2 rounded-md transition-all">✏️</button>


                            <button wire:click="delete({{ $laporan->id }})"
                                class="bg-red-600 text-white px-4 py-2 rounded-md transition-all">🗑️</button>
                        </td>
                    @elseif(in_array(auth()->user()->role, ['admin', 'dosen', 'vice_director']))
                        <td class="px-6 py-4 border-b text-center">
                            <a href="{{ route('laporan.beasiswa.show', $laporan->id) }}"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md inline-flex items-center justify-center transition-all"
                                title="Lihat Detail">👁️</a>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data laporan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $laporans->links() }}
    </div>
</div>
