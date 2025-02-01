<div class="space-y-4">
    <div class="p-4 space-y-4">
        <div class="filament-tables-container rounded-xl border border-gray-300 overflow-hidden">
            <table class="filament-tables-table w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Products</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\Category::withCount('products')->get() as $category)
                        <tr class="border-t hover:bg-gray-50 cursor-pointer" 
                            x-on:click="$wire.set('data.category_id', '{{ $category->id }}'); close()">
                            <td class="px-4 py-2">{{ $category->name }}</td>
                            <td class="px-4 py-2">{{ $category->products_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 