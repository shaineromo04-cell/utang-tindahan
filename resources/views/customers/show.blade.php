<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $customer->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 space-y-6">

            @if (session('status'))
                <div class="p-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <a href="{{ route('customers.index') }}" class="inline-flex items-center text-sm text-gray-600">
                ← Back to Customers
            </a>

            <div class="bg-white shadow-sm rounded-lg p-4 text-center">

            <div class="bg-white shadow-sm rounded-lg p-4 text-center">
                <p class="text-sm text-gray-500">Total Utang</p>
                <p class="text-3xl font-bold {{ $customer->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                    ₱{{ number_format($customer->balance, 2) }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('utangs.create', ['customer_id' => $customer->id]) }}"
                   class="text-center bg-orange-500 text-white py-3 rounded-lg font-medium">
                    + New Utang
                </a>
                <a href="{{ route('payments.create', ['customer_id' => $customer->id]) }}"
                   class="text-center bg-green-600 text-white py-3 rounded-lg font-medium">
                    Record Payment
                </a>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Utang History</h3>
                <div class="space-y-2">
                    @forelse ($utangs as $utang)
                        <div class="bg-white shadow-sm rounded-lg p-4">
                            <div class="flex justify-between">
                                <span class="font-medium">₱{{ number_format($utang->total_amount, 2) }}</span>
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $utang->status === 'paid' ? 'bg-green-100 text-green-700' : ($utang->status === 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($utang->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500">
                                Paid: ₱{{ number_format($utang->amount_paid, 2) }}
                                &middot; {{ $utang->created_at->format('M d, Y') }}
                            </p>
                            @if ($utang->notes)
                                <p class="text-sm text-gray-600 mt-1">{{ $utang->notes }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No utang recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Payment History</h3>
                <div class="space-y-2">
                    @forelse ($payments as $payment)
                        <div class="bg-white shadow-sm rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-green-700">₱{{ number_format($payment->amount, 2) }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ strtoupper($payment->payment_method) }}
                                    &middot; {{ $payment->created_at->format('M d, Y g:i A') }}
                                </p>
                            </div>
                            <span class="text-xs text-gray-400">
                                {{ $payment->utang_id ? 'Utang #' . $payment->utang_id : 'General credit' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No payments yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>