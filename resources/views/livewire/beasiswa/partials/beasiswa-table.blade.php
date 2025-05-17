@if ($role === 'admin' || $role === 'dosen')
    <button wire:click="create" class="bg-blue-600 text-white px-4 py-2 rounded mb-4">
        + Data Beasiswa
    </button>
@endif
<table class="min-w-full text-sm text-left text-gray-700 bg-white rounded shadow-md border border-gray-300">
    <thead class="bg-gray-100 text-gray-700 border-b border-gray-300">
        <tr>
            <th class="px-6 py-3 font-semibold">Nama Beasiswa</th>
            <th class="px-6 py-3 font-semibold">Penyelenggara</th>
            <th class="px-6 py-3 font-semibold">Periode</th>
            <th class="px-6 py-3 font-semibold">Kuota</th>
            <th class="px-6 py-3 font-semibold">Deskripsi</th>
            @if ($role != 'mahasiswa')
                <th class="px-6 py-3 font-semibold">Aksi</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($beasiswas as $data)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-6 py-3">{{ $data->nama_beasiswa }}</td>
                <td class="px-6 py-3">{{ $data->nama_penyelenggara }}</td>
                <td class="px-6 py-3">{{ \Carbon\Carbon::parse($data->periode)->format('Y-m-d') }}</td>
                <td class="px-6 py-3">{{ $data->kuota }}</td>
                <td class="px-6 py-3">{{ $data->deskripsi }}</td>
                @if ($role === 'admin' || $role === 'dosen')
                    <td class="px-6 py-3 space-x-2">
                        <button wire:click="edit({{ $data->id }})"
                            class="bg-yellow-400 hover:bg-yellow-500 px-3 py-1 rounded text-white text-sm font-semibold">
                            ✏️ Edit
                        </button>
                        <button wire:click="delete({{ $data->id }})"
                            class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white text-sm font-semibold">
                            🗑️ Hapus
                        </button>
                    </td>
                @elseif($role === 'mahasiswa')
                    <td class="px-6 py-3">
                        <button> <a href="{{ route('beasiswa.apply', ['id' => $data->id]) }}"
                                class="bg-green-600 hover:bg-green-700 px-4 py-1 rounded text-white text-sm font-semibold">
                                Apply
                            </a>
                        </button>
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
