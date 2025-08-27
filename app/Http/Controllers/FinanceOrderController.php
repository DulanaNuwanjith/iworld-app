<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceOrder;
use App\Models\FinancePayment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FinanceOrderController extends Controller
{
    public function index()
    {
        // Fetch all finance orders from the database
        $financeOrders = FinanceOrder::paginate(10);

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

        // 🔹 Generate sequential order number
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
        ]);

        // Create or update payment record for this installment
        $payment = FinancePayment::firstOrNew([
            'finance_order_id' => $order->id,
            'installment_number' => $installmentNumber,
        ]);

        // Add the new amount to existing payment
        $payment->amount = ($payment->amount ?? 0) + $request->amount;
        $payment->paid_at = now();
        $payment->save();

        return redirect()->back()->with('success', "Payment added to Installment {$installmentNumber}!");
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

}
