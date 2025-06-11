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
        <div class="flex flex-col max-h-[calc(100vh-300px)] overflow-hidden">
            <div class="overflow-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
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
                            class="flex flex-col p-4 border rounded-lg shadow bg-white h-full
    {{ !$canApply && !$isAdminOrDosen && $role !== 'vicedirector' ? 'opacity-50 pointer-events-none bg-gray-100' : '' }}">

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

                                <!-- Tambahan ketentuan persyaratan -->
                                <p class="text-xs text-gray-500 font-semibold mt-1">
                                    Persyaratan Upload:
                                    @if ($beasiswa->require_file)
                                        <span
                                            class="text-blue-600">{{ $beasiswa->persyaratan_file_name ?? '-' }}</span>
                                    @else
                                        <span class="text-gray-600">Tidak memerlukan upload file</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mt-4 flex flex-col space-y-2 text-right">
                                @if (in_array($role, ['admin', 'dosen', 'vicedirector']))
                                    <!-- Display only the "Lihat Detail" link for Vice Director, Admin, and Dosen -->
                                    <a href="{{ route('beasiswa.detail', ['beasiswa' => $beasiswa->id]) }}"
                                        class="text-blue-600 underline hover:text-blue-800 text-sm">
                                        Lihat Detail
                                    </a>
                                @endif

                                @if ($role === 'mahasiswa')
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

                                @if ($role !== 'vicedirector')
                                    @if ($isAdminOrDosen)
                                        <button wire:click="edit({{ $beasiswa->id }})"
                                            class="flex items-center justify-center px-2 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>

                                        <button wire:click="confirmDelete({{ $beasiswa->id }})"
                                            class="flex items-center justify-center px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>

                    @empty
                        <p class="text-center text-gray-500 col-span-full">Data beasiswa tidak ditemukan.</p>
                    @endforelse
                </div>
            </div>
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

                    @if ($selectedBeasiswa->require_file)
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">
                                Upload {{ $selectedBeasiswa->persyaratan_file_name }}
                            </label>
                            <input type="file" wire:model="file_persyaratan"
                                class="border rounded px-2 py-1 w-full" />
                            @error('file_persyaratan')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
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

                        <div class="grid grid-cols-5 gap-4 mb-3 items-start" x-data="{ requireFile: @entangle('require_file') }">
                            <label class="col-span-1 text-sm font-medium mt-1">Persyaratan Upload :</label>

                            <div class="col-span-4 space-y-2">
                                <!-- Radio Tidak Perlu Upload -->
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="require_file" value="0"
                                            class="form-radio text-blue-600">
                                        <span class="ml-2 text-sm text-gray-700">Tidak memerlukan upload file</span>
                                    </label>
                                </div>

                                <!-- Radio Perlu Upload -->
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="require_file" value="1"
                                            class="form-radio text-blue-600">
                                        <span class="ml-2 text-sm text-gray-700">Memerlukan upload file berikut:</span>
                                    </label>
                                </div>

                                <!-- Input Nama File (tampil kalau perlu upload) -->
                                <div x-show="requireFile == 1" x-transition class="mt-2">
                                    <input type="text" wire:model="persyaratan_file_name"
                                        class="border rounded px-2 py-1 w-full"
                                        placeholder="Contoh: Rekap Nilai per Semester, Transkrip, dll">
                                    @error('persyaratan_file_name')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
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

        <!-- Modal Upload Apply -->
        @if ($showApplyModal && $role === 'mahasiswa')
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-auto">
                <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative animate-fadeIn">
                    <button wire:click="resetApplyModal"
                        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>

                    <h3 class="text-xl font-semibold mb-4 text-center">
                        Upload Persyaratan - {{ $selectedBeasiswa->nama_beasiswa }}
                    </h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">
                            Upload {{ $selectedBeasiswa->persyaratan_file_name }}
                        </label>
                        <input type="file" wire:model="applyFile" class="border rounded px-2 py-1 w-full" />
                        @error('applyFile')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button wire:click="submitApply"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold">
                            Submit Apply
                        </button>
                        <button wire:click="resetApplyModal"
                            class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if ($showApplyDetailModal && $role === 'mahasiswa')
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-auto">
                <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative animate-fadeIn">
                    <button wire:click="$set('showApplyDetailModal', false)"
                        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>

                    <h3 class="text-xl font-semibold mb-4 text-center">
                        Detail Apply - {{ $applyDetail->beasiswa->nama_beasiswa ?? '-' }}
                    </h3>

                    <div class="mb-2"><strong>Status Apply:</strong> {{ ucfirst($applyDetail->status) }}</div>

                    <div class="mb-4">
                        <strong>File Persyaratan:</strong>
                        @if ($applyDetail->file_persyaratan_path)
                            <a href="{{ asset('storage/' . $applyDetail->file_persyaratan_path) }}" target="_blank"
                                class="text-blue-600 underline hover:text-blue-800">
                                Lihat File
                            </a>
                        @else
                            <span class="text-gray-600">Belum upload file</span>
                        @endif
                    </div>

                    <div class="flex justify-end">
                        <button wire:click="$set('showApplyDetailModal', false)"
                            class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif



        <livewire:beasiswa.partials.confirmation-modal />
    </div>

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
