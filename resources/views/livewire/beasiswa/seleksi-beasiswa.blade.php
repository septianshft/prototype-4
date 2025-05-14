<div class="p-6 bg-gray-100 min-h-screen">
    <h2 class="text-2xl font-bold text-gray-700 mb-4">Seleksi Beasiswa</h2>

    {{-- Tabel Seleksi Beasiswa --}}
    <table class="min-w-full text-sm text-left text-gray-700 bg-white rounded shadow-md">
        <thead class="bg-gray-200 text-gray-700">
            <tr>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">NIM</th>
                <th class="px-4 py-2">IPK</th>
                <th class="px-4 py-2">Program Studi</th>
                <th class="px-4 py-2">Status Seleksi</th>
                @if ($role === 'dosen')
                    <th class="px-4 py-2">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($mahasiswa as $mhs)
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-2">{{ $mhs->nama_mahasiswa }}</td>
                    <td class="px-4 py-2">{{ $mhs->nim }}</td>
                    <td class="px-4 py-2">{{ $mhs->ipk }}</td>
                    <td class="px-4 py-2">{{ $mhs->program_studi }}</td>
                    <td class="px-4 py-2">
                        @php
                            $status = $mhs->status_seleksi;
                            $badgeColor = match ($status) {
                                'diterima' => 'bg-green-100 text-green-800',
                                'ditolak' => 'bg-red-100 text-red-800',
                                default => 'bg-yellow-100 text-yellow-800',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                            {{ ucfirst($status ?? 'belum diseleksi') }}
                        </span>
                    </td>

                    @if ($role === 'dosen')
                        <td class="px-4 py-2 space-x-2">
                            <button wire:click="accept({{ $mhs->id }})"
                                class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded disabled:opacity-50"
                                @if ($mhs->status_seleksi === 'diterima') disabled @endif>
                                ✅ Terima
                            </button>
                            <button wire:click="reject({{ $mhs->id }})"
                                class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded disabled:opacity-50"
                                @if ($mhs->status_seleksi === 'ditolak') disabled @endif>
                                ❌ Tolak
                            </button>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
