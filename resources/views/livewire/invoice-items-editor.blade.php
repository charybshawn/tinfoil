<div class="space-y-4">
    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Variation</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($items as $index => $item)
                    <tr>
                        <td class="px-6 py-4">
                            <select wire:model="items.{{ $index }}.product_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="">Select Product</option>
                                @foreach($products as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <!-- Other fields... -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <button type="button" wire:click="addRow" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg">
        Add Row
    </button>
</div> 