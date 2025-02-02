<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->number }}</title>
    <style>
        :root {
            --primary-color: #006aff;
            --border-color: #e5e7eb;
            --text-color: #1f2937;
            --secondary-text: #6b7280;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.5;
            color: var(--text-color);
            margin: 0;
            padding: 40px;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .company-details {
            flex: 1;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-id {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .billing-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-bottom: 40px;
        }

        .billing-section h2 {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--secondary-text);
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        th {
            background-color: #f9fafb;
            padding: 12px;
            text-align: left;
            font-size: 0.875rem;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .amount-column {
            text-align: right;
        }

        .totals-section {
            width: 300px;
            margin-left: auto;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .grand-total {
            font-weight: 600;
            font-size: 1.125rem;
            border-top: 2px solid var(--border-color);
            padding-top: 12px;
            margin-top: 12px;
        }

        .notes-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            color: var(--secondary-text);
        }

        @media print {
            body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div class="company-details">
            <h1>{{ config('app.name') }}</h1>
            <!-- Add your company details here -->
        </div>
        <div class="invoice-details">
            <div class="invoice-id">Invoice #{{ $invoice->number }}</div>
            <div>Issue Date: {{ $invoice->issue_date->format('F j, Y') }}</div>
            <div>Due Date: {{ $invoice->due_date->format('F j, Y') }}</div>
        </div>
    </div>

    <div class="billing-grid">
        <div class="billing-section">
            <h2>Bill To</h2>
            <div>{{ $invoice->customer->name }}</div>
            <div>{{ $invoice->customer->email }}</div>
            <div>{{ $invoice->customer->phone }}</div>
            <div>{{ $invoice->customer->address }}</div>
            <div>{{ $invoice->customer->city }}, {{ $invoice->customer->state }} {{ $invoice->customer->postal_code }}</div>
        </div>
        <div class="billing-section">
            <h2>Payment Terms</h2>
            <div>{{ $invoice->paymentTerms->name }}</div>
            <div>{{ $invoice->paymentTerms->description }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th class="amount-column">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>
                    <div>{{ $item->variation->name }}</div>
                    <div style="color: var(--secondary-text); font-size: 0.875rem;">
                        {{ $item->unit_value }} {{ $item->unit_type }}
                    </div>
                </td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price, 2) }}</td>
                <td class="amount-column">${{ number_format($item->quantity * $item->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <div class="total-row">
            <span>Subtotal</span>
            <span>${{ number_format($invoice->subtotal, 2) }}</span>
        </div>
        <div class="total-row">
            <span>Tax (10%)</span>
            <span>${{ number_format($invoice->tax, 2) }}</span>
        </div>
        <div class="total-row grand-total">
            <span>Total</span>
            <span>${{ number_format($invoice->total, 2) }}</span>
        </div>
    </div>

    @if($invoice->notes)
    <div class="notes-section">
        <h2>Notes</h2>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif
</body>
</html> 