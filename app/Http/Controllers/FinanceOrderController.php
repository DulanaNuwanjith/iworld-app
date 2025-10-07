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
        $request->validate([
            'item_created_date' => 'required|date',
            'coordinator_name' => 'required|string|max:255',
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
            'rate' => 'required|numeric|min:0',
            'amount_of_installments' => 'required|numeric|min:0',
            'due_payment' => 'required|numeric|min:0',
        ]);

        // Generate sequential order number
        $lastOrder = FinanceOrder::orderBy('id', 'desc')->first();
        $nextNumber = $lastOrder ? ((int) str_replace('FO-', '', $lastOrder->order_number)) + 1 : 1;
        $orderNumber = 'FO-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $data = $request->only([
            'item_created_date', 'coordinator_name', 'buyer_name', 'buyer_id', 'buyer_address',
            'phone_1', 'phone_2', 'item_name', 'emi_number', 'colour',
            'icloud_mail', 'icloud_password', 'screen_lock_password', 'price', 'rate', 'amount_of_installments', 'due_payment'
        ]);

        $data['order_number'] = $orderNumber;

        // Handle file uploads with custom naming
        $fileFields = [
            'id_photo',
            'electricity_bill_photo',
            'photo_1',
            'photo_2',
            'photo_about'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $extension = $request->file($field)->getClientOriginalExtension();
                $fileName = $orderNumber . '_' . $field . '.' . $extension;
                $path = $request->file($field)->storeAs('finance_orders', $fileName, 'public');
                $data[$field] = $path; // save relative path in DB
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

    public function printInvoice($id)
    {
        $order = FinanceOrder::with('payments')->findOrFail($id);
        return view('finance-plc.invoice', compact('order'));
    }

}
