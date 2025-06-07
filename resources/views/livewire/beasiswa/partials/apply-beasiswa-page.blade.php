<div class="space-y-4">

    @foreach ($beasiswas as $beasiswa)
        <div class="p-4 border rounded-lg shadow bg-white">
            <h3 class="text-lg font-semibold text-gray-800">{{ $beasiswa->nama_beasiswa }}</h3>
            <p class="text-sm text-gray-700 mt-1">{{ $beasiswa->deskripsi }}</p>
            <p class="text-sm text-gray-600 mt-1">Kuota: {{ $beasiswa->kuota }}</p>
            <p class="text-sm text-gray-600 mt-1">
                Tenggat Pendaftaran:
                {{ \Carbon\Carbon::parse($beasiswa->deadline_pendaftaran)->translatedFormat('d M Y') }}
            </p>

            <p class="text-sm text-gray-600 mt-1">
                Persyaratan Upload:
                @if ($beasiswa->require_file)
                    <span class="text-blue-600">{{ $beasiswa->persyaratan_file_name ?? '-' }}</span>
                @else
                    <span class="text-gray-600">Tidak memerlukan upload file</span>
                @endif
            </p>

            <!-- Button Apply -->
            <div class="mt-4">
                <button wire:click="apply({{ $beasiswa->id }})"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-semibold">
                    Apply Beasiswa
                </button>
            </div>
        </div>
    @endforeach

    <!-- Flash Success -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            class="bg-green-100 text-green-800 px-4 py-3 rounded border border-green-300 transition mt-4">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Flash Error -->
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
            class="bg-red-100 text-red-800 px-4 py-3 rounded border border-red-300 transition mt-4">
            ⚠️ {{ session('error') }}
        </div>
    @endif

</div>
