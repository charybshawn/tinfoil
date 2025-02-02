<div class="space-y-4">
    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variation</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Quantity</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Unit Price</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Subtotal</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($items as $index => $item)
                    <tr wire:key="item-{{ $index }}">
                        <td class="px-4 py-2">
                            <select 
                                wire:model.live="items.{{ $index }}.product_id"
                                class="w-full border-gray-300 rounded-lg shadow-sm text-sm"
                            >
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <select 
                                wire:model.live="items.{{ $index }}.variation_id"
                                class="w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                @if(!$item['product_id']) disabled @endif
                            >
                                <option value="">Select Variation</option>
                                @foreach($variations as $variation)
                                    <option value="{{ $variation->id }}">{{ $variation->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <input 
                                type="number" 
                                wire:model.live="items.{{ $index }}.quantity"
                                class="w-full border-gray-300 rounded-lg shadow-sm text-sm text-right"
                                min="1"
                                step="1"
                            >
                        </td>
                        <td class="px-4 py-2">
                            <input 
                                type="number" 
                                wire:model.live="items.{{ $index }}.unit_price"
                                class="w-full border-gray-300 rounded-lg shadow-sm text-sm text-right"
                                step="0.01"
                            >
                        </td>
                        <td class="px-4 py-2 text-right">
                            ${{ number_format($item['subtotal'], 2) }}
                        </td>
                        <td class="px-4 py-2 text-right">
                            <button 
                                wire:click="removeItem({{ $index }})"
                                class="text-red-600 hover:text-red-800"
                            >
                                <x-heroicon-m-trash class="w-5 h-5" />
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50">
                        <td colspan="4" class="px-4 py-3 text-right text-sm font-medium">Total:</td>
                        <td class="px-4 py-3 text-right text-sm font-medium">${{ number_format($total, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <button 
        type="button"
        wire:click="addItem"
        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700"
    >
        Add Item
    </button>
</div> 