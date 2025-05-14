<div class="p-6 bg-gray-100 min-h-screen">
    <h2 class="text-2xl font-bold text-gray-700 mb-4">Laporan Beasiswa</h2>

    @if (auth()->user()->role === 'mahasiswa')
        <livewire:beasiswa.partials.laporan-beasiswa-form />
    @endif

    <livewire:beasiswa.partials.laporan-beasiswa-table />
</div>
