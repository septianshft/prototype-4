    @props(['title', 'icon', 'value'])

    <div class="rounded-lg bg-white p-4 shadow-md">
        <div class="flex items-center space-x-4">
            <div class="text-xl text-gray-600">
                <i class="fas {{ $icon }}"></i>
            </div>
            <div>
                <h4 class="text-lg font-semibold">{{ $title }}</h4>
                <div class="text-4xl font-bold">{{ $value }}</div>
            </div>
        </div>
    </div>
