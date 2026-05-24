<?php

namespace App\Filament\Resources\Purchases\Pages;

use App\Filament\Resources\Purchases\PurchaseResource;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Category;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreatePurchase extends Page
{
    use WithFileUploads;

    protected static string $resource = PurchaseResource::class;
    protected string $view = 'filament.pages.create-purchase';

    // Form inputs
    public ?int $supplierId = null;
    public string $purchaseDate = '';
    public string $status = 'completed';
    public $proofImage;

    // Cart items
    public array $quantities = []; // [product_id => quantity]
    public array $prices = []; // [product_id => price]

    // Search and filters
    public string $search = '';
    public ?int $selectedCategory = null;

    // Lists for rendering select options
    public array $suppliers = [];
    public array $categories = [];

    public function mount(): void
    {
        $this->purchaseDate = now()->format('Y-m-d');
        $this->suppliers = Supplier::orderBy('name')->get(['id', 'name'])->toArray();
        $this->categories = Category::orderBy('name')->get(['id', 'name'])->toArray();
    }

    public function getProducts()
    {
        return Product::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->selectedCategory, fn ($q) => $q->where('category_id', $this->selectedCategory))
            ->with('category')
            ->orderBy('name')
            ->get();
    }

    public function updatedQuantities($value, $key): void
    {
        $productId = (int) $key;
        $qty = (int) $value;
        if ($qty > 0 && (!isset($this->prices[$productId]) || !$this->prices[$productId])) {
            $product = Product::find($productId);
            if ($product) {
                $this->prices[$productId] = (float) $product->purchase_price;
            }
        }
    }

    public function incrementQty(int $productId): void
    {
        $qty = (int) ($this->quantities[$productId] ?? 0);
        $this->quantities[$productId] = $qty + 1;
        $this->updatedQuantities($this->quantities[$productId], $productId);
    }

    public function decrementQty(int $productId): void
    {
        $qty = (int) ($this->quantities[$productId] ?? 0);
        if ($qty > 0) {
            $this->quantities[$productId] = $qty - 1;
            $this->updatedQuantities($this->quantities[$productId], $productId);
        }
    }

    public function getActiveItems(): array
    {
        $items = [];
        foreach ($this->quantities as $productId => $qty) {
            $qty = (int) $qty;
            if ($qty > 0) {
                $product = Product::find($productId);
                if ($product) {
                    $price = (float) ($this->prices[$productId] ?? 0);
                    $items[] = [
                        'product_id' => $productId,
                        'name' => $product->name,
                        'qty' => $qty,
                        'price' => $price,
                        'subtotal' => $qty * $price,
                    ];
                }
            }
        }
        return $items;
    }

    public function getTotalAmount(): float
    {
        $total = 0;
        foreach ($this->getActiveItems() as $item) {
            $total += $item['subtotal'];
        }
        return $total;
    }

    public function savePurchase(): void
    {
        $activeItems = $this->getActiveItems();

        if (!$this->supplierId) {
            Notification::make()
                ->title('Pemasok Wajib Dipilih')
                ->body('Silakan pilih pemasok terlebih dahulu untuk menyimpan data pembelian.')
                ->danger()
                ->send();
            return;
        }

        if (empty($activeItems)) {
            Notification::make()
                ->title('Produk Kosong')
                ->body('Minimal pilih 1 produk yang ingin dibeli dengan jumlah kuantiti lebih dari 0.')
                ->danger()
                ->send();
            return;
        }

        $proofPath = null;
        if ($this->proofImage) {
            $proofPath = $this->proofImage->store('purchase-proofs', 'public');
        }

        DB::transaction(function () use ($activeItems, $proofPath) {
            $purchase = Purchase::create([
                'supplier_id' => $this->supplierId,
                'user_id' => auth()->id(),
                'purchase_date' => $this->purchaseDate,
                'total_amount' => $this->getTotalAmount(),
                'status' => $this->status,
                'proof_image' => $proofPath,
            ]);

            foreach ($activeItems as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Increment product stock
                Product::where('id', $item['product_id'])->increment('stock', $item['qty']);
            }
        });

        Notification::make()
            ->title('Berhasil Menyimpan Pembelian')
            ->body('Data pembelian stok produk berhasil disimpan dan stok telah ditambahkan.')
            ->success()
            ->send();

        $this->redirect(PurchaseResource::getUrl('index'));
    }
}
