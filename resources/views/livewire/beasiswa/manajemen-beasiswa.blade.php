<div>
    <div class="p-6 bg-white rounded shadow-md">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Manajemen Beasiswa</h2>

        {{-- Notifikasi --}}
        @if (session()->has('success'))
            <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('message'))
            <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-3 bg-red-200 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if ($role === 'admin' || $role === 'dosen')
            <button wire:click="openModal" class="bg-blue-600 text-white px-4 py-2 rounded mb-4">
                + Data Beasiswa
            </button>
        @endif

        {{-- Pencarian --}}
        <div class="mb-4">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Cari nama beasiswa..."
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" />
        </div>

        {{-- Daftar Beasiswa --}}
        <div class="space-y-4">
            @forelse ($beasiswas as $beasiswa)
                @php
                    $mahasiswaJenjang = strtolower(auth()->user()->dataMahasiswa->programStudi->jenjang ?? '');
                    $beasiswaJenjang = strtolower($beasiswa->programStudi->jenjang ?? '');
                    $mahasiswaPSId = auth()->user()->dataMahasiswa->program_studi_id ?? null;
                    $beasiswaPSId = $beasiswa->program_studi_id ?? null;

                    // Prodi dan jenjang sama?
                    $isJenjangSama = $mahasiswaJenjang === $beasiswaJenjang;
                    $isProgramStudiSama = $mahasiswaPSId === $beasiswaPSId;

                    $isExpired =
                        $beasiswa->deadline_pendaftaran &&
                        \Carbon\Carbon::parse($beasiswa->deadline_pendaftaran)->isPast();

                    $isAdminOrDosen = in_array($role, ['admin', 'dosen']);

                    // Hanya boleh apply jika prodi dan jenjang sama dan belum expired dan bukan admin/dosen
                    $canApply = !$isAdminOrDosen && !$isExpired && $isJenjangSama && $isProgramStudiSama;
                @endphp

                <div
                    class="flex justify-between items-center p-4 border rounded-lg shadow bg-white
                    {{ !$canApply && !$isAdminOrDosen ? 'opacity-50 pointer-events-none bg-gray-100' : '' }}">

                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $beasiswa->nama_beasiswa }}</h3>
                        <p class="text-sm text-gray-600">
                            Penyelenggara: {{ $beasiswa->nama_penyelenggara }} |
                            Periode: {{ $beasiswa->periode }} |
                            Program Studi: {{ $beasiswa->programStudi->program_studi ?? '-' }}
                            ({{ $beasiswa->programStudi->jenjang ?? '-' }})
                            |
                            Kuota: {{ $beasiswa->kuota }}
                            @if ($beasiswa->status === 'full')
                                <span class="text-red-600 font-semibold">(Penuh)</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-700 mt-1 line-clamp-3">{{ $beasiswa->deskripsi }}</p>
                        <p class="text-xs text-gray-500 font-semibold mt-1">
                            Tenggat Pendaftaran:
                            {{ \Carbon\Carbon::parse($beasiswa->deadline_pendaftaran)->translatedFormat('d M Y') }}
                        </p>
                    </div>

                    <div class="ml-4 flex-shrink-0 flex flex-col space-y-2 text-right">
                        @if ($isAdminOrDosen)
                            <button wire:click="edit({{ $beasiswa->id }})"
                                class="px-4 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-semibold">
                                Edit
                            </button>
                            <button wire:click="confirmDelete({{ $beasiswa->id }})"
                                class="px-4 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-semibold">
                                Hapus
                            </button>
                        @elseif ($role === 'mahasiswa')
                            @if ($isExpired)
                                <div
                                    class="px-4 py-1 bg-red-100 text-red-700 rounded text-sm font-semibold cursor-not-allowed select-none">
                                    Pendaftaran Ditutup
                                </div>
                            @elseif ($canApply)
                                <button wire:click="apply({{ $beasiswa->id }})"
                                    class="px-4 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-semibold">
                                    Apply
                                </button>
                            @else
                                <div
                                    class="px-4 py-1 bg-gray-200 text-gray-600 rounded text-sm font-semibold cursor-not-allowed select-none opacity-60">
                                    Jenjang program studi tidak cocok
                                </div>
                            @endif
                        @endif
                        @if ($isAdminOrDosen)
                            <a href="{{ route('beasiswa.detail', ['beasiswa' => $beasiswa->id]) }}"
                                class="text-blue-600 underline hover:text-blue-800 text-sm">
                                Lihat Detail
                            </a>
                        @endif

                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500">Data beasiswa tidak ditemukan.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $beasiswas->links() }}
        </div>

        {{-- Modal Apply --}}
        @if ($selectedBeasiswa && $role === 'mahasiswa')
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-auto">
                <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative animate-fadeIn">
                    <button wire:click="batal"
                        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
                    <h3 class="text-xl font-semibold mb-4 text-center">{{ $selectedBeasiswa->nama_beasiswa }}</h3>
                    <div class="mb-2"><strong>Penyelenggara:</strong> {{ $selectedBeasiswa->nama_penyelenggara }}
                    </div>
                    <div class="mb-2"><strong>Periode:</strong> {{ $selectedBeasiswa->periode }}</div>
                    <div class="mb-2"><strong>Tenggat Pendaftaran:</strong>
                        {{ \Carbon\Carbon::parse($selectedBeasiswa->deadline_pendaftaran ?? now())->translatedFormat('d M Y') }}
                    </div>
                    <div class="mb-2"><strong>Kuota:</strong> {{ $selectedBeasiswa->kuota }}</div>
                    <div class="mb-4"><strong>Deskripsi:</strong> {{ $selectedBeasiswa->deskripsi }}</div>

                    <div class="flex justify-end space-x-2">
                        <button wire:click="pilih"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">
                            Pilih
                        </button>
                        <button wire:click="batal"
                            class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Batal</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Tambah/Edit Beasiswa --}}
        @if ($showModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        {{ $isEdit ? 'Edit Beasiswa' : 'Tambah Beasiswa' }}
                    </h2>

                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                        <div class="grid grid-cols-5 gap-4 mb-3 items-center">
                            <label class="col-span-1 text-sm font-medium">Nama Beasiswa :</label>
                            <input type="text" wire:model="nama_beasiswa"
                                class="col-span-4 border rounded px-2 py-1 w-full" />
                            @error('nama_beasiswa')
                                <span class="text-red-500 text-xs col-span-5">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-5 gap-4 mb-3 items-center">
                            <label class="col-span-1 text-sm font-medium">Penyelenggara :</label>
                            <input type="text" wire:model="nama_penyelenggara"
                                class="col-span-4 border rounded px-2 py-1 w-full"
                                {{ $role === 'dosen' ? 'readonly' : '' }} />
                            @error('nama_penyelenggara')
                                <span class="text-red-500 text-xs col-span-5">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-5 gap-4 mb-3 items-center">
                            <label class="col-span-1 text-sm font-medium">Periode :</label>
                            <input type="text" wire:model="periode"
                                class="col-span-4 border rounded px-2 py-1 w-full" />
                            @error('periode')
                                <span class="text-red-500 text-xs col-span-5">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Dropdown Program Studi --}}
                        <div class="grid grid-cols-5 gap-4 mb-3 items-center">
                            <label class="col-span-1 text-sm font-medium">Program Studi :</label>
                            <select wire:model="program_studi_id" class="col-span-4 border rounded px-2 py-1 w-full"
                                required>
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach ($programStudis as $ps)
                                    <option value="{{ $ps->id }}">{{ $ps->program_studi }}
                                        ({{ $ps->jenjang }})
                                    </option>
                                @endforeach
                            </select>

                            @error('program_studi_id')
                                <span class="text-red-500 text-xs col-span-5">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-5 gap-4 mb-3 items-center">
                            <label class="col-span-1 text-sm font-medium">Kuota :</label>
                            <input type="number" wire:model="kuota"
                                class="col-span-4 border rounded px-2 py-1 w-full" />
                            @error('kuota')
                                <span class="text-red-500 text-xs col-span-5">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-5 gap-4 mb-3 items-start">
                            <label class="col-span-1 text-sm font-medium mt-1">Deskripsi :</label>
                            <textarea wire:model="deskripsi" rows="3" class="col-span-4 border rounded px-2 py-1 w-full"></textarea>
                            @error('deskripsi')
                                <span class="text-red-500 text-xs col-span-5">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Tambahan input deadline pendaftaran --}}
                        <div class="grid grid-cols-5 gap-4 mb-3 items-center">
                            <label class="col-span-1 text-sm font-medium">Tenggat Pendaftaran :</label>
                            <input type="date" wire:model="deadline_pendaftaran"
                                class="col-span-4 border rounded px-2 py-1 w-full" />
                            @error('deadline_pendaftaran')
                                <span class="text-red-500 text-xs col-span-5">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-2 mt-6">
                            <button type="button" wire:click="closeModal"
                                class="bg-gray-400 hover:bg-gray-500 text-white text-sm px-4 py-2 rounded">
                                Batal
                            </button>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <livewire:beasiswa.partials.confirmation-modal />
    </div>

    {{-- Animasi Fade In (tailwind + @keyframes) --}}
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.25s ease forwards;
        }
    </style>
</div>
