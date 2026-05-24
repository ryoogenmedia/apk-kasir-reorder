<div
    x-data="{ 
        isVisible: false,
        timeout: null,
        init() {
            document.addEventListener('livewire:init', () => {
                Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                    // Beri jeda kecil agar loading tidak berkedip untuk request yang sangat cepat
                    this.timeout = setTimeout(() => {
                        this.isVisible = true;
                    }, 200);

                    succeed(({ snapshot, effect }) => {
                        clearTimeout(this.timeout);
                        this.isVisible = false;
                    })
                    
                    fail(() => {
                        clearTimeout(this.timeout);
                        this.isVisible = false;
                    })
                })
            })
        }
    }"
    x-show="isVisible"
    style="display: none;"
    class="fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-2.5 bg-gray-900/95 dark:bg-white/95 text-white dark:text-gray-900 rounded-full shadow-2xl backdrop-blur-md border border-white/10 dark:border-gray-900/10 transition-all duration-300"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-8 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-8 scale-95"
>
    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <span class="text-sm font-semibold tracking-wide">Memuat...</span>
</div>
