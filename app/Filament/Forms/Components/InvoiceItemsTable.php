<?php

namespace App\Filament\Forms\Components;

use App\Models\Product;
use App\Models\Variation;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns;
use Illuminate\Support\Collection;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Concerns\CanBeValidated;

class InvoiceItemsTable extends Component
{
    use Concerns\HasName;
    use Concerns\HasState;
    use Concerns\CanBeValidated;
    
    protected string $view = 'filament.forms.components.invoice-items-table';

    protected bool $showAddItemModal = false;
    protected ?int $selectedProduct = null;
    protected ?int $selectedVariation = null;

    public static function make(string $name): static
    {
        $static = app(static::class);
        $static->name($name);
        
        return $static->live()
            ->rules(['required', 'array', 'min:1']);
    }

    public function mount(): void
    {
        $this->state([]);
    }

    public function getProducts(): array
    {
        return Product::query()
            ->where('status', 'active')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getVariations(): array
    {
        if (!$this->selectedProduct) {
            return [];
        }

        return Product::find($this->selectedProduct)
            ->variations()
            ->where('status', 'active')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function addSelectedItem(Get $get, Set $set): void
    {
        if (!$this->selectedProduct || !$this->selectedVariation) {
            return;
        }

        $variation = Variation::find($this->selectedVariation);
        $currentItems = collect($get('state') ?? []);
        
        $currentItems->push([
            'product_id' => $this->selectedProduct,
            'variation_id' => $this->selectedVariation,
            'product_name' => $variation->product->name,
            'variation_name' => $variation->name,
            'quantity' => 1,
            'unit_price' => $variation->retail_price,
            'subtotal' => $variation->retail_price,
        ]);

        $set('state', $currentItems->toArray());
        $this->showAddItemModal = false;
        $this->selectedProduct = null;
        $this->selectedVariation = null;
    }

    public function removeItem(Get $get, Set $set, int $index): void
    {
        $currentItems = collect($get('state') ?? []);
        $currentItems->forget($index);
        $set('state', $currentItems->toArray());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateUpdated(function (Get $get) {
            $items = collect($get('state') ?? []);
            $this->subtotal = $items->sum('subtotal');
            $this->tax = $this->subtotal * 0.1;
            $this->total = $this->subtotal + $this->tax;
        });
    }

    public function getSubtotal(): float
    {
        $items = collect($this->getState() ?? []);
        return $items->sum(function ($item) {
            return ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
        });
    }

    public function getTax(): float
    {
        return $this->getSubtotal() * 0.1;
    }

    public function getTotal(): float
    {
        return $this->getSubtotal() + $this->getTax();
    }

    public function updateItemQuantity(Get $get, Set $set, int $index, int $quantity): void
    {
        $currentItems = collect($get('state') ?? []);
        if (!$currentItems->has($index)) return;

        $item = $currentItems[$index];
        $item['quantity'] = max(1, $quantity);
        $item['subtotal'] = $item['quantity'] * $item['unit_price'];
        $currentItems[$index] = $item;

        $set('state', $currentItems->toArray());
    }
} 