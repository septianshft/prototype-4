<div class="p-6 bg-white rounded shadow-md overflow-x-auto">
    @if (!$hideTableMahasiswa)
        <h2 class="text-2xl font-bold mb-4">Detail Beasiswa: {{ $beasiswa->nama_beasiswa }}</h2>

        <table class="w-full text-sm text-left border border-gray-300">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="px-6 py-4 border-b">Nama Mahasiswa</th>
                    <th class="px-6 py-4 border-b">NIM</th>
                    <th class="px-6 py-4 border-b">Program Studi</th>
                    <th class="px-6 py-4 border-b">Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mahasiswas as $apply)
                    <tr class="hover:bg-gray-50 transition-all">
                        <td class="px-6 py-4 border-b">{{ $apply->nama_mahasiswa }}</td>
                        <td class="px-6 py-4 border-b">{{ $apply->nim }}</td>
                        <td class="px-6 py-4 border-b">
                            {{ $apply->program_studi ?? '-' }} ({{ $apply->jenjang ?? '-' }})
                        </td>
                        <td class="px-6 py-4 border-b">
                            <button type="button" wire:click="showLaporan({{ $apply->user_id }})"
                                class="text-blue-600 underline hover:text-blue-800 text-sm mr-2">
                                Lihat Progress
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if (!$laporanDetail)
            <div class="flex justify-end mt-4">
                <a href="{{ route('beasiswa') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                    Kembali
                </a>
            </div>
        @endif
    @endif

    {{-- Progress Laporan --}}
    @if ($laporanDetail)

        <h3 class="text-2xl font-bold mb-4 text-gray-700">Progress Laporan Mahasiswa</h3>

        <table class="w-full text-sm text-left border border-gray-300 mb-4">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="px-6 py-4 border-b">Nama Laporan</th>
                    <th class="px-6 py-4 border-b">Jenis</th>
                    <th class="px-6 py-4 border-b">File</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporanDetail as $laporan)
                    <tr class="hover:bg-gray-50 transition-all">
                        <td class="px-6 py-4 border-b">{{ $laporan['nama_laporan'] }}</td>
                        <td class="px-6 py-4 border-b capitalize">{{ $laporan['jenis_laporan'] }}</td>
                        <td class="px-6 py-4 border-b">
                            @if ($laporan['file_path'])
                                <a href="{{ $laporan['file_path'] }}" target="_blank"
                                    class="text-blue-600 underline hover:text-blue-800">Lihat File</a>
                            @else
                                <span class="text-gray-500 italic">Tidak ada file</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                            Belum ada laporan yang dikumpulkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="flex justify-end">
            <button type="button" wire:click="hideLaporan" class="bg-blue-600 text-white px-4 py-2 rounded mb-4">
                Kembali
            </button>
        </div>
    @endif
