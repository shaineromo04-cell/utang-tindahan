<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Utang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function destroy(Request $request, Utang $utang)
    {
        $request->validate([
            'password' => ['required'],
        ]);

        if (! Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        if ($utang->balance > 0) {
            return back()->withErrors(['delete' => 'Cannot delete an utang that still has a balance. It must be fully paid first.']);
        }

        $customerId = $utang->customer_id;
        $utang->delete();

        return redirect()
            ->route('customers.show', $customerId)
            ->with('status', 'Utang deleted.');
    }

    public function destroyAll(Request $request, Customer $customer)
    {
        $request->validate([
            'password' => ['required'],
        ]);

        if (! Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $deletable = $customer->utangs()->where('status', 'paid')->get();
        $skippedCount = $customer->utangs()->whereIn('status', ['unpaid', 'partial'])->count();

        foreach ($deletable as $utang) {
            $utang->delete();
        }

        $message = $deletable->count() . ' paid utang(s) deleted.';
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} unpaid/partial utang(s) were kept.";
        }

        return redirect()
            ->route('customers.show', $customer->id)
            ->with('status', $message);
    }
}