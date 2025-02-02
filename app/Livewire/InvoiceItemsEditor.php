class InvoiceItemsEditor extends Component
{
    public $items = [];
    public $products = [];
    public $variations = [];
    
    public function mount()
    {
        $this->products = Product::pluck('name', 'id');
        $this->items = [
            ['product_id' => null, 'variation_id' => null, 'quantity' => 1, 'unit_price' => 0]
        ];
    }

    public function addRow()
    {
        $this->items[] = ['product_id' => null, 'variation_id' => null, 'quantity' => 1, 'unit_price' => 0];
    }

    public function removeRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        [$index, $field] = explode('.', $key);
        
        if ($field === 'product_id') {
            $this->items[$index]['variation_id'] = null;
            $this->variations[$index] = Product::find($value)?->variations()->pluck('name', 'id') ?? [];
        }
        
        if ($field === 'variation_id') {
            $variation = Variation::find($value);
            if ($variation) {
                $this->items[$index]['unit_price'] = $variation->price;
            }
        }
    }

    public function render()
    {
        return view('livewire.invoice-items-editor');
    }
} 