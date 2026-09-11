<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Utang;
use Illuminate\Http\Request;

class UtangController extends Controller
{
    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $selectedCustomerId = $request->query('customer_id');

        return view('utangs.create', compact('customers', 'selectedCustomerId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $utang = Utang::create([
            'customer_id' => $validated['customer_id'],
            'total_amount' => $validated['total_amount'],
            'amount_paid' => 0,
            'status' => 'unpaid',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('customers.show', $utang->customer_id)
            ->with('status', 'Utang recorded.');
    }
}