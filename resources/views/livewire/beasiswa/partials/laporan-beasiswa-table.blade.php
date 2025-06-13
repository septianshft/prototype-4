<div>
    <div class="p-6 bg-white rounded shadow-md overflow-x-auto">

        {{-- STEP 1: Display Students (Admin/Dosen/Vice Director) --}}
        @if (
            (auth()->user()->role === 'dosen' || auth()->user()->role === 'admin' || auth()->user()->role === 'vicedirector') &&
                is_null($selectedMahasiswaId))
            <h2 class="text-xl font-semibold mb-4">Daftar Mahasiswa Penerima Beasiswa</h2>

            <table class="w-full text-sm text-left border border-gray-300">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="px-6 py-4 border-b">Nama Mahasiswa</th>
                        <th class="px-6 py-4 border-b">NIM</th>
                        <th class="px-6 py-4 border-b">Program Studi</th>
                        <th class="px-6 py-4 border-b">Nama Beasiswa</th> <!-- Add this column -->
                        <th class="px-6 py-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mahasiswas as $mhs)
                        <tr class="hover:bg-gray-50 transition-all">
                            <td class="px-6 py-4 border-b text-gray-800">{{ $mhs->nama_mahasiswa }}</td>
                            <td class="px-6 py-4 border-b text-gray-800">{{ $mhs->nim }}</td>
                            <td class="px-6 py-4 border-b text-gray-800">{{ $mhs->program_studi }} ({{ $mhs->jenjang }})
                            </td>
                            <td class="px-6 py-4 border-b text-gray-800">{{ $mhs->nama_beasiswa }}</td>
                            <!-- Display Nama Beasiswa -->
                            <td class="px-6 py-4 border-b text-center">
                                <button wire:click="selectMahasiswa({{ $mhs->user_id }})"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition text-sm">
                                    Lihat Laporan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-sm text-gray-500">Tidak ada mahasiswa
                                penerima beasiswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif (
            (auth()->user()->role === 'dosen' || auth()->user()->role === 'admin' || auth()->user()->role === 'vicedirector') &&
                $selectedMahasiswaId)
            <h2 class="text-xl font-semibold mb-4">Laporan Mahasiswa</h2>

            {{-- Table Laporan --}}
            @include('livewire.beasiswa.partials._table-laporan', ['laporans' => $laporans ?? []])

            {{-- Button Kembali --}}
            <div class="mt-4 flex justify-end">
                <button wire:click="$set('selectedMahasiswaId', null)"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition">
                    Kembali
                </button>
            </div>
        @elseif (auth()->user()->role === 'mahasiswa')
            <h2 class="text-xl font-semibold mb-4">Laporan Beasiswa</h2>

            {{-- Table Laporan --}}
            @include('livewire.beasiswa.partials._table-laporan', ['laporans' => $laporans ?? []])
        @endif

    </div>
</div>
