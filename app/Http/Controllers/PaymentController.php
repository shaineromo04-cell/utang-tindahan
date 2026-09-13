<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Utang;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function destroy(Request $request, Payment $payment)
    {
        $request->validate([
            'password' => ['required'],
        ]);

        if (! Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $customerId = $payment->customer_id;

        // If this payment was applied to a specific utang, reverse it out
        // so the utang's amount_paid/status stay accurate.
        if ($payment->utang_id) {
            $utang = Utang::find($payment->utang_id);

            if ($utang) {
                $utang->amount_paid -= $payment->amount;

                if ($utang->amount_paid < 0) {
                    $utang->amount_paid = 0;
                }

                $utang->refreshStatus();
            }
        }

        $payment->delete();

        return redirect()
            ->route('customers.show', $customerId)
            ->with('status', 'Payment deleted.');
    }

    public function destroyAll(Request $request, Customer $customer)
{
    $request->validate([
        'password' => ['required'],
    ]);

    if (! Hash::check($request->password, Auth::user()->password)) {
        return back()->withErrors(['password' => 'Incorrect password.']);
    }

    $payments = $customer->payments()->get();

    // Reverse each payment's effect on its utang before deleting,
    // so no utang is left showing a paid amount that no longer has
    // a matching payment record.
    foreach ($payments as $payment) {
        if ($payment->utang_id) {
            $utang = Utang::find($payment->utang_id);

            if ($utang) {
                $utang->amount_paid -= $payment->amount;

                if ($utang->amount_paid < 0) {
                    $utang->amount_paid = 0;
                }

                $utang->refreshStatus();
            }
        }
    }

    $customer->payments()->delete();

    return redirect()
        ->route('customers.show', $customer->id)
        ->with('status', 'All payments deleted.');
    }
}