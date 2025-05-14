 <div>
     <div class="p-6 bg-gray-100 min-h-screen">
         <h2 class="text-2xl font-bold text-gray-700 mb-4">Manajemen Mahasiswa</h2>

         {{-- Tombol Tambah --}}
         <div class="flex justify-between items-center mb-4">
             <button wire:click="openModal"
                 class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded">
                 + Data Mahasiswa
             </button>
             <input type="text" wire:model="search" placeholder="🔍 Pencarian"
                 class="border border-gray-300 rounded px-3 py-1.5 text-sm w-64" />
         </div>

         {{-- Tabel --}}
         <table class="min-w-full text-sm text-left text-gray-700 bg-white rounded shadow-md">
             <thead class="bg-gray-200 text-gray-700">
                 <tr>
                     <th class="px-4 py-2">Nama</th>
                     <th class="px-4 py-2">NIM</th>
                     <th class="px-4 py-2">IPK</th>
                     <th class="px-4 py-2">Email</th>
                     <th class="px-4 py-2">Role</th>
                     <th class="px-4 py-2">Program Studi</th>
                     <th class="px-4 py-2">Aksi</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach ($mahasiswa as $data)
                     <tr>
                         <td class="px-4 py-2">{{ $data->nama_mahasiswa }}</td>
                         <td class="px-4 py-2">{{ $data->nim }}</td>
                         <td class="px-4 py-2">{{ $data->ipk }}</td>
                         <td class="px-4 py-2">{{ $data->user->email ?? '-' }}</td>
                         <td class="px-4 py-2">{{ $data->user->role ?? '-' }}</td>
                         <td class="px-4 py-2">{{ $data->program_studi }}</td>
                         <td class="px-4 py-2 space-x-2">
                             <button wire:click="edit({{ $data->id }})"
                                 class="bg-yellow-300 px-2 py-1 rounded">✏️</button>
                             <button wire:click="delete({{ $data->id }})"
                                 class="bg-red-600 text-white px-2 py-1 rounded">🗑️</button>
                         </td>
                     </tr>
                 @endforeach
             </tbody>
         </table>

         {{-- Modal Form --}}
         @if ($showModal)
             <div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                 <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
                     <h3 class="text-xl font-semibold text-gray-800 mb-4">
                         {{ $isEdit ? 'Edit Mahasiswa' : 'Tambah Mahasiswa' }}
                     </h3>

                     <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="space-y-4">
                         <div>
                             <label class="block text-sm text-gray-700">Nama</label>
                             <input type="text " wire:model="nama_mahasiswa"
                                 class="w-full border px-3 py-2 rounded  dark:text-black" />
                         </div>
                         <div>
                             <label class="block text-sm text-gray-700">NIM</label>
                             <input type="text" wire:model="nim"
                                 class="w-full border px-3 py-2 rounded  dark:text-black" />
                         </div>
                         <div>
                             <label class="block text-sm text-gray-700">IPK</label>
                             <input type="number" step="0.01" wire:model="ipk"
                                 class="w-full border px-3 py-2 rounded  dark:text-black" />
                         </div>
                         @if ($isEdit)
                             <div>
                                 <label class="block text-sm text-gray-700">Email</label>
                                 <input type="email" wire:model="email" readonly
                                     class="w-full border px-3 py-2 rounded bg-gray-100  dark:text-black" />
                             </div>
                             <div>
                                 <label class="block text-sm text-gray-700">Role</label>
                                 <input type="text" wire:model="role" readonly
                                     class="w-full border px-3 py-2 rounded bg-gray-100 dark:text-black" />
                             </div>
                         @endif

                         <div>
                             <label class="block text-sm text-gray-700">Program Studi</label>
                             <input type="text" wire:model="program_studi"
                                 class="w-full border px-3 py-2 rounded dark:text-black" />
                         </div>


                         <div class="text-right">
                             <button type="button" wire:click="closeModal"
                                 class="bg-gray-300 px-4 py-2 rounded mr-2">Batal</button>
                             <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                                 Simpan
                             </button>
                         </div>
                     </form>
                 </div>
             </div>
         @endif
     </div>
 </div>
