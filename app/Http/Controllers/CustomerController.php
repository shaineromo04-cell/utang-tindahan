<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withSum(['utangs as open_balance' => function ($q) {
                $q->whereIn('status', ['unpaid', 'partial']);
            }], DB::raw('total_amount - amount_paid'))
            ->orderBy('name')
            ->get();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('status', 'Customer added.');
    }

    public function show(Customer $customer)
    {
        $utangs = $customer->utangs()->latest()->get();
        $payments = $customer->payments()->latest()->with('utang')->get();

        return view('customers.show', compact('customer', 'utangs', 'payments'));
    }
}