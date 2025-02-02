<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="fi-ta rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" 
        x-data="invoiceItemsField($wire.entangle('{{ $getStatePath() }}'))"
    >
        <div class="fi-ta-table w-full divide-y divide-gray-200 dark:divide-white/5">
            <thead>
                <tr class="bg-gray-50 dark:bg-white/5">
                    <th class="fi-ta-header-cell px-6 py-3 text-left">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Item</span>
                    </th>
                    <th class="fi-ta-header-cell px-6 py-3 text-right w-32">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Qty</span>
                    </th>
                    <th class="fi-ta-header-cell px-6 py-3 text-right w-40">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Price</span>
                    </th>
                    <th class="fi-ta-header-cell px-6 py-3 text-right w-40">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total</span>
                    </th>
                    <th class="w-10 px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                <!-- Selected Items -->
                <template x-for="(item, index) in items" :key="index">
                    <tr class="group hover:bg-gray-50 dark:hover:bg-white/5">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-medium" x-text="products.find(p => p.id == item.product_id)?.name || ''"></span>
                                <span class="text-sm text-gray-500" x-text="item.unit_type + ' - ' + item.unit_value"></span>
                                <span x-show="item.notes" class="text-sm text-gray-400 italic" x-text="item.notes"></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <input 
                                type="number"
                                x-model="item.quantity"
                                @input="calculateItemTotal(index)"
                                class="block w-full border-none bg-transparent text-right"
                                min="1"
                                step="1"
                            >
                        </td>
                        <td class="px-6 py-4 text-right" x-text="formatMoney(item.price)"></td>
                        <td class="px-6 py-4 text-right" x-text="formatMoney(item.subtotal)"></td>
                        <td class="px-6 py-4">
                            <button 
                                @click="removeItem(index)"
                                type="button"
                                class="text-gray-400 hover:text-danger-600 transition-colors"
                            >
                                <x-heroicon-m-trash class="h-5 w-5" />
                            </button>
                        </td>
                    </tr>
                </template>

                <!-- Add Item Row -->
                <tr>
                    <td colspan="5" class="px-6 py-4">
                        <div class="relative">
                            <!-- Error Alert -->
                            <div 
                                x-show="error"
                                x-transition
                                class="absolute bottom-full left-0 right-0 mb-2 p-2 text-sm rounded-lg bg-danger-50 dark:bg-danger-500/10 text-danger-600 dark:text-danger-400"
                            >
                                <div class="flex items-center">
                                    <x-heroicon-m-x-circle class="h-4 w-4 mr-2 flex-shrink-0" />
                                    <span x-text="error"></span>
                                </div>
                            </div>

                            <select 
                                x-model="selectedProduct"
                                @change="onProductSelect"
                                :class="{'opacity-50': loading}"
                                :disabled="loading"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">Add an item...</option>
                                <template x-for="product in products" :key="product.id">
                                    <option :value="product.id" x-text="product.name"></option>
                                </template>
                            </select>

                            <!-- Loading Indicator -->
                            <div 
                                x-show="loading"
                                class="absolute inset-y-0 right-0 flex items-center pr-3"
                            >
                                <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot class="border-t border-gray-200 dark:border-white/10">
                <tr>
                    <td colspan="3">
                        <div class="px-6 py-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                                <span class="font-medium text-gray-950 dark:text-white" x-text="formatMoney(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-sm items-center">
                                <span class="text-gray-500 dark:text-gray-400">Discount</span>
                                <input 
                                    type="number" 
                                    x-model="discount"
                                    @input="calculateTotals"
                                    class="fi-input w-32 border-none bg-transparent px-3 py-1.5 text-sm text-right text-gray-950 focus:ring-2 focus:ring-primary-600 dark:text-white dark:focus:ring-primary-500"
                                    min="0"
                                    step="0.01"
                                >
                            </div>
                            <div class="flex justify-between text-sm items-center">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-500 dark:text-gray-400">Tax Rate (%)</span>
                                    <input 
                                        type="number" 
                                        x-model="taxRate"
                                        @input="calculateTotals"
                                        class="fi-input w-20 border-none bg-transparent px-3 py-1.5 text-sm text-right text-gray-950 focus:ring-2 focus:ring-primary-600 dark:text-white dark:focus:ring-primary-500"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                    >
                                </div>
                                <span class="font-medium text-gray-950 dark:text-white" x-text="formatMoney(tax)"></span>
                            </div>
                            <div class="flex justify-between text-sm items-center">
                                <span class="text-gray-500 dark:text-gray-400">Shipping Charge</span>
                                <input 
                                    type="number" 
                                    x-model="shippingCharge"
                                    @input="calculateTotals"
                                    class="fi-input w-32 border-none bg-transparent px-3 py-1.5 text-sm text-right text-gray-950 focus:ring-2 focus:ring-primary-600 dark:text-white dark:focus:ring-primary-500"
                                    min="0"
                                    step="0.01"
                                >
                            </div>
                            <div class="flex justify-between text-base font-medium border-t border-gray-200 pt-2 dark:border-white/10">
                                <span class="text-gray-950 dark:text-white">Total</span>
                                <span class="text-gray-950 dark:text-white" x-text="formatMoney(total)"></span>
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </div>

        <!-- Variation Selection Modal -->
        <div
            x-show="showModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
        >
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    x-show="showModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                    @click.away="closeModal"
                >
                    <!-- Modal Error Message -->
                    <div 
                        x-show="error"
                        x-transition
                        class="mb-4 p-4 rounded-lg bg-danger-50 dark:bg-danger-500/10 text-danger-600 dark:text-danger-400"
                    >
                        <span x-text="error"></span>
                    </div>

                    <div class="space-y-4">
                        <div class="text-lg font-medium" x-text="products.find(p => p.id == selectedProduct)?.name"></div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Select Variation</label>
                            <select 
                                x-model="selectedVariation"
                                @change="error = null"
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">Choose a variation...</option>
                                <template x-for="variation in variations" :key="variation.id">
                                    <option :value="variation.id" x-text="variation.name + ' - ' + formatMoney(variation.price)"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Notes</label>
                            <textarea
                                x-model="itemNote"
                                rows="3"
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                placeholder="Add notes for this item..."
                            ></textarea>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button
                            type="button"
                            class="inline-flex w-full justify-center rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 sm:col-start-2"
                            @click="addItem"
                            :disabled="!selectedVariation"
                        >
                            Add Item
                        </button>
                        <button
                            type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0"
                            @click="closeModal"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('invoiceItemsField', (modelData) => ({
                items: modelData,
                products: @js(\App\Models\Product::where('status', 'active')->get()),
                variations: [],
                loading: false,
                error: null,
                selectedProduct: '',
                selectedVariation: null,
                itemNote: '',
                showModal: false,
                subtotal: 0,
                discount: 0,
                tax: 0,
                taxRate: 0,
                shippingCharge: 0,
                total: 0,

                init() {
                    if (!Array.isArray(this.items)) {
                        this.items = [];
                    }
                    this.calculateTotals();
                },

                formatMoney(amount) {
                    return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(amount);
                },

                async onProductSelect(event) {
                    this.error = null;
                    const productId = event.target.value;
                    
                    if (!productId) {
                        return;
                    }
                    
                    this.selectedProduct = productId;
                    this.loading = true;
                    this.selectedVariation = null;
                    this.itemNote = '';
                    
                    try {
                        const response = await fetch(`/admin/api/product-variations/${productId}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        });
                        
                        if (!response.ok) {
                            const errorData = await response.json().catch(() => null);
                            throw new Error(errorData?.message || `HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        console.log('Variations loaded:', data);
                        
                        if (!Array.isArray(data) || data.length === 0) {
                            throw new Error('No variations available for this product');
                        }
                        
                        this.variations = data;
                        this.error = null;
                        this.showModal = true;
                        
                    } catch (error) {
                        console.error('Error loading variations:', error);
                        this.error = 'Failed to load variations. Please try again.';
                        this.selectedProduct = '';
                        event.target.value = '';
                    } finally {
                        this.loading = false;
                    }
                },

                addItem() {
                    if (!this.selectedVariation) {
                        this.error = 'Please select a variation';
                        return;
                    }

                    const variation = this.variations.find(v => v.id == this.selectedVariation);
                    if (!variation) {
                        this.error = 'Selected variation not found';
                        return;
                    }

                    try {
                        this.items.push({
                            product_id: this.selectedProduct,
                            variation_id: this.selectedVariation,
                            quantity: 1,
                            price: parseFloat(variation.price) || 0,
                            unit_type: variation.unit_type || '',
                            unit_value: variation.unit_value || '',
                            notes: this.itemNote || '',
                            subtotal: parseFloat(variation.price) || 0
                        });
                        
                        this.calculateTotals();
                        this.closeModal();
                    } catch (error) {
                        console.error('Error adding item:', error);
                        this.error = 'Failed to add item';
                    }
                },

                closeModal() {
                    this.showModal = false;
                    this.selectedProduct = '';
                    this.selectedVariation = null;
                    this.itemNote = '';
                    this.variations = [];
                    this.error = null;
                    // Reset the select element
                    const selectElement = document.querySelector('[x-model="selectedProduct"]');
                    if (selectElement) {
                        selectElement.value = '';
                    }
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotals();
                },

                calculateItemTotal(index) {
                    const item = this.items[index];
                    item.quantity = parseInt(item.quantity) || 1;
                    item.price = parseFloat(item.price) || 0;
                    item.subtotal = item.quantity * item.price;
                    this.calculateTotals();
                },

                calculateTotals() {
                    this.subtotal = this.items.reduce((sum, item) => {
                        return sum + (parseFloat(item.subtotal) || 0);
                    }, 0);
                    
                    this.discount = parseFloat(this.discount) || 0;
                    this.taxRate = parseFloat(this.taxRate) || 0;
                    this.shippingCharge = parseFloat(this.shippingCharge) || 0;
                    
                    this.tax = (this.subtotal - this.discount) * (this.taxRate / 100);
                    this.total = this.subtotal - this.discount + this.tax + this.shippingCharge;
                }
            }))
        })
    </script>
</x-dynamic-component> 