<div class="space-y-1 text-sm">
    @if($customer->email)
        <div>{{ $customer->email }}</div>
    @endif
    @if($customer->phone)
        <div>{{ $customer->phone }}</div>
    @endif
    @if($customer->address)
        <div>{{ $customer->address }}</div>
        <div>{{ $customer->city }}, {{ $customer->state }} {{ $customer->postal_code }}</div>
    @endif
</div> 