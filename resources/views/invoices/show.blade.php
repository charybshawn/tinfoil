<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-4">Invoice #{{ $invoice->number }}</h2>
                    <div class="mb-4">
                        <p>Total Amount: {{ money($invoice->total) }}</p>
                        <p>Status: {{ ucfirst($invoice->status) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 