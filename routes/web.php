<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FinanceOrderController;
use App\Http\Controllers\FinanceDashboardController;
use App\Http\Controllers\InventoryController;
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
    Route::post('/finance/{id}/update-buyer-basic', [FinanceOrderController::class, 'updateBuyerBasic'])->name('finance.updateBuyerBasic');
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

    // Finance Dashboard
    Route::get('/dashboard', [FinanceDashboardController::class, 'index'])->name('dashboard');
    // Daily Finance Report web view
    Route::get('/finance/daily-report', [FinanceOrderController::class, 'dailyReport'])
        ->name('finance.dailyReport');
    // Daily Finance Report web view with date range
    Route::get('/finance/report/date-range', [FinanceOrderController::class, 'dateRangeReport'])->name('finance.dateRangeReport');

    // Finance Report page
    Route::get('/financeReport', function () {
        return view('report.financeReport');
    })->name('financeReport.index');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/createInvoice', function () {
        return view('phone-shop.createInvoice');
    })->name('createInvoice.index');

    // Store new phone inventory
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');

    // Display all inventory items
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

    // Delete an item
    Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

    Route::post('/inventory/{inventory}/repair', [InventoryController::class, 'storeRepair'])
    ->name('inventory.repair.store');

    Route::get('/inventory/{inventory}/repairs', [InventoryController::class, 'getRepairs'])
    ->name('inventory.repairs');

});

require __DIR__.'/auth.php';
