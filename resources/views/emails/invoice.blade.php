<x-mail::message>
# Invoice from {{ config('app.name') }}

Dear {{ $invoice->customer->name }},

Please find attached invoice #{{ $invoice->number }} for {{ money($invoice->total) }}.

<x-mail::panel>
## Invoice Summary
- Invoice Number: {{ $invoice->number }}
- Issue Date: {{ $invoice->issue_date->format('M d, Y') }}
- Due Date: {{ $invoice->due_date?->format('M d, Y') ?? 'Due on receipt' }}
- Total Amount: {{ money($invoice->total) }}
</x-mail::panel>

<x-mail::table>
| Item | Quantity | Price | Total |
|:-----|:---------|:------|:------|
@foreach($invoice->items as $item)
| {{ $item->productVariation->product->name }} | {{ $item->quantity }} | {{ money($item->price) }} | {{ money($item->line_total) }} |
@endforeach
| | | **Total:** | **{{ money($invoice->total) }}** |
</x-mail::table>

<x-mail::button :url="$paymentLink" color="success">
Pay Invoice Online
</x-mail::button>

Thank you for your business!

Best regards,<br>
{{ config('app.name') }}
</x-mail::message> 