<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceOrder;
use App\Models\FinancePayment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\SmsHelper;

class FinanceOrderController extends Controller
{
    public function index(Request $request)
    {
        $financeOrdersQuery = FinanceOrder::query();

        // Filters
        if ($request->filled('order_number')) {
            $financeOrdersQuery->where('order_number', $request->order_number);
        }

        if ($request->filled('buyer_name')) {
            $financeOrdersQuery->where('buyer_name', 'like', '%' . $request->buyer_name . '%');
        }

        if ($request->filled('buyer_id')) {
            $financeOrdersQuery->where('buyer_id', 'like', '%' . $request->buyer_id . '%');
        }

        if ($request->filled('item_created_date')) {
            $financeOrdersQuery->whereDate('item_created_date', $request->item_created_date);
        }

        // ✅ Use filtered query for ordering & pagination
        $financeOrders = $financeOrdersQuery
            ->orderByRaw('
                CASE 
                    WHEN remaining_amount = 0 THEN 1 
                    ELSE 0 
                END ASC, id DESC
            ')
            ->paginate(10)
            ->withQueryString();

        // Dropdown data
        $orderNumbers = FinanceOrder::select('order_number')->distinct()->pluck('order_number');
        $buyerNames = FinanceOrder::select('buyer_name')->distinct()->pluck('buyer_name');
        $buyerIds = FinanceOrder::select('buyer_id')->distinct()->pluck('buyer_id');

        return view('finance-plc.finance', compact('financeOrders', 'orderNumbers', 'buyerNames', 'buyerIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_created_date' => 'required|date',
            'coordinator_name' => 'nullable|string|max:255',
            'buyer_name' => 'required|string|max:255',
            'buyer_id' => 'required|string|max:255',
            'buyer_address' => 'required|string',
            'phone_1' => [
                'required',
                'string',
                'max:20',
                'regex:/^(07\d{8}|94\d{9}|03\d{8})$/'
            ],
            'phone_2' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(07\d{8}|94\d{9}|03\d{8})$/'
            ],
            'item_name' => 'required|string|max:255',
            'emi_number' => 'required|string|max:255',
            'colour' => 'required|string|max:255',
            'icloud_mail' => 'required|email',
            'icloud_password' => 'required|string|max:255',
            'screen_lock_password' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'amount_of_installments' => 'required|integer|min:1',
            'over_due_payment_fullamount' => 'nullable|numeric|min:0',
            'paid_amount_fullamount' => 'nullable|numeric|min:0',
            'remaining_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ], [
            'phone_1.regex' => 'Phone 1 must be 077XXXXXXX, 947XXXXXXX, or 03XXXXXXXX format only.',
            'phone_2.regex' => 'Phone 2 must be 077XXXXXXX, 947XXXXXXX, or 03XXXXXXXX format only.',
        ]);

        // ✅ Calculate due payment = price + (price * rate / 100)
        $price = $validated['price'];
        $rate = $validated['rate'];
        $due = $price + ($price * $rate / 100);
        $validated['due_payment'] = $due;

        // ✅ Auto-generate order number
        $maxNumber = FinanceOrder::selectRaw("MAX(CAST(SUBSTR(order_number, 4) AS INTEGER)) as max_number")
            ->value('max_number');

        $nextNumber = $maxNumber ? $maxNumber + 1 : 1;
        $validated['order_number'] = 'FO-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // ✅ Create FinanceOrder
        $order = FinanceOrder::create($validated);

        // ✅ Create FinancePayment records (amount_of_installments wise)
        $installmentAmount = round($due / $validated['amount_of_installments'], 2);
        $startDate = Carbon::parse($validated['item_created_date']);

        for ($i = 1; $i <= $validated['amount_of_installments']; $i++) {
            // 1st installment: same as item_created_date
            // 2nd installment: +30 days, 3rd: +60 days, ...
            $expectedDate = $startDate->copy()->addDays(30 * ($i - 1));

            // Adjust last installment for rounding differences
            if ($i == $validated['amount_of_installments']) {
                $installmentAmount = $due - $installmentAmount * ($validated['amount_of_installments'] - 1);
            }

            FinancePayment::create([
                'finance_order_id' => $order->id,
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'expected_date' => $expectedDate,
            ]);
        }


        return redirect()->back()->with('success', 'Finance order and installments created successfully.');
    }

    public function updateBuyerBasic(Request $request, $id)
    {
        $order = FinanceOrder::findOrFail($id);

        $validated = $request->validate([
            'buyer_address' => 'required|string',
            'phone_1' => [
                'required',
                'string',
                'max:20',
                'regex:/^(07\d{8}|94\d{9}|03\d{8})$/'
            ],
            'phone_2' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(07\d{8}|94\d{9}|03\d{8})$/'
            ],
        ], [
            'phone_1.regex' => 'Phone 1 must be 077XXXXXXX, 947XXXXXXX, or 03XXXXXXXX format only.',
            'phone_2.regex' => 'Phone 2 must be 077XXXXXXX, 947XXXXXXX, or 03XXXXXXXX format only.',
        ]);

