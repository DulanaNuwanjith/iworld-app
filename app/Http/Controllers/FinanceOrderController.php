<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceOrder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FinanceOrderController extends Controller
{
    public function index()
    {
        // Fetch all finance orders from the database
        $financeOrders = FinanceOrder::all();

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
        ]);

        $orderNumber = 'FO-' . Str::upper(Str::random(6));

        $data = $request->only([
            'item_created_date', 'buyer_name', 'buyer_id', 'buyer_address',
            'phone_1', 'phone_2', 'item_name', 'emi_number', 'colour',
            'icloud_mail', 'icloud_password', 'screen_lock_password'
        ]);

        $data['order_number'] = $orderNumber;

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

    // Show the edit form
    public function edit($id)
    {
        $order = FinanceOrder::findOrFail($id);
        return view('finance-plc.edit', compact('order'));
    }

    // Update a finance order
    public function update(Request $request, $id)
    {
        $order = FinanceOrder::findOrFail($id);

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
        ]);

        $data = $request->only([
            'item_created_date', 'buyer_name', 'buyer_id', 'buyer_address',
            'phone_1', 'phone_2', 'item_name', 'emi_number', 'colour',
            'icloud_mail', 'icloud_password', 'screen_lock_password'
        ]);

        // Handle file uploads and delete old files
        $fileFields = ['id_photo', 'electricity_bill_photo', 'photo_1', 'photo_2', 'photo_about'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                if ($order->$field) {
                    Storage::disk('public')->delete($order->$field);
                }
                $data[$field] = $request->file($field)->store('finance_orders', 'public');
            } else {
                $data[$field] = $order->$field;
            }
        }

        $order->update($data);

        return redirect()->route('finance.index')->with('success', 'Finance Order Updated Successfully!');
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

}
