<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Utang</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto px-4">
            <a href="{{ $selectedCustomerId ? route('customers.show', $selectedCustomerId) : route('customers.index') }}"
               class="inline-flex items-center text-sm text-gray-600 mb-4">
                ← Back
            </a>

            <form method="POST" action="{{ route('utangs.store') }}" class="bg-white shadow-sm rounded-lg p-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <select name="customer_id" required class="w-full border-gray-300 rounded-lg p-3">
                        <option value="">-- Select --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected($selectedCustomerId == $customer->id)>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₱)</label>
                    <input type="number" step="0.01" min="0.01" name="total_amount" value="{{ old('total_amount') }}" required
                           class="w-full border-gray-300 rounded-lg p-3" inputmode="decimal">
                    @error('total_amount')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                    <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-lg p-3"
                              placeholder="e.g. 2 Coke, 1 Lucky Me">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="w-full bg-orange-500 text-white py-3 rounded-lg font-medium">
                    Save Utang
                </button>
            </form>
        </div>
    </div>
</x-app-layout>