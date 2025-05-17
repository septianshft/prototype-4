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
        <div class="text-green-600 mt-2">{{ session('success') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="text-red-600 mt-2">{{ session('error') }}</div>
    @endif
</div>
