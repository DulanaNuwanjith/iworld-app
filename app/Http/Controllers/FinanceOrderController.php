<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceOrder;
use App\Models\FinancePayment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceOrderController extends Controller
{
    public function index()
    {
        // Fetch all finance orders from the database
        $financeOrders = FinanceOrder::orderBy('id', 'desc')->paginate(10);

        // Pass it to the view
        return view('finance-plc.finance', compact('financeOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_created_date' => 'required|date',
            'buyer_name' => 'required|string|max:255',
            'buyer_id' => 'required|string|max:255',
            'buyer_address' => 'required|string',
            'phone_1' => 'required|string|max:20',
            'phone_2' => 'nullable|string|max:20',
            'id_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'electricity_bill_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'item_name' => 'required|string|max:255',
            'emi_number' => 'required|string|max:255',
            'colour' => 'required|string|max:255',
            'photo_1' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'photo_2' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'photo_about' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'icloud_mail' => 'required|email',
            'icloud_password' => 'required|string|max:255',
            'screen_lock_password' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        // Generate sequential order number correctly
        $lastOrder = FinanceOrder::orderBy('id', 'desc')->first();
        $nextNumber = $lastOrder ? ((int) str_replace('FO-', '', $lastOrder->order_number)) + 1 : 1;
        $orderNumber = 'FO-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $data = $request->only([
            'item_created_date', 'buyer_name', 'buyer_id', 'buyer_address',
            'phone_1', 'phone_2', 'item_name', 'emi_number', 'colour',
            'icloud_mail', 'icloud_password', 'screen_lock_password', 'price'
        ]);

        $data['order_number'] = $orderNumber;

        // Format price
        $data['price'] = $request->price;

        // Handle file uploads
        $fileFields = ['id_photo', 'electricity_bill_photo', 'photo_1', 'photo_2', 'photo_about'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('finance_orders', 'public');
            } else {
                $data[$field] = null;
            }
        }

        FinanceOrder::create($data);

        return redirect()->route('finance.index')->with('success', 'Finance Order Created Successfully!');
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

    /**
     * Pay a specific installment for a finance order
     */
    public function payInstallment(Request $request, $orderId, $installmentNumber)
    {
        $order = FinanceOrder::findOrFail($orderId);

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'overdue_days' => 'nullable|integer|min:0',
        ]);

        // Calculate overdue charges
        $overdueAmount = 0;
        if (($installmentNumber == 2 || $installmentNumber == 3) && $request->overdue_days) {
            $rate = $installmentNumber == 2 ? 200 : 500;
            $overdueAmount = $rate * $request->overdue_days;
        }

        // Total payment including overdue
        $totalPayment = $request->amount + $overdueAmount;

        // Create or update current payment
        $payment = FinancePayment::firstOrNew([
            'finance_order_id' => $order->id,
            'installment_number' => $installmentNumber,
        ]);

        $payment->amount = ($payment->amount ?? 0) + $totalPayment;
        $payment->overdue_days = $request->overdue_days ?? 0;
        $payment->overdue_amount = $overdueAmount;
        $payment->paid_at = now();
        $payment->save();

        // 🔹 Set expected dates for future installments after first payment
        if ($installmentNumber == 1) {
            $firstPaymentDate = $payment->paid_at;

            // Installment 2 expected date = 30 days after first payment
            $payment2 = FinancePayment::firstOrNew([
                'finance_order_id' => $order->id,
                'installment_number' => 2,
            ]);
            $payment2->expected_date = Carbon::parse($firstPaymentDate)->addDays(30);
            $payment2->amount = $payment2->amount ?? 0;
            $payment2->save();

            // Installment 3 expected date = 60 days after first payment
            $payment3 = FinancePayment::firstOrNew([
                'finance_order_id' => $order->id,
                'installment_number' => 3,
            ]);
            $payment3->expected_date = Carbon::parse($firstPaymentDate)->addDays(60);
            $payment3->amount = $payment3->amount ?? 0;
            $payment3->save();
        }

        return redirect()->back()->with(
            'success',
            "Payment {$installmentNumber} completed! Total paid: LKR {$totalPayment}"
        );
    }

    public function remainingBalance($orderId)
    {
        $order = FinanceOrder::findOrFail($orderId);
        $paidAmount = $order->payments()->whereNotNull('paid_at')->sum('amount');
        return $order->price - $paidAmount;
    }

    /**
     * Optional: Show all installments for a finance order
     */
    public function showPayments($orderId)
    {
        $order = FinanceOrder::findOrFail($orderId);
        $payments = $order->payments()->orderBy('installment_number')->get();

        return view('finance.payments.index', compact('order', 'payments'));
    }

    public function nearestPayments()
    {
        // Get finance orders that have at least one unpaid payment
        $financeOrders = FinanceOrder::select('finance_orders.*')
            ->join('finance_payments', function($join) {
                $join->on('finance_payments.finance_order_id', '=', 'finance_orders.id')
                    ->whereNull('finance_payments.paid_at');
            })
            ->with(['payments' => function($query) {
                $query->whereNull('paid_at')->orderBy('installment_number');
            }])
            ->orderBy('finance_payments.expected_date', 'asc') // earliest unpaid date first
            ->distinct()
            ->paginate(20);

        return view('finance-plc.nearestPayments', compact('financeOrders'));
    }


}
