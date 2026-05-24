<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">

    <div class="purchase-layout">

        {{-- LEFT COLUMN: Product Catalog & Selector --}}
        <div class="flex flex-col gap-6">
            <div class="purchase-panel">

                {{-- Search & Filters Header --}}
                <div class="purchase-filters">
                    <div class="purchase-search-wrap">
                        <input type="text" wire:model.live.debounce.350ms="search" placeholder="Cari nama produk..."
                            class="purchase-search-input" />
                        <div class="purchase-search-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <select wire:model.live="selectedCategory" class="purchase-select">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                            @endforeach
                        </select>
                        <span class="purchase-badge">
                            {{ $this->getProducts()->count() }} Produk
                        </span>
                    </div>
                </div>

                {{-- Products Table --}}
                <div class="purchase-table-container">
                    <table class="purchase-table">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th style="text-align: center;">Batas Min/Max</th>
                                <th style="text-align: center;">Stok</th>
                                <th style="text-align: center;">Jumlah Beli</th>
                                <th>Harga Beli</th>
                                <th style="text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->getProducts() as $product)
                                @php
                                    $qty = (int) ($quantities[$product->id] ?? 0);
                                    $price = (float) ($prices[$product->id] ?? 0);
                                    $isLow = $product->stock <= $product->low_stock_threshold;
                                @endphp
                                <tr class="{{ $qty > 0 ? 'active-row' : '' }}" wire:key="row-{{ $product->id }}">

                                    {{-- Product Name & Category --}}
                                    <td>
                                        <div class="product-cell">
                                            @if ($product->image)
                                                <img src="{{ Storage::disk('public')->url($product->image) }}"
                                                    alt="{{ $product->name }}" class="product-img" />
                                            @else
                                                <div class="product-placeholder-img">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="product-name-title">{{ $product->name }}</p>
                                                <span
                                                    class="product-category-sub">{{ $product->category->name ?? 'No Category' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Min / Max Thresholds --}}
                                    <td style="text-align: center;">
                                        <span class="threshold-badge">Min: {{ $product->low_stock_threshold }}</span>
                                        <span class="threshold-badge">Max: {{ $product->max_stock_threshold }}</span>
                                    </td>

                                    {{-- Stock --}}
                                    <td style="text-align: center;">
                                        <span
                                            class="stock-status-badge {{ $isLow ? 'stock-status-low' : 'stock-status-safe' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>

                                    {{-- Qty Counter --}}
                                    <td>
                                        <div class="counter-wrapper">
                                            <button type="button" wire:click="decrementQty({{ $product->id }})"
                                                class="counter-btn counter-btn-dec">
                                                −
                                            </button>

                                            <input type="number" wire:model.live="quantities.{{ $product->id }}"
                                                min="0" class="counter-qty-input" />

                                            <button type="button" wire:click="incrementQty({{ $product->id }})"
                                                class="counter-btn counter-btn-inc">
                                                +
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Purchase Price --}}
                                    <td>
                                        <div class="price-input-wrap">
                                            <div class="price-input-prefix">
                                                Rp
                                            </div>
                                            <input type="number" wire:model.live="prices.{{ $product->id }}"
                                                placeholder="{{ number_format($product->purchase_price, 0, '', '') }}"
                                                class="price-input-field" />
                                        </div>
                                    </td>

                                    {{-- Subtotal --}}
                                    <td style="text-align: right;" class="subtotal-cell">
                                        Rp {{ number_format($qty * $price, 0, ',', '.') }}
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 2rem; text-align: center;"
                                        class="product-category-sub">
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
            <div class="purchase-panel flex flex-col gap-4">
                <h3 class="sidebar-panel-header">Informasi Pembelian</h3>

                {{-- Supplier Select --}}
                <div class="form-group">
                    <label class="form-group-label">Pemasok / Supplier <span style="color: #ef4444;">*</span></label>
                    <select wire:model="supplierId" class="purchase-select w-full">
                        <option value="">-- Pilih Pemasok --</option>
                        @foreach ($suppliers as $sup)
                            <option value="{{ $sup['id'] }}">{{ $sup['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Picker --}}
                <div class="form-group">
                    <label class="form-group-label">Tanggal Transaksi</label>
                    <input type="date" wire:model="purchaseDate" class="purchase-date-input" />
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label class="form-group-label">Status Pembelian</label>
                    <select wire:model="status" class="purchase-select w-full">
                        <option value="completed">Completed (Selesai)</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                {{-- Proof Photo Upload --}}
                <div class="form-group">
                    <label class="form-group-label">Foto Bukti Transaksi</label>
                    <div class="upload-container">
                        <input type="file" wire:model="proofImage" class="upload-input-file" />
                        @if ($proofImage)
                            <div style="display: flex; flex-direction: column; align-items: center;">
                                <img src="{{ $proofImage->temporaryUrl() }}"
                                    style="max-height: 80px; object-fit: contain; border-radius: 0.375rem; border: 1px solid #e2e8f0;" />
                                <span
                                    style="font-size: 0.65rem; color: #10b981; font-weight: 700; margin-top: 0.25rem;">Selesai
                                    di-upload</span>
                            </div>
                        @else
                            <div
                                style="display: flex; flex-direction: column; align-items: center; justify-content: center; color: #64748b; padding: 0.5rem 0;">
                                <svg class="w-8 h-8 mb-1 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" style="width: 2rem; height: 2rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span style="font-size: 0.75rem; font-weight: 600;">Pilih atau Seret Foto Bukti</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Checkout Summary Panel --}}
            <div class="purchase-panel flex flex-col gap-4 purchase-summary-card">
                <h3 class="sidebar-panel-header">Ringkasan Belanja</h3>

                {{-- Active items scrollbar --}}
                <div class="summary-items-list">
                    @forelse($this->getActiveItems() as $item)
                        <div class="summary-item-row" wire:key="summary-{{ $item['product_id'] }}">
                            <div>
                                <p class="summary-item-name">{{ $item['name'] }}</p>
                                <span class="summary-item-qtyprice">{{ $item['qty'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                            </div>
                            <span class="summary-item-subtotal">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div style="padding: 1rem 0; text-align: center; color: #94a3b8; font-size: 0.75rem;">
                            Belum ada produk yang dipilih
                        </div>
                    @endforelse
                </div>

                {{-- Grand Total --}}
                <div class="grand-total-box">
                    <span class="grand-total-label">Total Pembelian</span>
                    <span class="grand-total-value">Rp {{ number_format($this->getTotalAmount(), 0, ',', '.') }}</span>
                </div>

                {{-- Action Buttons --}}
                <div class="action-buttons-wrap">
                    <a href="{{ \App\Filament\Resources\Purchases\PurchaseResource::getUrl('index') }}"
                        class="btn-batal">
                        Batal
                    </a>

                    <button type="button" wire:click="savePurchase" wire:loading.attr="disabled"
                        class="btn-simpan">
                        <span wire:loading.remove wire:target="savePurchase">Simpan</span>
                        <span wire:loading wire:target="savePurchase" class="loading-spinner"></span>
                    </button>
                </div>

            </div>

        </div>

    </div>
</x-filament-panels::page>
