<div class="p-4 bg-white shadow rounded">
    {{-- Filter Status Seleksi --}}
    <div class="mb-4">
        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari:</label>
        <input type="text" id="search" wire:model.debounce.500ms="search"
            placeholder="Cari NIM, Nama, Status, Prodi, Beasiswa" class="border px-4 py-2 rounded w-full" />
        <p class="text-red-500 mt-2">DEBUG SEARCH: {{ $search }}</p>

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
                @if (in_array($role, ['dosen']))
                    <th class="px-4 py-2">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($pendaftar as $mhs)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $mhs->nama_mahasiswa }}</td>
                    <td class="px-4 py-2">{{ $mhs->nim }}</td>
                    <td class="px-4 py-2">{{ $mhs->ipk }}</td>
                    <td class="px-4 py-2">{{ $mhs->nama_program_studi ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $mhs->nama_beasiswa ?? '-' }}</td>
                    <td class="px-4 py-2">
                        @php
                            $statusSeleksi = $mhs->status ?? 'pending';
                            $badgeColor = match ($statusSeleksi) {
                                'diterima' => 'bg-green-100 text-green-800',
                                'ditolak' => 'bg-red-100 text-red-800',
                                default => 'bg-yellow-100 text-yellow-800',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                            {{ ucfirst($statusSeleksi) }}
                        </span>
                    </td>
                    @if (in_array($role, ['dosen']))
                        <td class="px-4 py-2 space-x-2">
                            <button wire:click="accept({{ $mhs->apply_id }})"
                                class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded disabled:opacity-50"
                                @if ($mhs->status !== 'pending' || $mhs->hasAccepted) disabled @endif>
                                ✅ Terima
                            </button>
                            <button wire:click="reject({{ $mhs->apply_id }})"
                                class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded disabled:opacity-50"
                                @if ($mhs->status !== 'pending' || $mhs->hasAccepted) disabled @endif>
                                ❌ Tolak
                            </button>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ in_array($role, ['dosen', 'admin']) ? 7 : 6 }}"
                        class="px-4 py-4 text-center text-gray-500">
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
