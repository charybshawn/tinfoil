<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-4">Invoice #{{ $invoice->number }}</h2>
                    <div class="mb-4">
                        <p>Total Amount: {{ money($invoice->total) }}</p>
                    </div>
                    <div id="card-container"></div>
                    <button id="card-button" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">
                        Pay {{ money($invoice->total) }}
                    </button>
                    <div id="payment-status-container"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
    <script>
        const payments = Square.payments('{{ $squareAppId }}', '{{ $squareLocationId }}');
        const card = await payments.card();
        await card.attach('#card-container');

        const cardButton = document.getElementById('card-button');
        cardButton.addEventListener('click', async () => {
            const statusContainer = document.getElementById('payment-status-container');
            try {
                const result = await card.tokenize();
                if (result.status === 'OK') {
                    const response = await fetch('{{ route('invoice.process-payment', $invoice) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            sourceId: result.token,
                        }),
                    });
                    
                    const payment = await response.json();
                    if (payment.success) {
                        statusContainer.textContent = 'Payment complete!';
                        window.location.href = '{{ route('invoice.pay', $invoice) }}';
                    }
                }
            } catch (e) {
                statusContainer.textContent = e.message;
            }
        });

        // Add testing info
        const testingInfo = `
            <div class="mt-4 p-4 bg-gray-50 rounded">
                <h3 class="font-semibold">Test Card Numbers:</h3>
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li>Success: 4111 1111 1111 1111</li>
                    <li>Decline: 4000 0000 0000 0002</li>
                </ul>
                <p class="mt-2 text-sm text-gray-600">
                    Use any future expiration date and any CVV.
                </p>
            </div>
        `;
        document.querySelector('#card-container').insertAdjacentHTML('afterend', testingInfo);
    </script>
    @endpush
</x-app-layout> 