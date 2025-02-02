<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="filament-repeater-component space-y-2">
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="overflow-hidden">
                <table class="w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variation</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        {{ $getChildComponentContainer() }}
                    </tbody>
                </table>
            </div>
        </div>

        @if (! $isDisabled())
            <div class="flex justify-start px-4 py-2">
                <x-filament::button
                    type="button"
                    size="sm"
                    outlined
                    color="gray"
                    wire:click="dispatchFormEvent('repeater::createItem', '{{ $getStatePath() }}')"
                    wire:target="dispatchFormEvent('repeater::createItem', '{{ $getStatePath() }}')"
                >
                    <x-heroicon-m-plus class="w-4 h-4 mr-1" />
                    {{ $getAddActionLabel() }}
                </x-filament::button>
            </div>
        @endif
    </div>
</x-dynamic-component> 