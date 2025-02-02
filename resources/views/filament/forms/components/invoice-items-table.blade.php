<div class="space-y-4">
    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="flex justify-between items-center p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Invoice Items</h3>
            <x-filament::button
                type="button"
                size="sm"
                x-on:click="$wire.set('data.showAddItemModal', true)"
            >
                Add Item
            </x-filament::button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        <th class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($getState() ?? [] as $index => $item)
                        <tr>
                            <td class="px-6 py-4">{{ $item['product_name'] ?? '' }}</td>
                            <td class="px-6 py-4">{{ $item['variation_name'] ?? '' }}</td>
                            <td class="px-6 py-4">
                                <input 
                                    type="number"
                                    wire:model.live.debounce.500ms="state.{{ $index }}.quantity"
                                    x-on:change="$wire.updateItemQuantity({{ $index }}, $event.target.value)"
                                    min="1"
                                    class="block w-24 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                                />
                            </td>
                            <td class="px-6 py-4 text-right">${{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-right">${{ number_format($item['subtotal'] ?? 0, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <x-filament::icon-button
                                    icon="heroicon-m-trash"
                                    color="danger"
                                    x-on:click="$wire.removeItem({{ $index }})"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No items added yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Subtotal</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${{ number_format($getSubtotal(), 2) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Tax (10%)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${{ number_format($getTax(), 2) }}</td>
                        <td></td>
                    </tr>
                    <tr class="border-t-2 border-gray-300">
                        <td colspan="4" class="px-6 py-4 text-base font-semibold text-gray-900 text-right">Total</td>
                        <td class="px-6 py-4 whitespace-nowrap text-base font-semibold text-gray-900 text-right">${{ number_format($getTotal(), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <x-filament::modal
        id="add-invoice-item"
        :heading="__('Add Item')"
        wire:model="data.showAddItemModal"
    >
        <div class="space-y-4">
            <x-filament-forms::select
                name="selectedProduct"
                label="Product"
                :options="$this->getProducts()"
                wire:model.live="data.selectedProduct"
            />

            @if($selectedProduct)
                <x-filament-forms::select
                    name="selectedVariation"
                    label="Variation"
                    :options="$this->getVariations()"
                    wire:model.live="data.selectedVariation"
                />
            @endif
        </div>

        <x-slot name="footer">
            <x-filament::button
                x-on:click="$wire.addSelectedItem()"
                :disabled="!$selectedProduct || !$selectedVariation"
            >
                Add Item
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</div> 