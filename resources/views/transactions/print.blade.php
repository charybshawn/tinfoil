<!DOCTYPE html>
<html>
<head>
    <title>Transaction #{{ $transaction->number }}</title>
    <style>
        /* Add your print styles here */
    </style>
</head>
<body>
    <div class="transaction-print">
        <h1>Transaction #{{ $transaction->number }}</h1>
        
        <div class="customer-info">
            <h3>Customer</h3>
            <p>{{ $transaction->customer->name }}</p>
        </div>

        <div class="transaction-details">
            <p><strong>Date:</strong> {{ $transaction->created_at->format('M d, Y') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($transaction->status) }}</p>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Subtotal</td>
                    <td>${{ number_format($transaction->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3">Tax</td>
                    <td>${{ number_format($transaction->tax, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong>${{ number_format($transaction->total, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html> 