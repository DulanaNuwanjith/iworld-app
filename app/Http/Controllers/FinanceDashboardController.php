<?php

namespace App\Http\Controllers;

use App\Models\FinanceOrder;
use Illuminate\Support\Facades\DB;

class FinanceDashboardController extends Controller
{
    public function index()
    {
        // Detect database driver (sqlite or mysql)
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite-compatible query
            $monthlyStats = FinanceOrder::select(
                    DB::raw('strftime("%Y", item_created_date) as year'),
                    DB::raw('strftime("%m", item_created_date) as month'),
                    DB::raw('COUNT(*) as total_customers'),
                    DB::raw('SUM(CAST(price AS FLOAT)) as total_investment'),
                    DB::raw('SUM(CAST(paid_amount_fullamount AS FLOAT)) as total_paid'),
                    DB::raw('SUM(CAST(remaining_amount AS FLOAT)) as total_remaining'),
                    DB::raw('SUM(CAST(over_due_payment_fullamount AS FLOAT)) as total_overdue')
                )
                ->groupBy('year', 'month')
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->get();
        } else {
            // MySQL / MariaDB query
            $monthlyStats = FinanceOrder::select(
                    DB::raw('YEAR(item_created_date) as year'),
                    DB::raw('MONTH(item_created_date) as month'),
                    DB::raw('COUNT(*) as total_customers'),
                    DB::raw('SUM(COALESCE(price,0)) as total_investment'),
                    DB::raw('SUM(COALESCE(paid_amount_fullamount,0)) as total_paid'),
                    DB::raw('SUM(COALESCE(remaining_amount,0)) as total_remaining'),
                    DB::raw('SUM(COALESCE(over_due_payment_fullamount,0)) as total_overdue')
                )
                ->groupBy(DB::raw('YEAR(item_created_date), MONTH(item_created_date)'))
                ->orderBy(DB::raw('YEAR(item_created_date)'), 'desc')
                ->orderBy(DB::raw('MONTH(item_created_date)'), 'desc')
                ->get();
        }

        // --- Overall Totals ---
        $totals = [
            'total_customers'   => FinanceOrder::count(),
            'total_investment'  => (float) FinanceOrder::sum('price'),
            'total_paid'        => (float) FinanceOrder::sum('paid_amount_fullamount'),
            'total_remaining'   => (float) FinanceOrder::sum('remaining_amount'),
            'total_overdue'     => (float) FinanceOrder::sum('over_due_payment_fullamount'),
        ];

        return view('dashboard', compact('monthlyStats', 'totals'));
    }
}
