<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FinanceOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Finance pages
    Route::get('/finance', [FinanceOrderController::class, 'index'])->name('finance.index');
    Route::post('/finance/store', [FinanceOrderController::class, 'store'])->name('financeOrders.store');
    Route::delete('/finance/{id}', [FinanceOrderController::class, 'destroy'])->name('finance.destroy');
    Route::patch('finance-orders/{id}/update-note', [FinanceOrderController::class, 'updateNote'])->name('finance.update-note');
    Route::get('/finance/nearest-payments', [FinanceOrderController::class, 'nearestPayments'])
        ->name('finance.nearestPayments');
    Route::get('/finance/invoice/{id}', [FinanceOrderController::class, 'printInvoice'])->name('finance.print-invoice');
    // Installment modal & payment
    Route::get('/finance/{id}/installments', [FinanceOrderController::class, 'getInstallments'])
        ->name('finance.installments');
    Route::post('/finance/payment/{id}/pay', [FinanceOrderController::class, 'payInstallment'])
        ->name('finance.pay.installment');
    Route::get('/finance/nearest-payments', [FinanceOrderController::class, 'nearestPayments'])->name('finance.nearestPayments');


    // Finance Report page
    Route::get('/financeReport', function () {
        return view('report.financeReport');
    })->name('financeReport.index');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
