<div class="p-6 bg-white min-h-screen">
    <h2 class="text-2xl font-bold text-gray-700 mb-6">Laporan Beasiswa</h2>


    @if (auth()->user()->role === 'mahasiswa')
        <livewire:beasiswa.partials.laporan-beasiswa-form />
    @endif

    <livewire:beasiswa.partials.laporan-beasiswa-table />
    

</div>
