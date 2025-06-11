<div class="p-6 bg-white rounded shadow-md overflow-x-auto">
    <table class="w-full text-sm text-left border border-gray-300">
        <thead class="bg-gray-200 text-gray-700">
            <tr>
                <th class="px-6 py-4 border-b">Nama Laporan</th>
                <th class="px-6 py-4 border-b">File</th>
                <th class="px-6 py-4 border-b">Tanggal</th>
                <th class="px-6 py-4 border-b">Status Laporan</th>
                <th class="px-6 py-4 border-b text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporans as $laporan)
                <tr class="hover:bg-gray-50 transition-all">
                    {{-- Nama Laporan --}}
                    <td class="px-6 py-4 border-b text-gray-800">{{ $laporan->nama_laporan }}</td>

                    {{-- File --}}
                    <td class="px-6 py-4 border-b">
                        @if ($laporan->file_path)
                            <a href="{{ Storage::url($laporan->file_path) }}" target="_blank"
                                class="text-blue-600 underline hover:text-blue-800 transition">Lihat File</a>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </td>

                    {{-- Tanggal --}}
                    <td class="px-6 py-4 border-b text-gray-800">
                        {{ $laporan->created_at->format('d M Y') }}
                    </td>

                    {{-- Status Laporan --}}
                    <td class="px-6 py-4 border-b text-gray-800 text-center font-semibold">
                        @if ($laporan->status_acc === 'approved')
                            Disetujui
                        @elseif ($laporan->status_acc === 'rejected')
                            Ditolak
                        @else
                            Pending
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4 border-b text-center">
                        @if (auth()->user()->role === 'mahasiswa')
                            <div class="flex justify-center items-center flex-nowrap gap-2">
                                {{-- Detail --}}
                                <a href="{{ route('laporan.beasiswa.show', $laporan->id) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition text-sm">
                                    Detail
                                </a>

                                {{-- Edit --}}
                                <button wire:click="emitEditLaporan({{ $laporan->id }})"
                                    class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded transition text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>

                                {{-- Delete --}}
                                <button wire:click="confirmDelete({{ $laporan->id }})"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        @elseif (in_array(auth()->user()->role, ['admin', 'dosen', 'vicedirector']))
                            {{-- ADMIN / DOSEN / VICE DIRECTOR hanya bisa Lihat Detail --}}
                            <a href="{{ route('laporan.beasiswa.show', $laporan->id) }}"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-flex items-center justify-center transition"
                                title="Lihat Detail">
                                Detail
                            </a>
                        @endif
                    </td>
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
