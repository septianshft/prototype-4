<div class="p-4 bg-white rounded shadow">

    <h3 class="text-xl font-bold text-gray-700 mb-4">Daftar Mahasiswa (Diterima)</h3>

    <table class="min-w-full text-sm text-left border border-gray-300">
        <thead class="bg-gray-200 text-gray-700">
            <tr>
                <th class="px-4 py-2">Nama Mahasiswa</th>
                <th class="px-4 py-2">NIM</th>
                <th class="px-4 py-2">Program Studi</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mahasiswas as $mhs)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $mhs->nama_mahasiswa }}</td>
                    <td class="px-4 py-2">{{ $mhs->nim }}</td>
                    <td class="px-4 py-2">
                        {{ $mhs->program_studi }} ({{ $mhs->jenjang }})
                    </td>
                    <td class="px-4 py-2">
                    <button wire:click="showLaporan({{ $mhs->user_id }})"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                            Lihat Laporan
                        </button>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-sm text-gray-500">
                        Tidak ada data mahasiswa diterima.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
