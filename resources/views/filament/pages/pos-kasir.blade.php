<x-filament-panels::page>
<style>
    .pos-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 1rem;
        height: calc(100vh - 9rem);
    }
    .product-card:active { transform: scale(0.97); }
    .qty-btn:active { transform: scale(0.9); }
    .cart-item { animation: slideIn 0.15s ease-out; }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* Hide scrollbar visually but keep functionality */
    .scroll-hidden { scrollbar-width: thin; scrollbar-color: #e5e7eb transparent; }
    .scroll-hidden::-webkit-scrollbar { width: 4px; }
    .scroll-hidden::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }
</style>

<div class="pos-layout">

    {{-- ════════════════════════════════════════
         PANEL KIRI — Daftar Produk
    ══════════════════════════════════════════ --}}
    <div class="flex flex-col gap-3 overflow-hidden">

        {{-- Search & Filter Bar --}}
        <div class="flex items-center gap-2">
            {{-- Search --}}
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                </span>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama produk..."
                    class="w-full pl-9 pr-4 py-2.5 text-sm bg-white border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                />
            </div>

            {{-- Kategori --}}
            <select
                wire:model.live="selectedCategory"
                class="py-2.5 pl-3 pr-8 text-sm bg-white border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition appearance-none cursor-pointer"
                style="background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e\"); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.25em 1.25em; padding-right: 2.5rem;"
            >
                <option value="">🗂 Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                @endforeach
            </select>

            {{-- Jumlah produk --}}
            <div class="hidden sm:flex items-center gap-1.5 bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-xs text-gray-500 shadow-sm whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7H4M20 12H4M20 17H4"/>
                </svg>
                <span>{{ $this->getProducts()->count() }} produk</span>
            </div>
        </div>

        {{-- Grid Produk --}}
        <div class="overflow-y-auto flex-1 scroll-hidden -mr-1 pr-1">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                @forelse ($this->getProducts() as $product)
                    <button
                        wire:click="addToCart({{ $product->id }})"
                        wire:key="prod-{{ $product->id }}"
                        class="product-card group relative flex flex-col bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-primary-300 hover:-translate-y-0.5 transition-all duration-150 text-left overflow-hidden cursor-pointer"
                    >
                        {{-- Foto --}}
                        <div class="relative w-full aspect-square bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden">
                            @if ($product->image)
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image) }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                />
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Badge Stok Rendah --}}
                            @if ($product->stock <= $product->low_stock_threshold)
                                <span class="absolute top-1.5 right-1.5 bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">
                                    Sisa {{ $product->stock }}
                                </span>
                            @endif

                            {{-- Overlay + Icon --}}
                            <div class="absolute inset-0 bg-primary-600 opacity-0 group-hover:opacity-10 transition-opacity"></div>
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="bg-white/90 rounded-full p-2 shadow-sm">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="p-2.5">
                            <p class="text-xs font-semibold text-gray-800 leading-snug line-clamp-2 min-h-[2.5rem]">{{ $product->name }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5 truncate">{{ $product->category->name ?? '—' }}</p>
                            <p class="text-sm font-bold text-primary-600 mt-1.5">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </button>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-20 text-gray-300">
                        <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-base font-medium text-gray-400">Produk tidak ditemukan</p>
                        <p class="text-sm text-gray-300 mt-1">Coba ubah filter atau kata kunci</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════
         PANEL KANAN — Keranjang
    ══════════════════════════════════════════ --}}
    <div class="flex flex-col bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Header Keranjang --}}
        <div class="flex items-center justify-between px-4 py-3.5 bg-primary-600 text-white">
            <div class="flex items-center gap-2.5">
                <div class="bg-white/20 rounded-lg p-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-sm leading-tight">Keranjang Belanja</p>
                    <p class="text-white/70 text-xs">{{ count($cart) }} jenis item</p>
                </div>
            </div>
            @if (!empty($cart))
                <button wire:click="clearCart" class="flex items-center gap-1 text-xs bg-white/10 hover:bg-white/20 text-white px-2.5 py-1.5 rounded-lg transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Kosongkan
                </button>
            @endif
        </div>

        {{-- Daftar Item --}}
        <div class="flex-1 overflow-y-auto scroll-hidden">
            @forelse ($cart as $key => $item)
                <div class="cart-item flex items-center gap-3 px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors" wire:key="cart-{{ $key }}">
                    {{-- Foto Mini --}}
                    <div class="w-11 h-11 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100">
                        @if ($item['image'])
                            <img src="{{ $item['image'] }}" class="w-full h-full object-cover"/>
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Nama & Harga --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate leading-snug">{{ $item['name'] }}</p>
                        <p class="text-xs text-gray-400">@ Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        <p class="text-xs font-bold text-primary-600 mt-0.5">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                    </div>

                    {{-- Qty Controls --}}
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button wire:click="decrementQty('{{ $key }}')" class="qty-btn w-7 h-7 rounded-lg bg-gray-100 hover:bg-red-50 hover:text-red-500 text-gray-500 flex items-center justify-center transition-all font-bold text-base leading-none">
                            −
                        </button>
                        <span class="text-sm font-bold w-6 text-center text-gray-800">{{ $item['qty'] }}</span>
                        <button wire:click="incrementQty('{{ $key }}')" class="qty-btn w-7 h-7 rounded-lg bg-gray-100 hover:bg-primary-50 hover:text-primary-600 text-gray-500 flex items-center justify-center transition-all font-bold text-base leading-none">
                            +
                        </button>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-full py-12 text-center px-6">
                    <div class="w-20 h-20 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-400">Keranjang masih kosong</p>
                    <p class="text-xs text-gray-300 mt-1">Klik produk di sebelah kiri untuk menambahkan item</p>
                </div>
            @endforelse
        </div>

        {{-- Footer: Metode Bayar + Total + Tombol --}}
        <div class="border-t border-gray-100 p-4 space-y-3 bg-gray-50/50">

            {{-- Metode Pembayaran --}}
            <div>
                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Metode Pembayaran</label>
                <div class="grid grid-cols-2 gap-1.5">
                    @foreach (['cash' => '💵 Tunai', 'transfer' => '🏦 Transfer', 'qris' => '📱 QRIS', 'debit' => '💳 Debit'] as $val => $label)
                        <button
                            wire:click="$set('paymentMethod', '{{ $val }}')"
                            class="py-2 px-2 rounded-xl text-xs font-semibold border transition-all {{ $paymentMethod === $val ? 'bg-primary-600 text-white border-primary-600 shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-primary-300 hover:text-primary-600' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-100"></div>

            {{-- Total --}}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400">Total Tagihan</p>
                    <p class="text-2xl font-extrabold text-gray-900 leading-tight">
                        Rp {{ number_format($this->getTotal(), 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">{{ count($cart) }} jenis</p>
                    <p class="text-xs text-gray-400">{{ collect($cart)->sum('qty') }} item</p>
                </div>
            </div>

            {{-- Tombol Proses --}}
            <button
                wire:click="processTransaction"
                wire:loading.attr="disabled"
                class="w-full flex items-center justify-center gap-2 py-3.5 rounded-2xl font-bold text-sm transition-all shadow-sm
                    {{ !empty($cart)
                        ? 'bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white shadow-primary-100 hover:shadow-md cursor-pointer'
                        : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                {{ empty($cart) ? 'disabled' : '' }}
            >
                <span wire:loading.remove wire:target="processTransaction">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Proses Transaksi
                </span>
                <span wire:loading wire:target="processTransaction" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Memproses...
                </span>
            </button>
        </div>
    </div>

</div>
</x-filament-panels::page>
