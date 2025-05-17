<div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">{{ $isEdit ? 'Edit Beasiswa' : 'Tambah Beasiswa' }}</h3>

        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="space-y-4">
            <div>
                <label class="block text-sm text-gray-700">Nama Beasiswa</label>
                <input type="text" wire:model="nama_beasiswa" class="w-full border px-3 py-2 rounded  text-black" />
            </div>

            @if ($role === 'admin')
                <div>
                    <label class="block text-sm text-gray-700">Penyelenggara</label>
                    <input type="text" wire:model="nama_penyelenggara"
                        class="w-full border px-3 py-2 rounded  text-black" />
                </div>
            @endif

            <div>
                <label class="block text-sm text-gray-700">Periode</label>
                <input type="date" wire:model="periode"
                    class="w-full border px-3 py-2 rounded  dark:bg-gray-600 dark:text-black" />
                @error('periode')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block text-sm text-gray-700">Kuota</label>
                <input type="number" wire:model="kuota"
                    class="w-full border px-3 py-2 rounded  dark:bg-gray-600 dark:text-black" min="0"
                    max="1000" />
                @error('kuota')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block text-sm text-gray-700">Deskripsi</label>
                <textarea wire:model="deskripsi" rows="3" class="w-full border px-3 py-2 rounded  text-black"></textarea>
            </div>
            <div class="text-right">
                <button type="button" wire:click="closeModal" class="bg-gray-300 px-4 py-2 rounded mr-2">Batal</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
