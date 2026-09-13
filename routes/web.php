<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UtangController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landpage');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::delete('/customers/{customer}/payments', [PaymentController::class, 'destroyAll'])->name('payments.destroyAll');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('/utangs/create', [UtangController::class, 'create'])->name('utangs.create');
    Route::post('/utangs', [UtangController::class, 'store'])->name('utangs.store');
    Route::delete('/utangs/{utang}', [UtangController::class, 'destroy'])->name('utangs.destroy');
    Route::delete('/customers/{customer}/utangs', [UtangController::class, 'destroyAll'])->name('utangs.destroyAll');

    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
});

require __DIR__.'/auth.php';
