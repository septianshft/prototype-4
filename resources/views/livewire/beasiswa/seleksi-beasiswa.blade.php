<div class="p-6 bg-white rounded shadow-md">
    <h2 class="text-2xl font-bold text-gray-700 mb-4">
        {{ in_array($role, ['dosen', 'admin']) ? 'Seleksi Beasiswa' : 'Hasil Seleksi Beasiswa' }}
    </h2>

    {{-- Filter Status Seleksi --}}
    <div class="mb-4">
        <label for="sortStatus">Filter Status Seleksi:</label>
        <select id="sortStatus" wire:model="sortStatus" class="border rounded px-3 py-1 text-sm">
            <option value="all">Semua</option>
            <option value="diterima">Diterima</option>
            <option value="ditolak">Ditolak</option>
            <option value="pending">Pending</option>
        </select>
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
                @if (in_array($role, ['dosen', 'admin']))
                    <th class="px-4 py-2">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($pendaftar as $mhs)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $mhs->nama_mahasiswa }}</td>
                    <td class="px-4 py-2">{{ $mhs->nim }}</td>
                    <td class="px-4 py-2">{{ $mhs->ipk }}</td>
                    <td class="px-4 py-2">{{ $mhs->program_studi }}</td>
                    <td class="px-4 py-2">{{ $mhs->beasiswa->nama_beasiswa ?? '-' }}</td>
                    <td class="px-4 py-2">
                        @php
                            $status = $mhs->status;
                            $badgeColor = match ($status) {
                                'diterima' => 'bg-green-100 text-green-800',
                                'ditolak' => 'bg-red-100 text-red-800',
                                default => 'bg-yellow-100 text-yellow-800',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                            {{ ucfirst($status ?? 'pending') }}
                        </span>
                    </td>
                    @if (in_array($role, ['dosen', 'admin']))
                        <td class="px-4 py-2 space-x-2">
                            <button wire:click="accept({{ $mhs->apply_id }})"
                                class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded disabled:opacity-50"
                                @if ($mhs->status !== 'pending') disabled @endif>
                                ✅ Terima
                            </button>
                            <button wire:click="reject({{ $mhs->apply_id }})"
                                class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded disabled:opacity-50"
                                @if ($mhs->status !== 'pending') disabled @endif>
                                ❌ Tolak
                            </button>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
