@php
    $record = $getRecord();
    $variations = $record 
        ? $record->variations 
        : ($this->data['variations'] ?? collect([]));
@endphp

@if($variations && count($variations) > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2 px-4">Name</th>
                    <th class="text-left py-2 px-4">UPC</th>
                    <th class="text-left py-2 px-4">Unit Type</th>
                    <th class="text-left py-2 px-4">Weight</th>
                    <th class="text-right py-2 px-4">Price</th>
                    <th class="text-right py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($variations as $variation)
                    <tr class="border-b">
                        <td class="py-2 px-4">{{ is_array($variation) ? $variation['name'] : $variation->name }}</td>
                        <td class="py-2 px-4">{{ is_array($variation) ? $variation['upc'] : $variation->upc }}</td>
                        <td class="py-2 px-4">{{ is_array($variation) ? $variation['unit_type'] : $variation->unit_type }}</td>
                        <td class="py-2 px-4">
                            {{ is_array($variation) ? $variation['weight'] : $variation->weight }}
                            {{ is_array($variation) ? $variation['weight_unit'] : $variation->weight_unit }}
                        </td>
                        <td class="py-2 px-4 text-right">${{ is_array($variation) ? $variation['price'] : $variation->price }}</td>
                        <td class="py-2 px-4 text-right">
                            @if(!is_array($variation))
                            <x-filament::button
                                color="warning"
                                size="sm"
                                icon="heroicon-m-pencil"
                                wire:click="editVariation({{ $variation->id }})"
                            >
                                Edit
                            </x-filament::button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-gray-500 text-center py-4">
        No variations added yet
    </div>
@endif 