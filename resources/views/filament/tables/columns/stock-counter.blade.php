<div class="flex items-center justify-center space-x-2">
    <button 
        wire:click="decrementStock('{{ $getRecord()->id }}')"
        class="w-8 h-8 rounded-full bg-gray-200 text-gray-800 hover:bg-gray-300 flex items-center justify-center font-bold"
    >
        -
    </button>
    
    <input 
        type="number" 
        wire:change="updateStock('{{ $getRecord()->id }}', $event.target.value)"
        value="{{ $getState() }}"
        class="w-16 text-center border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 bg-white"
    >
    
    <button 
        wire:click="incrementStock('{{ $getRecord()->id }}')"
        class="w-8 h-8 rounded-full bg-gray-200 text-gray-800 hover:bg-gray-300 flex items-center justify-center font-bold"
    >
        +
    </button>
</div>
