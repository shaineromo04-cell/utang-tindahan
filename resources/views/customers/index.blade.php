<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customers') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4">

            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <a href="{{ route('customers.create') }}"
               class="block w-full text-center bg-blue-600 text-white py-3 rounded-lg font-medium mb-4">
                + Add Customer
            </a>

            <div class="space-y-2">
                @forelse ($customers as $customer)
                    <a href="{{ route('customers.show', $customer) }}"
                       class="flex justify-between items-center bg-white shadow-sm rounded-lg p-4 active:bg-gray-50">
                        <span class="font-medium text-gray-800">{{ $customer->name }}</span>
                        @php $balance = (float) ($customer->open_balance ?? 0); @endphp
                        <span class="text-sm font-semibold {{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            @if ($balance > 0)
                                ₱{{ number_format($balance, 2) }}
                            @else
                                Walang utang
                            @endif
                        </span>
                    </a>
                @empty
                    <p class="text-center text-gray-500 py-8">No customers yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>