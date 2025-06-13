<div class="p-4 bg-white shadow rounded">
    <h2 class="text-2xl font-bold text-gray-700 mb-4">Seleksi Mahasiswa</h2>

    {{-- Search --}}
    <div class="mb-4">
        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari:</label>
        <input type="text" id="search" wire:model.live="search" placeholder="Cari NIM, Nama, Status, Prodi, Beasiswa"
            class="border px-4 py-2 rounded w-full" />
    </div>

    {{-- Tabel --}}
    <table class="min-w-full text-sm text-left text-gray-700 bg-white rounded shadow-md">
        <thead class="bg-gray-200 text-gray-700">
            <tr>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">NIM</th>
                <th class="px-4 py-2">IPK</th>
                <th class="px-4 py-2">Program Studi</th>
                <th class="px-4 py-2">Nama Beasiswa</th>
                <th class="px-4 py-2">Status Seleksi</th>
                <th class="px-4 py-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pendaftar as $mhs)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $mhs->nama_mahasiswa }}</td>
                    <td class="px-4 py-2">{{ $mhs->nim }}</td>
                    <td class="px-4 py-2">{{ $mhs->ipk }}</td>
                    <td class="px-4 py-2">
                        {{ $mhs->nama_program_studi ?? '-' }} ({{ $mhs->jenjang ?? '-' }})
                    </td>
                    <td class="px-4 py-2">{{ $mhs->nama_beasiswa ?? '-' }}</td>
                    <td class="px-4 py-2">
                        @php
                            $statusSeleksi = $mhs->display_status ?? 'pending'; // pakai display_status!
                            $badgeColor = match ($statusSeleksi) {
                                'diterima' => 'bg-green-100 text-green-800',
                                'ditolak' => 'bg-red-100 text-red-800',
                                'syarat ditolak' => 'bg-red-100 text-red-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-yellow-100 text-yellow-800',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                            {{ ucfirst($statusSeleksi) }}
                        </span>

                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-2 space-x-1 text-center">
                        @if (in_array($role, ['dosen']))
                            {{-- ACC --}}
                            <button wire:click="accept({{ $mhs->apply_id }})"
                                class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded disabled:opacity-50"
                                @if ($mhs->display_status !== 'pending' || $mhs->hasAccepted) disabled @endif>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </button>

                            {{-- Tolak --}}
                            <button wire:click="reject({{ $mhs->apply_id }})"
                                class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded disabled:opacity-50"
                                @if ($mhs->display_status !== 'pending' || $mhs->hasAccepted) disabled @endif>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif

                        {{-- Detail (semua role boleh lihat) --}}
                        <a href="{{ route('seleksi.beasiswa.detail', ['apply' => $mhs->apply_id]) }}"
                            class="bg-blue-600 hover:bg-blue-800 text-white px-3 py-1 rounded inline-flex items-center justify-center transition text-sm"
                            title="Lihat Detail">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                        Tidak ada data yang sesuai dengan filter
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $pendaftar->links() }}
    </div>
</div>
