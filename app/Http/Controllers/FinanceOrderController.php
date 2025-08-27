<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceOrder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FinanceOrderController extends Controller
{
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

        // Generate unique order number
        $orderNumber = 'FO-' . Str::upper(Str::random(6));

        // Handle file uploads
        $uploads = ['id_photo','electricity_bill_photo','photo_1','photo_2','photo_about'];
        foreach($uploads as $fileField){
            if($request->hasFile($fileField)){
                $request[$fileField] = $request->file($fileField)->store('finance_orders', 'public');
            } else {
                $request[$fileField] = null;
            }
        }

        $data = $request->all();
        $data['order_number'] = $orderNumber;

        FinanceOrder::create($data);

        return back()->with('success', 'Finance Order Created Successfully!');
    }
}
