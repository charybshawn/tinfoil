<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="fi-ta rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" x-data="{
        items: $wire.$entangle('{{ $getStatePath() }}'),
        products: @js(\App\Models\Product::where('status', 'active')->get()),
        variations: [],
        total: 0,
        subtotal: 0,
        tax: 0,
        taxRate: 0.10, // 10% tax rate

        init() {
            if (!this.items.length) {
                this.addItem();
            }
            this.calculateTotals();
        },

        addItem() {
            this.items.push({
                product_id: '',
                variation_id: '',
                quantity: 1,
                unit_price: 0,
                unit_type: '',
                unit_value: '',
                subtotal: 0
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotals();
        },

        async updateProduct(index) {
            const item = this.items[index];
            if (item.product_id) {
                const response = await fetch(`/api/products/${item.product_id}/variations`);
                const data = await response.json();
                this.variations[index] = data;
            } else {
                this.variations[index] = [];
            }
            item.variation_id = '';
            item.unit_price = 0;
            this.calculateItemTotal(index);
        },

        updateVariation(index) {
            const item = this.items[index];
            if (item.variation_id) {
                const variation = this.variations[index].find(v => v.id == item.variation_id);
                if (variation) {
                    item.unit_price = variation.price;
                    item.unit_type = variation.unit_type;
                    item.unit_value = variation.unit_value;
                }
            }
            this.calculateItemTotal(index);
        },

        calculateItemTotal(index) {
            const item = this.items[index];
            item.subtotal = item.quantity * item.unit_price;
            this.calculateTotals();
        },

        calculateTotals() {
            this.subtotal = this.items.reduce((sum, item) => sum + (item.subtotal || 0), 0);
            this.tax = this.subtotal * this.taxRate;
            this.total = this.subtotal + this.tax;
        },

        formatMoney(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(amount);
        }
    }">
        <!-- Invoice Header -->
        <div class="fi-ta-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
            <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">Invoice Items</h3>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th class="fi-ta-header-cell px-6 py-3 text-start">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Product</span>
                        </th>
                        <th class="fi-ta-header-cell px-6 py-3 text-start">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Variation</span>
                        </th>
                        <th class="fi-ta-header-cell px-6 py-3 text-right w-32">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Quantity</span>
                        </th>
                        <th class="fi-ta-header-cell px-6 py-3 text-right w-40">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Unit Price</span>
                        </th>
                        <th class="fi-ta-header-cell px-6 py-3 text-right w-40">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Amount</span>
                        </th>
                        <th class="fi-ta-header-cell w-10 px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                    <template x-for="(item, index) in items" :key="index">
                        <tr class="fi-ta-row group hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="fi-ta-cell px-6 py-3">
                                <select 
                                    x-model="item.product_id"
                                    @change="updateProduct(index)"
                                    class="fi-select-input block w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:text-white dark:focus:ring-primary-500"
                                >
                                    <option value="">Select Product</option>
                                    <template x-for="product in products" :key="product.id">
                                        <option :value="product.id" x-text="product.name"></option>
                                    </template>
                                </select>
                            </td>
                            <td class="fi-ta-cell px-6 py-3">
                                <select 
                                    x-model="item.variation_id"
                                    @change="updateVariation(index)"
                                    class="fi-select-input block w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:text-white dark:focus:ring-primary-500"
                                    :disabled="!item.product_id"
                                >
                                    <option value="">Select Variation</option>
                                    <template x-for="variation in variations[index]" :key="variation.id">
                                        <option :value="variation.id" x-text="variation.name"></option>
                                    </template>
                                </select>
                            </td>
                            <td class="fi-ta-cell px-6 py-3">
                                <input 
                                    type="number"
                                    x-model="item.quantity"
                                    @input="calculateItemTotal(index)"
                                    class="fi-input block w-full border-none bg-transparent px-3 py-1.5 text-base text-right text-gray-950 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:text-white dark:focus:ring-primary-500"
                                    min="1"
                                    step="1"
                                >
                            </td>
                            <td class="fi-ta-cell px-6 py-3">
                                <input 
                                    type="number"
                                    x-model="item.unit_price"
                                    @input="calculateItemTotal(index)"
                                    class="fi-input block w-full border-none bg-transparent px-3 py-1.5 text-base text-right text-gray-950 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:text-white dark:focus:ring-primary-500"
                                    step="0.01"
                                >
                            </td>
                            <td class="fi-ta-cell px-6 py-3 text-right font-medium text-gray-950 dark:text-white" x-text="formatMoney(item.subtotal || 0)"></td>
                            <td class="fi-ta-cell px-6 py-3">
                                <button 
                                    @click="removeItem(index)"
                                    class="fi-icon-btn text-gray-400 hover:text-danger-600 dark:hover:text-danger-500 transition-colors invisible group-hover:visible"
                                    type="button"
                                >
                                    <x-heroicon-m-trash class="h-5 w-5" />
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="border-t border-gray-200 dark:border-white/10">
                    <tr>
                        <td colspan="3" class="px-6 py-4">
                            <button 
                                type="button"
                                @click="addItem"
                                class="fi-btn fi-btn-size-sm inline-flex items-center justify-center gap-x-1 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 dark:bg-primary-500 dark:hover:bg-primary-400"
                            >
                                <x-heroicon-m-plus-circle class="h-5 w-5" />
                                <span>Add Item</span>
                            </button>
                        </td>
                        <td colspan="3">
                            <div class="px-6 py-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                                    <span class="font-medium text-gray-950 dark:text-white" x-text="formatMoney(subtotal)"></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Tax (10%)</span>
                                    <span class="font-medium text-gray-950 dark:text-white" x-text="formatMoney(tax)"></span>
                                </div>
                                <div class="flex justify-between text-base font-medium border-t border-gray-200 pt-2 dark:border-white/10">
                                    <span class="text-gray-950 dark:text-white">Total</span>
                                    <span class="text-gray-950 dark:text-white" x-text="formatMoney(total)"></span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-dynamic-component> 