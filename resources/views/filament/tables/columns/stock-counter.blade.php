<div class="flex items-center justify-center gap-3">
    <button 
        wire:click="decrementStock('{{ $getRecord()->id }}')"
        class="w-9 h-9 rounded-lg bg-red-500 hover:bg-red-600 text-white flex items-center justify-center transition-colors duration-150 shadow-sm"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
        </svg>
    </button>
    
    <input 
        type="number" 
        wire:change="updateStock('{{ $getRecord()->id }}', $event.target.value)"
        value="{{ $getState() }}"
        min="0"
        class="w-24 h-9 text-center border border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 text-sm font-medium text-gray-900 bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
    >
    
    <button 
        wire:click="incrementStock('{{ $getRecord()->id }}')"
        class="w-9 h-9 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center transition-colors duration-150 shadow-sm"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
    </button>
</div>
