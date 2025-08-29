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

Route::get('finance', function () {
        return view('finance-plc.finance');
    })->name('finance.index');

Route::get('nearestPayments', function () {
        return view('finance-plc.nearestPayments');
    })->name('nearestPayments.index');

Route::get('financeReport', function () {
        return view('report.financeReport');
    })->name('financeReport.index');

Route::get('/finance', [FinanceOrderController::class, 'index'])->name('finance.index');
Route::post('/finance/store', [FinanceOrderController::class, 'store'])->name('financeOrders.store');
Route::delete('/finance/{id}', [FinanceOrderController::class, 'destroy'])->name('finance.destroy');
Route::patch('finance-orders/{id}/update-note', [FinanceOrderController::class, 'updateNote'])->name('finance.update-note');
Route::patch('/finance/payInstallment/{order}/{installment}', [FinanceOrderController::class, 'payInstallment'])
    ->name('finance.payInstallment');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
