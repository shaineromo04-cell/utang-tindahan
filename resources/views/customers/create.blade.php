<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Customer</h2>
    </x-slot>

    <div class="py-6">
    <div class="max-w-md mx-auto px-4">
        <a href="{{ route('customers.index') }}" class="inline-flex items-center text-sm text-gray-600 mb-4">
            ← Back to Customers
        </a>

        <form method="POST" action="{{ route('customers.store') }}" class="bg-white shadow-sm rounded-lg p-4 space-y-4">

    <div class="py-6">
        <div class="max-w-md mx-auto px-4">
            <form method="POST" action="{{ route('customers.store') }}" class="bg-white shadow-sm rounded-lg p-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full border-gray-300 rounded-lg p-3">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium">
                    Save
                </button>
            </form>
        </div>
    </div>
</x-app-layout>