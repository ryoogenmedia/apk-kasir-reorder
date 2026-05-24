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
use Livewire\WithFileUploads;

class PosKasir extends Page
{
    use WithFileUploads;

    protected string $view = 'filament.pages.pos-kasir';

    protected static ?string $navigationLabel = 'Kasir POS';
    protected static ?string $title = 'Kasir POS';
    protected static string|UnitEnum|null $navigationGroup = 'Kasir';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'kasir']);
    }

    // Keranjang belanja: [product_id => [name, price, qty, subtotal, image]]
    public array $cart = [];

    // Filter
    public string $search = '';
    public ?int $selectedCategory = null;

    // Metode pembayaran
    public string $paymentMethod = 'cash';

    // Pembayaran Cash
    public ?float $amountPaid = null;
    public ?float $changeAmount = 0;

    // Hitungan berapa kali setiap pecahan ditekan
    public array $denominationCounts = [];

    // Upload Bukti QRIS (opsional)
    public $qrisProofFile;

    // State Popup Transaksi
    public bool $showSuccessModal = false;
    public array $lastOrder = [];

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
                    ->title('Stok Tidak Mencukupi')
                    ->body("Stok \"{$product->name}\" yang tersedia saat ini tidak mencukupi untuk ditambahkan ke keranjang.")
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
            Notification::make()
                ->title('Stok Tidak Mencukupi')
                ->body("Stok \"{$product->name}\" yang tersedia saat ini tidak mencukupi untuk ditambah lagi.")
                ->warning()
                ->send();
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

    public function updatedAmountPaid($value): void
    {
        $this->calculateChange();
    }

    public function calculateChange(): void
    {
        $total = $this->getTotal();
        if ($this->amountPaid) {
            $this->changeAmount = max(0, (float) $this->amountPaid - $total);
        } else {
            $this->changeAmount = 0;
        }
    }

    public function selectDenomination(float $amount): void
    {
        // Legacy - tetap bisa dipakai (set langsung)
        $this->amountPaid = $amount;
        $this->calculateChange();
    }

    public function addDenomination(float $amount): void
    {
        // Akumulasi: tambahkan nilai ke amountPaid
        $this->amountPaid = ((float) ($this->amountPaid ?? 0)) + $amount;

        // Hitung berapa kali pecahan ini ditekan
        $key = (string)(int)$amount;
        $this->denominationCounts[$key] = ($this->denominationCounts[$key] ?? 0) + 1;

        $this->calculateChange();
    }

    public function resetDenominations(): void
    {
        $this->amountPaid = null;
        $this->changeAmount = 0;
        $this->denominationCounts = [];
    }

    public function selectExactAmount(): void
    {
        $this->amountPaid = $this->getTotal();
        $this->denominationCounts = [];
        $this->calculateChange();
    }

    public function resetCashier(): void
    {
        $this->showSuccessModal = false;
        $this->cart = [];
        $this->amountPaid = null;
        $this->changeAmount = 0;
        $this->denominationCounts = [];
        $this->qrisProofFile = null;
        $this->paymentMethod = 'cash';
        $this->lastOrder = [];
    }

    public function processTransaction(): void
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('Keranjang Belanja Kosong')
                ->body('Silakan pilih dan masukkan produk ke keranjang terlebih dahulu sebelum memproses transaksi.')
                ->warning()
                ->send();
            return;
        }

        $total = $this->getTotal();

        if ($this->paymentMethod === 'cash') {
            if (is_null($this->amountPaid) || $this->amountPaid < $total) {
                Notification::make()
                    ->title('Uang Pembayaran Kurang')
                    ->body('Jumlah uang yang dibayarkan kurang dari total belanja transaksi ini.')
                    ->danger()
                    ->send();
                return;
            }
        }

        $qrisPath = null;
        if ($this->paymentMethod === 'qris' && $this->qrisProofFile) {
            $qrisPath = $this->qrisProofFile->store('qris-proofs', 'public');
        }

        $orderData = [];

        DB::transaction(function () use ($total, $qrisPath, &$orderData) {
            $order = Order::create([
                'user_id'        => Auth::id(),
                'order_date'     => now()->toDateString(),
                'total_amount'   => $total,
                'payment_method' => $this->paymentMethod,
                'status'         => 'completed',
                'qris_proof'     => $this->paymentMethod === 'qris' ? $qrisPath : null,
                'amount_paid'    => $this->paymentMethod === 'cash' ? (float) $this->amountPaid : $total,
                'change_amount'  => $this->paymentMethod === 'cash' ? (float) $this->changeAmount : 0,
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

            $orderData = [
                'id' => $order->id,
                'order_date' => $order->order_date->format('d/m/Y'),
                'total_amount' => $order->total_amount,
                'amount_paid' => $order->amount_paid,
                'change_amount' => $order->change_amount,
                'payment_method' => $order->payment_method === 'cash' ? 'Tunai' : 'QRIS',
                'cashier_name' => Auth::user()->name,
                'items' => collect($this->cart)->map(fn($item) => [
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ])->toArray(),
            ];
        });

        $this->lastOrder = $orderData;
        $this->showSuccessModal = true;

        Notification::make()
            ->title('Transaksi Berhasil Disimpan')
            ->body('Data transaksi penjualan produk berhasil disimpan dan stok produk telah terpotong.')
            ->success()
            ->send();
    }
}
