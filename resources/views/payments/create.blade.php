<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Record Payment</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto px-4">
            <a href="{{ $selectedCustomerId ? route('customers.show', $selectedCustomerId) : route('customers.index') }}"
               class="inline-flex items-center text-sm text-gray-600 mb-4">
                ← Back
            </a>

            <form method="GET" action="{{ route('payments.create') }}" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                <select name="customer_id" onchange="this.form.submit()" class="w-full border-gray-300 rounded-lg p-3">
                    <option value="">-- Select customer --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected($selectedCustomerId == $customer->id)>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if ($selectedCustomerId)
                <form method="POST" action="{{ route('payments.store') }}" class="bg-white shadow-sm rounded-lg p-4 space-y-4">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $selectedCustomerId }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apply to</label>
                        <select name="utang_id" class="w-full border-gray-300 rounded-lg p-3">
                            <option value="">General payment (auto-apply to oldest utang)</option>
                            @foreach ($openUtangs as $utang)
                                <option value="{{ $utang->id }}">
                                    #{{ $utang->id }} &mdash; ₱{{ number_format($utang->balance, 2) }} remaining
                                    ({{ $utang->created_at->format('M d') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₱)</label>
                        <input type="number" step="0.01" min="0.01" name="amount" required
                               class="w-full border-gray-300 rounded-lg p-3" inputmode="decimal">
                        @error('amount')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Method</label>
                        <select name="payment_method" class="w-full border-gray-300 rounded-lg p-3">
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                        <input type="text" name="notes" class="w-full border-gray-300 rounded-lg p-3">
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-medium">
                        Save Payment
                    </button>
                </form>
            @else
                <p class="text-center text-gray-500 py-8">Select a customer to continue.</p>
            @endif
        </div>
    </div>
</x-app-layout>