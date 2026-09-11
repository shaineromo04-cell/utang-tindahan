<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Utang;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCustomers = Customer::count();

        $totalOutstanding = Utang::whereIn('status', ['unpaid', 'partial'])
            ->sum(DB::raw('total_amount - amount_paid'));

        $collectedToday = Payment::whereDate('created_at', today())->sum('amount');

        $collectedThisMonth = Payment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $topDebtors = Customer::withSum(['utangs as open_balance' => function ($q) {
        $q->whereIn('status', ['unpaid', 'partial']);
            }], DB::raw('total_amount - amount_paid'))
            ->get()
            ->filter(fn ($customer) => $customer->open_balance > 0)
            ->sortByDesc('open_balance')
            ->take(5)
            ->values();

        $recentActivity = Payment::with('customer')
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'totalCustomers',
            'totalOutstanding',
            'collectedToday',
            'collectedThisMonth',
            'topDebtors',
            'recentActivity'
        ));
    }
}