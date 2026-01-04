<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FinanceOrderController;
use App\Http\Controllers\FinanceDashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PhoneInventoryController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('auth.login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard (EVERYONE)
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [FinanceDashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | FINANCE (SUPERADMIN | ADMIN | FINANCECOORDINATOR | FINANCEMONITOR)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:SUPERADMIN,ADMIN,FINANCECOORDINATOR,FINANCEMONITOR'])->group(function () {

        Route::get('/finance', [FinanceOrderController::class, 'index'])
            ->name('finance.index');

        Route::post('/finance/store', [FinanceOrderController::class, 'store'])
            ->name('financeOrders.store');

        Route::post('/finance/{id}/update-buyer-basic', [FinanceOrderController::class, 'updateBuyerBasic'])
            ->name('finance.updateBuyerBasic');

        Route::patch('/finance-orders/{id}/update-note', [FinanceOrderController::class, 'updateNote'])
            ->name('finance.update-note');

        Route::get('/finance/invoice/{id}', [FinanceOrderController::class, 'printInvoice'])
            ->name('finance.print-invoice');

        Route::get('/finance/{id}/installments', [FinanceOrderController::class, 'getInstallments'])
            ->name('finance.installments');

        Route::post('/finance/payment/{id}/pay', [FinanceOrderController::class, 'payInstallment'])
            ->name('finance.pay.installment');

        Route::get('/finance/nearest-payments', [FinanceOrderController::class, 'nearestPayments'])
            ->name('finance.nearestPayments');

        Route::delete('/finance/{id}', [FinanceOrderController::class, 'destroy'])
            ->name('finance.destroy');

        // Settled Payments Route
        Route::get('/settledPayments', function () {
            return view('finance-plc.settledPayments');
        })->name('settledPayments.index');
    });

    /*
    |--------------------------------------------------------------------------
    | INVENTORY (SUPERADMIN | ADMIN ONLY)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:SUPERADMIN,ADMIN'])->group(function () {

        Route::get('/inventory', [InventoryController::class, 'index'])
            ->name('inventory.index');

        Route::post('/inventory', [InventoryController::class, 'store'])
            ->name('inventory.store');

        Route::post('/inventory/{inventory}/repair', [InventoryController::class, 'storeRepair'])
            ->name('inventory.repair.store');

        Route::get('/inventory/{inventory}/repairs', [InventoryController::class, 'getRepairs'])
            ->name('inventory.repairs');

        Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])
            ->name('inventory.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | INVENTORY (SOLD / EXCHANGE)
    | SUPERADMIN | ADMIN | PHONESHOPOPERATOR
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:SUPERADMIN,ADMIN,PHONESHOPOPERATOR'])->group(function () {

        Route::get('/inventory/sold', [InventoryController::class, 'sold'])
            ->name('inventory.sold');

        Route::post('/inventory/exchange', [InventoryController::class, 'exchange'])
            ->name('inventory.exchange');
    });

    /*
    |--------------------------------------------------------------------------
    | INVOICES (SUPERADMIN | ADMIN ONLY)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:SUPERADMIN,ADMIN,PHONESHOPOPERATOR'])->group(function () {

        Route::get('/invoices', [InvoiceController::class, 'index'])
            ->name('invoices.index');

        Route::post('/invoices/store', [InvoiceController::class, 'store'])
            ->name('invoices.store');

        Route::get('/phone-inventory/{emi}', [PhoneInventoryController::class, 'getByEmi']);

        Route::get('/phone-shop/invoice-print/{id}', [InvoiceController::class, 'printInvoice'])
            ->name('phone-shop.invoice-print');

        Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])
            ->name('invoices.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | REPORTS (SUPERADMIN | ADMIN ONLY)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:SUPERADMIN,ADMIN'])->group(function () {

        Route::get('/phoneShopReport', function () {
            return view('report.phoneShopReport');
        })->name('phoneShopReport.index');

        Route::get('/financeReport', function () {
            return view('report.financeReport');
        })->name('financeReport.index');

        Route::get('/repairs/report', [InventoryController::class, 'generateRepairsReport'])
            ->name('repairs.dateRangeReport');

        Route::get('/reports/sales', [InvoiceController::class, 'salesReportForm'])
            ->name('sales.reportForm');

        Route::get('/reports/sales/generate', [InvoiceController::class, 'generateSalesReport'])
            ->name('sales.dateRangeReport');

        Route::get('/finance/daily-report', [FinanceOrderController::class, 'dailyReport'])
            ->name('finance.dailyReport');

        Route::get('/finance/report/date-range', [FinanceOrderController::class, 'dateRangeReport'])
            ->name('finance.dateRangeReport');
    });

    /*
    |--------------------------------------------------------------------------
    | PROFILE (EVERYONE)
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
