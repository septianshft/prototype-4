@if ($role === 'admin' || $role === 'dosen')
    <button wire:click="create" class="bg-blue-600 text-white px-4 py-2 rounded mb-4">
        + Data Beasiswa
    </button>
@endif
<div class="overflow-auto max-h-[400px] rounded shadow-md border border-gray-300">
    <table class="min-w-full text-sm text-left text-gray-700 bg-white">
        <thead class="bg-gray-100 text-gray-700 border-b border-gray-300">
            <tr>
                <th class="px-6 py-3 font-semibold sticky top-0 bg-gray-100">Nama Beasiswa</th>
                <th class="px-6 py-3 font-semibold sticky top-0 bg-gray-100">Penyelenggara</th>
                <th class="px-6 py-3 font-semibold sticky top-0 bg-gray-100">Periode</th>
                <th class="px-6 py-3 font-semibold sticky top-0 bg-gray-100">Kuota</th>
                <th class="px-6 py-3 font-semibold sticky top-0 bg-gray-100">Deskripsi</th>
                @if ($role != 'mahasiswa')
                    <th class="px-6 py-3 font-semibold sticky top-0 bg-gray-100">Aksi</th>
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
                                class="bg-blue-600 hover:bg-blue-800 px-3 py-1 rounded text-white text-sm font-semibold">
                                Edit
                            </button>
                            <button wire:click="confimDelete({{ $data->id }})"
                                class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white text-sm font-semibold">
                                🗑️ Hapus
                            </button>
                        </td>
                    @elseif($role === 'mahasiswa')
                        <td class="px-6 py-3">
                            <button>
                                <a href="{{ route('beasiswa.apply', ['id' => $data->id]) }}"
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
</div>

<!-- Modal tetap di luar -->
<livewire:beasiswa.partials.confirmation-modal />
