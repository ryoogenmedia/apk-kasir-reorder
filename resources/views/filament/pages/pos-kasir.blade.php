<x-filament-panels::page>
    <div class="flex gap-4 h-[calc(100vh-10rem)]">

        {{-- KIRI: Daftar Produk --}}
        <div class="flex-1 flex flex-col gap-3 overflow-hidden">

            {{-- Search & Filter Kategori --}}
            <div class="flex gap-2">
                <div class="flex-1">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari produk..."
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                    />
                </div>
                <select
                    wire:model.live="selectedCategory"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Grid Produk --}}
            <div class="overflow-y-auto flex-1 pr-1">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    @forelse ($this->getProducts() as $product)
                        <button
                            wire:click="addToCart({{ $product->id }})"
                            wire:key="prod-{{ $product->id }}"
                            class="group relative flex flex-col rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md hover:border-primary-400 transition-all duration-150 text-left overflow-hidden"
                        >
                            {{-- Gambar Produk --}}
                            <div class="w-full h-28 bg-gray-100 overflow-hidden">
                                @if ($product->image)
                                    <img
                                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image) }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                    />
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Info Produk --}}
                            <div class="p-2 flex flex-col gap-0.5">
                                <p class="text-xs font-semibold text-gray-800 leading-snug line-clamp-2">{{ $product->name }}</p>
                                <p class="text-xs text-gray-400">{{ $product->category->name ?? '-' }}</p>
                                <p class="text-sm font-bold text-primary-600 mt-0.5">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-400">Stok: {{ $product->stock }}</p>
                            </div>

                            {{-- Overlay klik --}}
                            <div class="absolute inset-0 bg-primary-500 opacity-0 group-active:opacity-10 transition-opacity"></div>
                        </button>
                    @empty
                        <div class="col-span-full text-center text-gray-400 py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm">Tidak ada produk ditemukan</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- KANAN: Keranjang --}}
        <div class="w-80 flex-shrink-0 flex flex-col rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">

            {{-- Header Keranjang --}}
            <div class="bg-primary-600 text-white px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="font-semibold text-sm">Keranjang Belanja</span>
                </div>
                @if (!empty($cart))
                    <button wire:click="clearCart" class="text-xs text-white/70 hover:text-white underline">Kosongkan</button>
                @endif
            </div>

            {{-- Item Keranjang --}}
            <div class="flex-1 overflow-y-auto divide-y divide-gray-100">
                @forelse ($cart as $key => $item)
                    <div class="flex items-center gap-2 px-3 py-2" wire:key="cart-{{ $key }}">
                        {{-- Gambar kecil --}}
                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                            @if ($item['image'])
                                <img src="{{ $item['image'] }}" class="w-full h-full object-cover" />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300 text-xs">—</div>
                            @endif
                        </div>

                        {{-- Detail --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800 truncate">{{ $item['name'] }}</p>
                            <p class="text-xs text-gray-500">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>

                        {{-- Qty Controls --}}
                        <div class="flex items-center gap-1">
                            <button wire:click="decrementQty('{{ $key }}')" class="w-6 h-6 rounded-full bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 flex items-center justify-center text-sm font-bold transition-colors">−</button>
                            <span class="text-sm font-semibold w-5 text-center">{{ $item['qty'] }}</span>
                            <button wire:click="incrementQty('{{ $key }}')" class="w-6 h-6 rounded-full bg-gray-100 hover:bg-primary-100 text-gray-600 hover:text-primary-600 flex items-center justify-center text-sm font-bold transition-colors">+</button>
                        </div>

                        {{-- Subtotal --}}
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-bold text-gray-800">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <p class="text-sm text-gray-400">Keranjang kosong</p>
                        <p class="text-xs text-gray-300">Klik produk untuk menambahkan</p>
                    </div>
                @endforelse
            </div>

            {{-- Footer: Total & Proses --}}
            <div class="border-t border-gray-200 p-4 space-y-3">

                {{-- Metode Pembayaran --}}
                <div>
                    <label class="text-xs text-gray-500 font-medium block mb-1">Metode Pembayaran</label>
                    <select wire:model.live="paymentMethod" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="cash">Tunai</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                        <option value="debit">Kartu Debit</option>
                    </select>
                </div>

                {{-- Total --}}
                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                    <span class="text-sm font-medium text-gray-600">Total</span>
                    <span class="text-lg font-bold text-primary-600">
                        Rp {{ number_format($this->getTotal(), 0, ',', '.') }}
                    </span>
                </div>

                {{-- Tombol Proses --}}
                <button
                    wire:click="processTransaction"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                    class="w-full rounded-xl bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white font-semibold py-3 text-sm transition-colors shadow-sm disabled:opacity-50"
                >
                    <span wire:loading.remove>🧾 Proses Transaksi</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>
        </div>

    </div>
</x-filament-panels::page>
