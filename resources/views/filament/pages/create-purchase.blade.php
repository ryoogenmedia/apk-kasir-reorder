<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN: Product Catalog & Selector --}}
        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm">
                
                {{-- Search & Filters Header --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center mb-6">
                    <div class="relative w-full sm:max-w-xs">
                        <input 
                            type="text" 
                            wire:model.live.debounce.350ms="search" 
                            placeholder="Cari nama produk..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-primary-500 focus:border-primary-500 focus:outline-none"
                        />
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg class="w-5 height-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="w-full sm:w-auto flex items-center gap-2">
                        <select 
                            wire:model.live="selectedCategory" 
                            class="w-full sm:w-48 py-2 px-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-primary-500 focus:border-primary-500 focus:outline-none"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                            @endforeach
                        </select>
                        <span class="text-sm font-semibold text-gray-500 dark:text-gray-400 whitespace-nowrap bg-gray-100 dark:bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700">
                            {{ $this->getProducts()->count() }} Produk
                        </span>
                    </div>
                </div>

                {{-- Products Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-800 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-950">
                                <th class="py-3 px-4">Nama Produk</th>
                                <th class="py-3 px-4 text-center">Batas Min/Max</th>
                                <th class="py-3 px-4 text-center">Stok Sekarang</th>
                                <th class="py-3 px-4 text-center">Jumlah Beli</th>
                                <th class="py-3 px-4">Harga Beli Satuan</th>
                                <th class="py-3 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($this->getProducts() as $product)
                                @php
                                    $qty = (int) ($quantities[$product->id] ?? 0);
                                    $price = (float) ($prices[$product->id] ?? 0);
                                    $isLow = $product->stock <= $product->low_stock_threshold;
                                @endphp
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors {{ $qty > 0 ? 'bg-primary-50/10 dark:bg-primary-950/5' : '' }}">
                                    
                                    {{-- Product Name & Category --}}
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            @if($product->image)
                                                <img src="{{ Storage::disk('public')->url($product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-700" />
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-850 flex items-center justify-center text-gray-400 dark:text-gray-600 border border-gray-200 dark:border-gray-800">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-bold text-gray-900 dark:text-white leading-tight">{{ $product->name }}</p>
                                                <span class="text-xs text-gray-500 dark:text-gray-450">{{ $product->category->name ?? 'No Category' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Min / Max Thresholds --}}
                                    <td class="py-4 px-4 text-center text-sm text-gray-500 dark:text-gray-400 font-medium">
                                        <span class="bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded text-xs">Min: {{ $product->low_stock_threshold }}</span>
                                        <span class="bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded text-xs ml-1">Max: {{ $product->max_stock_threshold }}</span>
                                    </td>

                                    {{-- Stock --}}
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $isLow ? 'bg-danger-100 text-danger-700 dark:bg-danger-950/30 dark:text-danger-400' : 'bg-success-100 text-success-700 dark:bg-success-950/30 dark:text-success-400' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>

                                    {{-- Qty Counter --}}
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button 
                                                type="button" 
                                                wire:click="decrementQty({{ $product->id }})" 
                                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-danger-500 hover:text-white transition-colors text-gray-600 dark:text-gray-400 font-bold"
                                            >
                                                −
                                            </button>
                                            
                                            <input 
                                                type="number" 
                                                wire:model.live="quantities.{{ $product->id }}" 
                                                min="0" 
                                                class="w-14 text-center py-1 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-white text-sm font-bold focus:outline-none focus:ring-1 focus:ring-primary-500"
                                            />

                                            <button 
                                                type="button" 
                                                wire:click="incrementQty({{ $product->id }})" 
                                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-success-500 hover:text-white transition-colors text-gray-600 dark:text-gray-400 font-bold"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Purchase Price --}}
                                    <td class="py-4 px-4">
                                        <div class="relative w-36">
                                            <div class="absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs font-bold">
                                                Rp
                                            </div>
                                            <input 
                                                type="number" 
                                                wire:model.live="prices.{{ $product->id }}" 
                                                placeholder="{{ number_format($product->purchase_price, 0, '', '') }}" 
                                                class="w-full pl-8 pr-2 py-1 text-sm border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-primary-500"
                                            />
                                        </div>
                                    </td>

                                    {{-- Subtotal --}}
                                    <td class="py-4 px-4 text-right font-extrabold text-sm text-gray-900 dark:text-white">
                                        Rp {{ number_format($qty * $price, 0, ',', '.') }}
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada produk yang cocok dengan pencarian / kategori.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        {{-- RIGHT COLUMN: Supplier Info & Order Checkout Summary --}}
        <div class="flex flex-col gap-6">
            
            {{-- Supplier Form --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm flex flex-col gap-4">
                <h3 class="font-bold text-gray-900 dark:text-white text-lg border-b border-gray-100 dark:border-gray-850 pb-2">Informasi Pembelian</h3>
                
                {{-- Supplier Select --}}
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Pemasok / Supplier <span class="text-danger-500">*</span></label>
                    <select 
                        wire:model="supplierId" 
                        class="w-full py-2 px-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-1 focus:ring-primary-500 focus:outline-none"
                    >
                        <option value="">-- Pilih Pemasok --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup['id'] }}">{{ $sup['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Picker --}}
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Tanggal Transaksi</label>
                    <input 
                        type="date" 
                        wire:model="purchaseDate" 
                        class="w-full py-2 px-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-1 focus:ring-primary-500 focus:outline-none"
                    />
                </div>

                {{-- Status --}}
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Status Pembelian</label>
                    <select 
                        wire:model="status" 
                        class="w-full py-2 px-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-white focus:ring-1 focus:ring-primary-500 focus:outline-none"
                    >
                        <option value="completed">Completed (Selesai)</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                {{-- Proof Photo Upload --}}
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Foto Bukti Transaksi</label>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-3 text-center relative hover:border-primary-500 transition-colors">
                        <input 
                            type="file" 
                            wire:model="proofImage" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        />
                        @if($proofImage)
                            <div class="flex flex-col items-center">
                                <img src="{{ $proofImage->temporaryUrl() }}" class="max-h-24 object-contain rounded border border-gray-200 dark:border-gray-700" />
                                <span class="text-[10px] text-success-500 font-bold mt-1">Selesai di-upload</span>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 py-2">
                                <svg class="w-8 h-8 mb-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-xs font-semibold">Pilih atau Seret Foto Bukti</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Checkout Checkout Panel --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm flex flex-col gap-4">
                <h3 class="font-bold text-gray-900 dark:text-white text-lg border-b border-gray-100 dark:border-gray-850 pb-2">Ringkasan Belanja</h3>
                
                {{-- Active items scrollbar --}}
                <div class="max-h-48 overflow-y-auto flex flex-col gap-2.5">
                    @forelse($this->getActiveItems() as $item)
                        <div class="flex justify-between items-center text-sm border-b border-gray-100 dark:border-gray-850 pb-2">
                            <div>
                                <p class="font-bold text-gray-800 dark:text-gray-200">{{ $item['name'] }}</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $item['qty'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                            </div>
                            <span class="font-extrabold text-gray-900 dark:text-white whitespace-nowrap">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="py-4 text-center text-gray-400 dark:text-gray-500 text-xs">
                            Belum ada produk yang dipilih
                        </div>
                    @endforelse
                </div>

                {{-- Grand Total --}}
                <div class="bg-gray-50 dark:bg-gray-950 p-4 rounded-xl border border-gray-100 dark:border-gray-850 flex justify-between items-center">
                    <span class="text-sm font-bold uppercase text-gray-500 dark:text-gray-400">Total Pembelian</span>
                    <span class="text-xl font-black text-primary-650 dark:text-primary-400">Rp {{ number_format($this->getTotalAmount(), 0, ',', '.') }}</span>
                </div>

                {{-- Action Buttons --}}
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <a 
                        href="{{ PurchaseResource::getUrl('index') }}" 
                        class="w-full text-center py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-850 font-bold transition-colors"
                    >
                        Batal
                    </a>
                    
                    <button 
                        type="button" 
                        wire:click="savePurchase" 
                        wire:loading.attr="disabled"
                        class="w-full py-2.5 bg-primary-600 hover:bg-primary-750 text-white rounded-lg text-sm font-extrabold shadow-md shadow-primary-500/20 transition-all flex items-center justify-center gap-1.5"
                    >
                        <span wire:loading.remove wire:target="savePurchase">Simpan</span>
                        <span wire:loading wire:target="savePurchase" class="inline-block animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
                    </button>
                </div>

            </div>

        </div>

    </div>
</x-filament-panels::page>
