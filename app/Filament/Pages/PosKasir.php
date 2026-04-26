<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use UnitEnum;

class PosKasir extends Page
{
    protected static string $view = 'filament.pages.pos-kasir';

    protected static ?string $navigationLabel = 'Kasir POS';
    protected static ?string $title = 'Kasir POS';
    protected static string|UnitEnum|null $navigationGroup = 'Kasir';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 1;

    // Keranjang belanja: [product_id => [name, price, qty, subtotal, image]]
    public array $cart = [];

    // Filter
    public string $search = '';
    public ?int $selectedCategory = null;

    // Metode pembayaran
    public string $paymentMethod = 'cash';

    // Daftar kategori
    public array $categories = [];

    public function mount(): void
    {
        $this->categories = Category::orderBy('name')->get(['id', 'name'])->toArray();
    }

    public function getProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->selectedCategory, fn ($q) => $q->where('category_id', $this->selectedCategory))
            ->where('stock', '>', 0)
            ->with('category')
            ->orderBy('name')
            ->get();
    }

    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product) return;

        $key = (string) $productId;

        if (isset($this->cart[$key])) {
            // Cek stok
            if ($this->cart[$key]['qty'] >= $product->stock) {
                Notification::make()
                    ->title('Stok tidak mencukupi!')
                    ->warning()
                    ->send();
                return;
            }
            $this->cart[$key]['qty']++;
            $this->cart[$key]['subtotal'] = $this->cart[$key]['qty'] * $this->cart[$key]['price'];
        } else {
            $imageUrl = $product->image
                ? \Illuminate\Support\Facades\Storage::disk('public')->url($product->image)
                : null;

            $this->cart[$key] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => (float) $product->price,
                'qty'        => 1,
                'subtotal'   => (float) $product->price,
                'image'      => $imageUrl,
            ];
        }
    }

    public function removeFromCart(string $key): void
    {
        unset($this->cart[$key]);
    }

    public function incrementQty(string $key): void
    {
        if (!isset($this->cart[$key])) return;
        $product = Product::find($this->cart[$key]['product_id']);
        if ($product && $this->cart[$key]['qty'] >= $product->stock) {
            Notification::make()->title('Stok tidak mencukupi!')->warning()->send();
            return;
        }
        $this->cart[$key]['qty']++;
        $this->cart[$key]['subtotal'] = $this->cart[$key]['qty'] * $this->cart[$key]['price'];
    }

    public function decrementQty(string $key): void
    {
        if (!isset($this->cart[$key])) return;
        if ($this->cart[$key]['qty'] <= 1) {
            $this->removeFromCart($key);
            return;
        }
        $this->cart[$key]['qty']--;
        $this->cart[$key]['subtotal'] = $this->cart[$key]['qty'] * $this->cart[$key]['price'];
    }

    public function getTotal(): float
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function clearCart(): void
    {
        $this->cart = [];
    }

    public function processTransaction(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Keranjang masih kosong!')->warning()->send();
            return;
        }

        DB::transaction(function () {
            $order = Order::create([
                'user_id'        => Auth::id(),
                'order_date'     => now()->toDateString(),
                'total_amount'   => $this->getTotal(),
                'payment_method' => $this->paymentMethod,
                'status'         => 'completed',
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal'   => $item['subtotal'],
                ]);

                // Kurangi stok
                Product::where('id', $item['product_id'])->decrement('stock', $item['qty']);
            }
        });

        $this->cart = [];
        $this->paymentMethod = 'cash';

        Notification::make()
            ->title('Transaksi berhasil disimpan!')
            ->success()
            ->send();
    }
}
