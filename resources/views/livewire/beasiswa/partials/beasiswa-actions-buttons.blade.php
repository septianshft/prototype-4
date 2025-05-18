@if($role == 'admin' || $role == 'dosen')
    <button wire:click="edit({{ $data->id }})"
        class="bg-blue-600 hover:bg-blue-800 px-2 py-1 rounded">Edit</button>
    <button wire:click="delete({{ $data->id }})"
        class="bg-red-600 text-white px-2 py-1 rounded">🗑️</button>

@elseif($role == 'mahasiswa')
    <button wire:click="applyBeasiswa({{ $data->id }})"
        class="bg-green-600 text-white px-2 py-1 rounded">
        Apply
    </button>

@elseif($role == 'vice_director')
    <button wire:click="showDetail({{ $data->id }})"
        class="bg-blue-600 text-white px-2 py-1 rounded">
        Detail
    </button>
@endif
