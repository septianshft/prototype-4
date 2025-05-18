<div>
    @foreach ($beasiswas as $beasiswa)
        <div class="border p-4 rounded mb-2">
            <h3 class="font-bold text-lg">{{ $beasiswa->nama_beasiswa }}</h3>
            <p>{{ $beasiswa->deskripsi }}</p>
            <p>Kuota: {{ $beasiswa->kuota }}</p>

            <button wire:click="apply({{ $beasiswa->id }})" class="bg-blue-600 text-white px-4 py-2 mt-2 rounded">
                Apply
            </button>
        </div>
    @endforeach

    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4 border border-green-300 transition">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
            class="bg-red-100 text-red-800 px-4 py-3 rounded mb-4 border border-red-300 transition">
            ⚠️ {{ session('error') }}
        </div>
    @endif

</div>