        $order->update($validated);

        return redirect()->back()->with('success', 'Buyer details updated successfully.');
    }

    /**
     * Update finance order note
     */
    public function updateNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:5000',
        ]);

        $order = FinanceOrder::findOrFail($id);
        $order->note = $request->note;
        $order->save();

        return redirect()->back()->with('success', 'Note updated successfully.');
    }

    // Delete a finance order
    public function destroy($id)
    {
        $order = FinanceOrder::findOrFail($id);

        // Delete uploaded files
        $fileFields = ['id_photo', 'electricity_bill_photo', 'photo_1', 'photo_2', 'photo_about'];
        foreach ($fileFields as $field) {
            if ($order->$field && Storage::disk('public')->exists($order->$field)) {
                Storage::disk('public')->delete($order->$field);
            }
        }

        $order->delete();

        return redirect()->route('finance.index')->with('success', 'Finance Order Deleted Successfully!');
    }

    public function printInvoice($id)
    {
        $order = FinanceOrder::with('payments')->findOrFail($id);
        return view('finance-plc.invoice', compact('order'));
    }

    /**
     * Fetch installment list for modal popup
     */
    public function getInstallments($orderId)
    {
        $order = FinanceOrder::with(['payments' => function($q) {
            $q->orderBy('installment_number');
        }])->findOrFail($orderId);

        return view('finance-plc.modal-installments', compact('order'));
    }

    public function payInstallment(Request $request, $id)
    {
        $payment = FinancePayment::findOrFail($id);
        $order = FinanceOrder::findOrFail($payment->finance_order_id);

        $overdueChargePerDay = 200;
        $overdueDays = (int) ($request->overdue_days ?? 0);
        $overdueAmount = $overdueDays * $overdueChargePerDay;

        // 🔒 Prevent paying future installments if previous unpaid
        if ($payment->installment_number > 1) {
            $previous = FinancePayment::where('finance_order_id', $payment->finance_order_id)
                ->where('installment_number', $payment->installment_number - 1)
                ->first();

            if ($previous && !$previous->paid_at) {
                return redirect()->back()->with('error', "You must pay installment #{$previous->installment_number} first.");
            }
        }

        if ($payment->paid_at) {
            return redirect()->back()->with('error', 'Installment already paid.');
        }

        // 💰 Get all payments
        $payments = $order->payments()->orderBy('installment_number')->get();
        $lastUnpaid = $payments->whereNull('paid_at')->last();

        // 🧮 Determine amount to pay
        if ($payment->id === optional($lastUnpaid)->id) {
            // ✅ Last unpaid installment → must pay full remaining balance
            $totalPaidSoFar = $payments->whereNotNull('paid_at')->sum('paid_amount');
            $totalOverduePaid = $payments->whereNotNull('paid_at')->sum('overdue_amount');

            $remainingBalance = $order->due_payment - ($totalPaidSoFar - $totalOverduePaid);

            $unpaidOverdue = $payments->whereNull('paid_at')
                ->where('id', '<>', $payment->id)
                ->sum('overdue_amount');

            // total remaining full amount to close the order
            $amountToPay = max($remainingBalance + $unpaidOverdue + $overdueAmount, 0);

            $minAmount = $amountToPay; // enforce full payment
        } else {
            // ✅ Other installments → can pay any positive amount (partial allowed)
            $amountToPay = $payment->amount + $overdueAmount;
            $minAmount = 1; // only requires >0
        }

        // ✅ Validation
        $request->validate([
            'paid_amount' => "required|numeric|min:$minAmount",
            'overdue_days' => 'nullable|integer|min:0',
        ]);

        // 💾 Save payment
        $payment->update([
            'paid_at' => now(),
            'paid_amount' => $request->paid_amount,
            'overdue_days' => $overdueDays,
            'overdue_amount' => $overdueAmount,
        ]);

        // 🔄 Update totals
        $totalOverdue = $order->payments()->sum('overdue_amount');
        $totalPaidFull = $order->payments()->sum('paid_amount');
        $paidInitialAmount = $totalPaidFull - $totalOverdue;
        $remainingAmount = $order->due_payment - $paidInitialAmount;

        // 🔄 Update totals
        $order->update([
            'over_due_payment_fullamount' => $totalOverdue,
            'paid_amount_fullamount' => $totalPaidFull,
            'remaining_amount' => max($remainingAmount, 0),
        ]);

        // 🧹 Remove future unpaid installments if fully paid
        if ($remainingAmount <= 0) {
            FinancePayment::where('finance_order_id', $order->id)
                ->whereNull('paid_at')
                ->delete();
        }

        /* ===============================
        📩 SEND PAYMENT SUCCESS SMS
        =============================== */
        try {
            if ($order->phone_1) {
                $phone = preg_replace('/\D/', '', $order->phone_1);

                if (str_starts_with($phone, '0')) {
                    $phone = '94' . substr($phone, 1);
                }

                if (preg_match('/^94\d{9}$/', $phone)) {

                    $amount = number_format($request->paid_amount, 2);
                    $date   = now()->format('d M Y');

                   $message = "Dear {$order->buyer_name},\n\n"
                        . "We confirm receipt of your payment of Rs. {$amount} "
                        . "for installment #{$payment->installment_number} on {$date}.\n\n"
                        . "Thank you for your prompt payment.\n\n"
                        . "For inquiries:\n"
                        . "Tel: 076 411 28 49 | 077 20 87 649\n\n"
                        . "Iworld Finance";

                    SmsHelper::sendSms($phone, $message);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Payment SMS failed', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }

        // 🔚 Redirect response
        return redirect()->back()->with(
            'success',
            "Installment #{$payment->installment_number} paid successfully. Totals updated."
        );
    }

    public function nearestPayments(Request $request)
    {
        $allOrders = FinanceOrder::with(['payments' => function ($query) {
            $query->whereNull('paid_at')->orderBy('expected_date', 'asc');
        }])->get()
        ->filter(fn($order) => $order->payments->isNotEmpty())
        ->sortBy(fn($order) => $order->payments->first()->expected_date);

        $perPage = 15;
        $page = $request->get('page', 1);
        $total = $allOrders->count();
        $results = $allOrders->slice(($page - 1) * $perPage, $perPage)->values();

        $financeOrders = new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('finance-plc.nearestPayments', compact('financeOrders'));
    }

    public function dailyReport(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $newOrders = FinanceOrder::whereDate('item_created_date', $date)->get();
        $payments = FinancePayment::with('financeOrder')
            ->whereDate('paid_at', $date)
            ->get();

        $totalOrders = $newOrders->count();
        $totalPayments = $payments->count();
        $totalPaidAmount = $payments->sum('paid_amount');
        $totalOverdueCollected = $payments->sum('overdue_amount');
        $totalIncome = $totalPaidAmount + $totalOverdueCollected;
        $remainingBalance = FinanceOrder::whereDate('item_created_date', '<=', $date)->sum('remaining_amount');
        $totalInvestment = FinanceOrder::whereDate('item_created_date', $date)->sum('price');

        // Total Profit = sum of (due_payment - price) for new orders
        $totalProfit = $newOrders->sum(function($order) {
            return $order->due_payment - $order->price;
        });

        return view('finance-plc.daily-report', compact(
            'date',
            'newOrders',
            'payments',
            'totalOrders',
            'totalPayments',
            'totalPaidAmount',
            'totalOverdueCollected',
            'totalIncome',
            'remainingBalance',
            'totalInvestment',
            'totalProfit'
        ));
    }

    public function dateRangeReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $newOrders = FinanceOrder::whereBetween('item_created_date', [$startDate, $endDate])->get();
        $payments = FinancePayment::with('financeOrder')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->get();

        $totalOrders = $newOrders->count();
        $totalPayments = $payments->count();
        $totalPaidAmount = $payments->sum('paid_amount');
        $totalOverdueCollected = $payments->sum('overdue_amount');
        $totalIncome = $totalPaidAmount + $totalOverdueCollected;

        $remainingBalance = FinanceOrder::whereDate('item_created_date', '<=', $endDate)->sum('remaining_amount');
        $totalInvestment = $newOrders->sum('price');
        $totalProfit = $newOrders->sum(function($order) {
            return $order->due_payment - $order->price;
        });

        return view('report.templates.financeReportDateRange', compact(
            'startDate',
            'endDate',
            'newOrders',
            'payments',
            'totalOrders',
            'totalPayments',
            'totalPaidAmount',
            'totalOverdueCollected',
            'totalIncome',
            'remainingBalance',
            'totalInvestment',
            'totalProfit'
        ));
    }

    public function settledPayments(Request $request)
    {
        $query = FinanceOrder::where('remaining_amount', 0);

        // Filters
        if ($request->filled('order_number')) {
            $query->where('order_number', $request->order_number);
        }

        if ($request->filled('buyer_name')) {
            $query->where('buyer_name', 'like', '%' . $request->buyer_name . '%');
        }

        if ($request->filled('buyer_id')) {
            $query->where('buyer_id', 'like', '%' . $request->buyer_id . '%');
        }

        $financeOrders = $query
            ->orderBy('item_created_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Dropdown data (same as Finance tab)
        $orderNumbers = FinanceOrder::where('remaining_amount', 0)
            ->select('order_number')->distinct()->pluck('order_number');

        $buyerNames = FinanceOrder::where('remaining_amount', 0)
            ->select('buyer_name')->distinct()->pluck('buyer_name');

        $buyerIds = FinanceOrder::where('remaining_amount', 0)
            ->select('buyer_id')->distinct()->pluck('buyer_id');

        return view('finance-plc.settledPayments', compact(
            'financeOrders',
            'orderNumbers',
            'buyerNames',
            'buyerIds'
        ));
    }

}
