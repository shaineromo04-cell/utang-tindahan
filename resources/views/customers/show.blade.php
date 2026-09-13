<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $customer->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 space-y-6">

            <a href="{{ route('customers.index') }}" class="inline-flex items-center text-sm text-gray-600">
                ← Back to Customers
            </a>

            @if (session('status'))
                <div class="p-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @error('password')
                <div class="p-3 rounded bg-red-100 text-red-800 text-sm">
                    {{ $message }}
                </div>
            @enderror

            @error('delete')
                <div class="p-3 rounded bg-red-100 text-red-800 text-sm">
                    {{ $message }}
                </div>
            @enderror

            <div class="bg-white shadow-sm rounded-lg p-4 text-center">
                <p class="text-sm text-gray-500">Total Utang</p>
                <p class="text-3xl font-bold {{ $customer->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                    ₱{{ number_format($customer->balance, 2) }}
                </p>
            </div>

            @if ($customer->balance <= 0)
                <div x-data="{ open: false }" class="relative flex justify-end">
                    <button type="button" @click="open = true" class="text-xs text-red-600 hover:text-red-700 font-medium">
                        Delete Customer
                    </button>

                    <div x-show="open" x-cloak
                         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                        <div class="bg-white rounded-lg p-4 w-full max-w-xs" @click.outside="open = false">
                            <form method="POST" action="{{ route('customers.destroy', $customer) }}">
                                @csrf
                                @method('DELETE')

                                <p class="text-sm font-medium text-gray-800 mb-1">Delete {{ $customer->name }}?</p>
                                <p class="text-xs text-gray-500 mb-2">This also removes their utang and payment history. This cannot be undone.</p>

                                <input type="password" name="password" required
                                       class="w-full border-gray-300 rounded-lg p-2 text-sm mb-2"
                                       placeholder="Your account password">

                                <div class="flex gap-2">
                                    <button type="button" @click="open = false"
                                            class="flex-1 text-sm py-2 border border-gray-300 rounded-lg">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="flex-1 text-sm py-2 bg-red-600 text-white rounded-lg">
                                        Delete
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

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
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-semibold text-gray-800">Utang History</h3>

                    @if ($utangs->where('status', 'paid')->isNotEmpty())
                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = true" class="text-xs text-red-600 hover:text-red-700 font-medium">
                                Delete All Paid
                            </button>

                            <div x-show="open" x-cloak
                                 class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                                <div class="bg-white rounded-lg p-4 w-full max-w-xs" @click.outside="open = false">
                                    <form method="POST" action="{{ route('utangs.destroyAll', $customer) }}">
                                        @csrf
                                        @method('DELETE')

                                        <p class="text-sm font-medium text-gray-800 mb-1">Delete all paid utangs?</p>
                                        <p class="text-xs text-gray-500 mb-2">Unpaid or partial utangs will be kept.</p>

                                        <input type="password" name="password" required
                                               class="w-full border-gray-300 rounded-lg p-2 text-sm mb-2"
                                               placeholder="Your account password">

                                        <div class="flex gap-2">
                                            <button type="button" @click="open = false"
                                                    class="flex-1 text-sm py-2 border border-gray-300 rounded-lg">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                    class="flex-1 text-sm py-2 bg-red-600 text-white rounded-lg">
                                                Delete All Paid
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-2">
                    @forelse ($utangs as $utang)
                        <div class="bg-white shadow-sm rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-medium">₱{{ number_format($utang->total_amount, 2) }}</span>
                                    <p class="text-sm text-gray-500">
                                        Paid: ₱{{ number_format($utang->amount_paid, 2) }}
                                        &middot; {{ $utang->created_at->format('M d, Y') }}
                                    </p>
                                    @if ($utang->notes)
                                        <p class="text-sm text-gray-600 mt-1">{{ $utang->notes }}</p>
                                    @endif
                                </div>

                                <div class="flex flex-col items-end gap-2">
                                    <span class="text-xs px-2 py-1 rounded-full
                                        {{ $utang->status === 'paid' ? 'bg-green-100 text-green-700' : ($utang->status === 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ ucfirst($utang->status) }}
                                    </span>

                                    @if ($utang->status === 'paid')
                                        <div x-data="{ open: false }" class="relative">
                                            <button type="button" @click="open = true" class="text-xs text-red-500 hover:text-red-700">
                                                Delete
                                            </button>

                                            <div x-show="open" x-cloak
                                                 class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                                                <div class="bg-white rounded-lg p-4 w-full max-w-xs" @click.outside="open = false">
                                                    <form method="POST" action="{{ route('utangs.destroy', $utang) }}">
                                                        @csrf
                                                        @method('DELETE')

                                                        <p class="text-sm font-medium text-gray-800 mb-2">Confirm your password to delete</p>

                                                        <input type="password" name="password" required
                                                               class="w-full border-gray-300 rounded-lg p-2 text-sm mb-2"
                                                               placeholder="Your account password">

                                                        <div class="flex gap-2">
                                                            <button type="button" @click="open = false"
                                                                    class="flex-1 text-sm py-2 border border-gray-300 rounded-lg">
                                                                Cancel
                                                            </button>
                                                            <button type="submit"
                                                                    class="flex-1 text-sm py-2 bg-red-600 text-white rounded-lg">
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No utang recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-semibold text-gray-800">Payment History</h3>

                    @if ($payments->isNotEmpty())
                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = true" class="text-xs text-red-600 hover:text-red-700 font-medium">
                                Delete All
                            </button>

                            <div x-show="open" x-cloak
                                 class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                                <div class="bg-white rounded-lg p-4 w-full max-w-xs" @click.outside="open = false">
                                    <form method="POST" action="{{ route('payments.destroyAll', $customer) }}">
                                        @csrf
                                        @method('DELETE')

                                        <p class="text-sm font-medium text-gray-800 mb-1">Delete all payments?</p>
                                        <p class="text-xs text-gray-500 mb-2">This removes every payment record for {{ $customer->name }} and recalculates their utang balances.</p>

                                        <input type="password" name="password" required
                                               class="w-full border-gray-300 rounded-lg p-2 text-sm mb-2"
                                               placeholder="Your account password">

                                        <div class="flex gap-2">
                                            <button type="button" @click="open = false"
                                                    class="flex-1 text-sm py-2 border border-gray-300 rounded-lg">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                    class="flex-1 text-sm py-2 bg-red-600 text-white rounded-lg">
                                                Delete All
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

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

                            <div class="flex flex-col items-end gap-2">
                                <span class="text-xs text-gray-400">
                                    {{ $payment->utang_id ? 'Utang #' . $payment->utang_id : 'General credit' }}
                                </span>

                                <div x-data="{ open: false }" class="relative">
                                    <button type="button" @click="open = true" class="text-xs text-red-500 hover:text-red-700">
                                        Delete
                                    </button>

                                    <div x-show="open" x-cloak
                                         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                                        <div class="bg-white rounded-lg p-4 w-full max-w-xs" @click.outside="open = false">
                                            <form method="POST" action="{{ route('payments.destroy', $payment) }}">
                                                @csrf
                                                @method('DELETE')

                                                <p class="text-sm font-medium text-gray-800 mb-2">Confirm your password to delete</p>

                                                <input type="password" name="password" required
                                                       class="w-full border-gray-300 rounded-lg p-2 text-sm mb-2"
                                                       placeholder="Your account password">

                                                <div class="flex gap-2">
                                                    <button type="button" @click="open = false"
                                                            class="flex-1 text-sm py-2 border border-gray-300 rounded-lg">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                            class="flex-1 text-sm py-2 bg-red-600 text-white rounded-lg">
                                                        Delete
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No payments yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>