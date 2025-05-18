<div>
    @if($show)
        <div class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white p-8 rounded-xl shadow-2xl w-[32rem] text-center">
                <div class="text-red-600 text-7xl mb-4">🗑️</div>
                <h2 class="text-2xl font-bold mb-3">{{ $title }}</h2>
                <p class="text-gray-700 text-base">{{ $message }}</p>
                <div class="flex justify-center mt-6 gap-4">
                    <button wire:click="confirm"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-md text-sm font-semibold">
                        {{ $confirmText }}
                    </button>
                    <button wire:click="close"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-md text-sm font-semibold">
                        {{ $cancelText }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
