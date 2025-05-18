<x-layouts.app :title="__('Dashboard')">
    @php
        $role = auth()->user()->role;
    @endphp
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        @switch($role)
            @case('admin')
                @include('dashboard.roles.admin', $data ?? [])
            @break

            @case('dosen')
                @include('dashboard.roles.dosen', $data ?? [])
            @break

            @case('mahasiswa')
                @include('dashboard.roles.mahasiswa', $data ?? [])
            @break

            @case('vicedirector')
                @include('dashboard.roles.vicedirector', $data ?? [])
            @break

            @default
                <div class="text-gray-700 text-xl p-6">Dashboard tidak tersedia untuk peran ini.</div>
        @endswitch
    </div>
</x-layouts.app>
