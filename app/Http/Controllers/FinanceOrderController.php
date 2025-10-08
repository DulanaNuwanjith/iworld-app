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

        // Pagination with filters
        $financeOrders = $financeOrdersQuery->orderBy('id', 'desc')
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
            'phone_1' => 'required|string|max:20',
            'phone_2' => 'nullable|string|max:20',
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

        // Prevent paying future installments if previous unpaid
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

        // Get all payments
        $payments = $order->payments()->orderBy('installment_number')->get();
        $lastPayment = $payments->whereNull('paid_at')->last();

        // Determine amount to pay
        if ($payment->id === optional($lastPayment)->id) {
            // Last unpaid installment
            $totalPaidSoFar = $payments->whereNotNull('paid_at')->sum('paid_amount');
            $totalOverduePaid = $payments->whereNotNull('paid_at')->sum('overdue_amount');

            $remainingBalance = $order->due_payment - ($totalPaidSoFar - $totalOverduePaid);

            $unpaidOverdue = $payments->whereNull('paid_at')
                ->where('id', '<>', $payment->id)
                ->sum('overdue_amount');

            $amountToPay = max($remainingBalance + $unpaidOverdue + $overdueAmount, 0);
        } else {
            $amountToPay = $payment->amount + $overdueAmount;
        }

        // Validation
        $request->validate([
            'paid_amount' => "required|numeric|min:$amountToPay",
            'overdue_days' => 'nullable|integer|min:0',
        ]);

        // Save payment
        $payment->paid_at = now();
        $payment->paid_amount = $request->paid_amount;
        $payment->overdue_days = $overdueDays;
        $payment->overdue_amount = $overdueAmount;
        $payment->save();

        // Update totals
        $totalOverdue = $order->payments()->sum('overdue_amount');
        $totalPaidFull = $order->payments()->sum('paid_amount');
        $paidInitialAmount = $totalPaidFull - $totalOverdue;
        $remainingAmount = $order->due_payment - $paidInitialAmount;

        $order->over_due_payment_fullamount = $totalOverdue;
        $order->paid_amount_fullamount = $totalPaidFull;
        $order->remaining_amount = max($remainingAmount, 0);
        $order->save();

        // Remove future unpaid installments if fully paid
        if ($remainingAmount <= 0) {
            FinancePayment::where('finance_order_id', $order->id)
                ->whereNull('paid_at')
                ->delete();
        }

        return redirect()->back()->with('success', "Installment #{$payment->installment_number} successfully paid. Totals updated.");
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

}
