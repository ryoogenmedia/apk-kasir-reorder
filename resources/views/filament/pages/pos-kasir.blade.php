<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/pos.css') }}">

    <div class="pos-layout">

        {{-- ═══════════════════════════════
             KIRI — Daftar Produk
        ════════════════════════════════ --}}
        <div class="pos-product-panel">

            {{-- Search & Filter --}}
            <div class="pos-searchbar">
                <div class="pos-search-wrap">
                    <svg class="pos-search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama produk..."
                        class="pos-search-input"
                    />
                </div>
                <select wire:model.live="selectedCategory" class="pos-category-select">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                    @endforeach
                </select>
                <div class="pos-counter-badge">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    {{ $this->getProducts()->count() }} produk
                </div>
            </div>

            {{-- Grid Produk --}}
            <div class="pos-product-grid">
                <div class="pos-grid">
                    @forelse ($this->getProducts() as $product)
                        <button
                            wire:click="addToCart({{ $product->id }})"
                            wire:key="prod-{{ $product->id }}"
                            class="pos-card"
                        >
                            <div class="pos-card-img">
                                @if ($product->image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image) }}" alt="{{ $product->name }}"/>
                                @else
                                    <div class="pos-card-placeholder">
                                        <svg width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif

                                @if ($product->stock <= $product->low_stock_threshold)
                                    <span class="pos-stock-badge">Sisa {{ $product->stock }}</span>
                                @endif

                                <div class="pos-card-overlay">
                                    <div class="pos-card-add-icon">
                                        <svg width="18" height="18" fill="none" stroke="#2563eb" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="pos-card-info">
                                <p class="pos-card-name">{{ $product->name }}</p>
                                <p class="pos-card-category">{{ $product->category->name ?? '—' }}</p>
                                <p class="pos-card-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                        </button>
                    @empty
                        <div class="pos-empty">
                            <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>Produk tidak ditemukan</p>
                            <span>Coba ubah filter atau kata kunci pencarian</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ═══════════════════════════════
             KANAN — Keranjang Belanja
        ════════════════════════════════ --}}
        <div class="pos-cart-panel">

            {{-- Header --}}
            <div class="pos-cart-header">
                <div class="pos-cart-header-left">
                    <div class="pos-cart-header-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="pos-cart-title">Keranjang Belanja</div>
                        <div class="pos-cart-subtitle">{{ count($cart) }} jenis · {{ collect($cart)->sum('qty') }} item</div>
                    </div>
                </div>
                @if (!empty($cart))
                    <button wire:click="clearCart" class="pos-clear-btn">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Kosongkan
                    </button>
                @endif
            </div>

            {{-- Daftar Item --}}
            <div class="pos-cart-items">
                @forelse ($cart as $key => $item)
                    <div class="pos-cart-item" wire:key="cart-{{ $key }}">
                        <div class="pos-cart-thumb">
                            @if ($item['image'])
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"/>
                            @else
                                <svg width="18" height="18" fill="none" stroke="#d1d5db" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="pos-cart-item-info">
                            <div class="pos-cart-item-name">{{ $item['name'] }}</div>
                            <div class="pos-cart-item-unit">@ Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                            <div class="pos-cart-item-sub">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
                        </div>
                        <div class="pos-qty-controls">
                            <button wire:click="decrementQty('{{ $key }}')" class="pos-qty-btn minus">−</button>
                            <span class="pos-qty-num">{{ $item['qty'] }}</span>
                            <button wire:click="incrementQty('{{ $key }}')" class="pos-qty-btn plus">+</button>
                        </div>
                    </div>
                @empty
                    <div class="pos-cart-empty">
                        <div class="pos-cart-empty-icon">
                            <svg width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <p>Keranjang masih kosong</p>
                        <span>Klik produk di sebelah kiri<br>untuk menambahkan item</span>
                    </div>
                @endforelse
            </div>

            {{-- Footer --}}
            <div class="pos-cart-footer">

                {{-- Metode Pembayaran --}}
                <div>
                    <span class="pos-pay-label">Metode Pembayaran</span>
                    <div class="pos-pay-methods" style="grid-template-columns: 1fr 1fr;">
                        @foreach (['cash' => '💵 Tunai', 'qris' => '📱 QRIS'] as $val => $label)
                            <button
                                type="button"
                                wire:click="$set('paymentMethod', '{{ $val }}')"
                                class="pos-pay-btn {{ $paymentMethod === $val ? 'active' : '' }}"
                            >{{ $label }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Cash Payment Inputs --}}
                @if ($paymentMethod === 'cash')
                    <div class="pos-cash-container">
                        <div class="pos-input-group">
                            <span class="pos-pay-label" style="margin-bottom:0">Uang Dibayar (Rp)</span>
                            <input
                                type="number"
                                wire:model.live="amountPaid"
                                placeholder="Masukkan nominal..."
                                class="pos-cash-input-field"
                            />
                        </div>
                        <div class="pos-denominations-grid">
                            <button type="button" wire:click="selectExactAmount" class="pos-denom-btn" style="grid-column: span 2; background: #dbeafe; color: #1e40af; border-color: #bfdbfe;">Uang Pas</button>
                            <button type="button" wire:click="selectDenomination(1000)" class="pos-denom-btn">1K</button>
                            <button type="button" wire:click="selectDenomination(2000)" class="pos-denom-btn">2K</button>
                            <button type="button" wire:click="selectDenomination(5000)" class="pos-denom-btn">5K</button>
                            <button type="button" wire:click="selectDenomination(10000)" class="pos-denom-btn">10K</button>
                            <button type="button" wire:click="selectDenomination(20000)" class="pos-denom-btn">20K</button>
                            <button type="button" wire:click="selectDenomination(50000)" class="pos-denom-btn">50K</button>
                            <button type="button" wire:click="selectDenomination(100000)" class="pos-denom-btn">100K</button>
                        </div>
                        <div class="pos-change-display">
                            <span>Kembalian:</span>
                            <span>Rp {{ number_format($changeAmount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif

                {{-- QRIS Payment Inputs --}}
                @if ($paymentMethod === 'qris')
                    <div class="pos-qris-container">
                        <span class="pos-pay-label" style="margin-bottom:0">Bukti Pembayaran QRIS (Foto, Opsional)</span>
                        <input
                            type="file"
                            wire:model="qrisProofFile"
                            class="pos-file-input"
                            accept="image/*"
                        />
                        <div wire:loading wire:target="qrisProofFile" class="text-xs text-gray-500 mt-1" style="color: #6b7280; font-size: 0.7rem;">Mengunggah...</div>
                        @if ($qrisProofFile)
                            <div class="text-xs text-green-600 mt-1" style="color: #10b981; font-size: 0.7rem;">✓ File siap diunggah</div>
                        @endif
                    </div>
                @endif

                <hr class="pos-divider">

                {{-- Total --}}
                <div class="pos-total-row">
                    <div>
                        <div class="pos-total-label">Total Tagihan</div>
                        <div class="pos-total-amount">Rp {{ number_format($this->getTotal(), 0, ',', '.') }}</div>
                    </div>
                    <div class="pos-total-meta">
                        {{ count($cart) }} jenis<br>
                        {{ collect($cart)->sum('qty') }} item
                    </div>
                </div>

                {{-- Tombol Proses --}}
                <button
                    wire:click="processTransaction"
                    wire:loading.attr="disabled"
                    class="pos-process-btn {{ !empty($cart) ? 'active' : 'disabled' }}"
                    {{ empty($cart) ? 'disabled' : '' }}
                >
                    <span wire:loading.remove wire:target="processTransaction">
                        <svg style="display:inline;margin-right:6px;vertical-align:-3px" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Proses Transaksi
                    </span>
                    <span wire:loading wire:target="processTransaction" style="display:flex;align-items:center;gap:8px;">
                        <span class="pos-spinner"></span>
                        Memproses...
                    </span>
                </button>

            </div>
        </div>

    </div>

    {{-- Success Popup Modal --}}
    @if ($showSuccessModal && $lastOrder)
        <div class="pos-modal-overlay">
            <div class="pos-modal-content">
                <div class="pos-modal-header">
                    <svg style="display:inline-block;margin-bottom:4px" width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="pos-modal-title">Transaksi Berhasil!</div>
                </div>
                <div class="pos-modal-body">
                    <div class="pos-receipt-summary">
                        <div class="pos-receipt-title">STRUK PENJUALAN</div>
                        <div class="pos-receipt-row">
                            <span>No. Transaksi:</span>
                            <span>#{{ $lastOrder['id'] }}</span>
                        </div>
                        <div class="pos-receipt-row">
                            <span>Tanggal:</span>
                            <span>{{ $lastOrder['order_date'] }}</span>
                        </div>
                        <div class="pos-receipt-row">
                            <span>Kasir:</span>
                            <span>{{ $lastOrder['cashier_name'] }}</span>
                        </div>
                        <div class="pos-receipt-row">
                            <span>Metode Bayar:</span>
                            <span>{{ $lastOrder['payment_method'] }}</span>
                        </div>
                        <div class="pos-receipt-divider"></div>
                        @foreach ($lastOrder['items'] as $item)
                            <div class="pos-receipt-row">
                                <span>{{ $item['name'] }} (x{{ $item['qty'] }})</span>
                                <span>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="pos-receipt-divider"></div>
                        <div class="pos-receipt-row" style="font-weight: 700;">
                            <span>TOTAL:</span>
                            <span>Rp {{ number_format($lastOrder['total_amount'], 0, ',', '.') }}</span>
                        </div>
                        <div class="pos-receipt-row">
                            <span>Bayar:</span>
                            <span>Rp {{ number_format($lastOrder['amount_paid'], 0, ',', '.') }}</span>
                        </div>
                        <div class="pos-receipt-row" style="color: #10b981; font-weight: 700;">
                            <span>Kembalian:</span>
                            <span>Rp {{ number_format($lastOrder['change_amount'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="pos-modal-footer">
                    <button
                        type="button"
                        onclick="window.open('/admin/orders/receipt/{{ $lastOrder['id'] }}', '_blank')"
                        class="pos-modal-btn print"
                    >
                        Cetak Struk
                    </button>
                    <button
                        type="button"
                        wire:click="resetCashier"
                        class="pos-modal-btn new-trans"
                    >
                        Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
