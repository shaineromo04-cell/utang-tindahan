<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Utang;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $selectedCustomerId = $request->query('customer_id');

        $openUtangs = collect();
        if ($selectedCustomerId) {
            $customer = Customer::find($selectedCustomerId);
            $openUtangs = $customer?->openUtangs() ?? collect();
        }

        return view('payments.create', compact('customers', 'selectedCustomerId', 'openUtangs'));
    }

    public function store(Request $request, PaymentService $paymentService)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'utang_id' => ['nullable', 'exists:utangs,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,gcash,other'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $utang = isset($validated['utang_id']) ? Utang::find($validated['utang_id']) : null;

        $paymentService->recordPayment(
            customer: $customer,
            amount: (float) $validated['amount'],
            utang: $utang,
            method: $validated['payment_method'],
            notes: $validated['notes'] ?? null,
        );

        return redirect()
            ->route('customers.show', $customer->id)
            ->with('status', 'Payment recorded.');
    }
}