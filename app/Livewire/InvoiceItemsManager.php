<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Variation;
use Illuminate\Support\Collection;

class InvoiceItemsManager extends Component
{
    public $items = [];
    public $products;
    public Collection $variations;
    public $total = 0;

    public function mount($existingItems = [])
    {
        $this->products = Product::where('status', 'active')->get();
        $this->variations = collect();
        $this->items = $existingItems ?: [
            $this->getEmptyItem()
        ];
        $this->calculateTotal();
    }

    public function addItem()
    {
        $this->items[] = $this->getEmptyItem();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1];

        if ($field === 'product_id') {
            // Reset variation when product changes
            $this->items[$index]['variation_id'] = null;
            $this->items[$index]['unit_price'] = 0;
            $this->items[$index]['subtotal'] = 0;
            
            // Load variations for the selected product
            $product = Product::find($value);
            if ($product) {
                $this->variations = $product->variations;
            }
        }

        if ($field === 'variation_id') {
            $variation = Variation::find($value);
            if ($variation) {
                $this->items[$index]['unit_price'] = $variation->price;
                $this->items[$index]['unit_type'] = $variation->unit_type;
                $this->items[$index]['unit_value'] = $variation->unit_value;
            }
        }

        if (in_array($field, ['quantity', 'unit_price', 'variation_id'])) {
            $this->calculateItemSubtotal($index);
        }

        $this->calculateTotal();
    }

    private function calculateItemSubtotal($index)
    {
        $quantity = floatval($this->items[$index]['quantity']);
        $unitPrice = floatval($this->items[$index]['unit_price']);
        $this->items[$index]['subtotal'] = $quantity * $unitPrice;
    }

    private function calculateTotal()
    {
        $this->total = array_sum(array_column($this->items, 'subtotal'));
    }

    private function getEmptyItem()
    {
        return [
            'product_id' => '',
            'variation_id' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'unit_type' => '',
            'unit_value' => '',
            'subtotal' => 0,
        ];
    }

    public function render()
    {
        return view('livewire.invoice-items-manager');
    }
} 