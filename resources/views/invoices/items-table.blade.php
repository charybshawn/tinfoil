<div class="space-y-4" x-data="invoiceItems({
    items: @entangle('data.items'),
    updateItems: {{ $updateItems }},
})">
    <x-filament::button
        type="button"
        x-on:click="addItem"
        size="sm"
    >
        Add Item
    </x-filament::button>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 text-left">Item</th>
                    <th class="px-4 py-2 text-right">Quantity</th>
                    <th class="px-4 py-2 text-right">Unit Price</th>
                    <th class="px-4 py-2 text-right">Amount</th>
                    <th class="px-4 py-2 w-10"></th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in items" :key="index">
                    <tr class="border-t">
                        <td class="px-4 py-2">
                            <x-filament::select
                                x-model="item.variation_id"
                                :options="\App\Models\Variation::all()->pluck('name', 'id')"
                                x-on:change="updateVariation(index, $event.target.value)"
                            />
                        </td>
                        <td class="px-4 py-2">
                            <x-filament::input
                                type="number"
                                x-model="item.quantity"
                                class="text-right w-24"
                                min="1"
                                x-on:change="updateLineTotal(index)"
                            />
                        </td>
                        <td class="px-4 py-2">
                            <x-filament::input
                                type="number"
                                x-model="item.price"
                                class="text-right w-32"
                                step="0.01"
                                x-on:change="updateLineTotal(index)"
                            />
                        </td>
                        <td class="px-4 py-2 text-right">
                            <span x-text="formatMoney(item.line_total)"></span>
                        </td>
                        <td class="px-4 py-2">
                            <x-filament::icon-button
                                icon="heroicon-m-trash"
                                color="danger"
                                x-on:click="removeItem(index)"
                            />
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('invoiceItems', ({ items, updateItems }) => ({
        items: items || [],

        addItem() {
            this.items.push({
                variation_id: '',
                quantity: 1,
                price: 0,
                line_total: 0,
            });
            this.updateItems(this.items);
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.updateItems(this.items);
        },

        async updateVariation(index, variationId) {
            const response = await fetch(`/api/variations/${variationId}`);
            const variation = await response.json();
            
            this.items[index].price = variation.wholesale_price || variation.retail_price;
            this.items[index].unit_type = variation.unit_type;
            this.items[index].unit_value = variation.unit_value;
            this.items[index].weight_unit = variation.weight_unit;
            
            this.updateLineTotal(index);
        },

        updateLineTotal(index) {
            const item = this.items[index];
            item.line_total = item.quantity * item.price;
            this.updateItems(this.items);
        },

        formatMoney(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            }).format(amount);
        },
    }));
});
</script>
@endpush 