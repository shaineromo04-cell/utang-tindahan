<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 space-y-8">

            {{-- Key numbers --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Utang</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">
                        ₱{{ number_format($totalOutstanding, 2) }}
                    </p>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Customers</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">
                        {{ $totalCustomers }}
                    </p>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Collected Today</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">
                        ₱{{ number_format($collectedToday, 2) }}
                    </p>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Collected This Month</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">
                        ₱{{ number_format($collectedThisMonth, 2) }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Top debtors --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">
                        Top Utang
                    </h3>
                    <div class="border border-gray-200 rounded-lg divide-y divide-gray-100">
                        @forelse ($topDebtors as $customer)
                            <a href="{{ route('customers.show', $customer) }}"
                               class="flex justify-between items-center px-4 py-3 hover:bg-gray-50">
                                <span class="text-sm text-gray-800">{{ $customer->name }}</span>
                                <span class="text-sm font-medium text-red-600">
                                    ₱{{ number_format($customer->open_balance, 2) }}
                                </span>
                            </a>
                        @empty
                            <p class="px-4 py-3 text-sm text-gray-500">No outstanding utang.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Recent activity --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">
                        Recent Payments
                    </h3>
                    <div class="border border-gray-200 rounded-lg divide-y divide-gray-100">
                        @forelse ($recentActivity as $payment)
                            <div class="flex justify-between items-center px-4 py-3">
                                <div>
                                    <p class="text-sm text-gray-800">{{ $payment->customer->name }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $payment->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="text-sm font-medium text-green-600">
                                    ₱{{ number_format($payment->amount, 2) }}
                                </span>
                            </div>
                        @empty
                            <p class="px-4 py-3 text-sm text-gray-500">No payments yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <a href="{{ route('customers.index') }}"
               class="inline-block text-sm text-blue-600 hover:underline">
                View all customers →
            </a>
        </div>
    </div>
</x-app-layout>