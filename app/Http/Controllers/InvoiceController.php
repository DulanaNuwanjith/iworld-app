<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PhoneInventory;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // Show all invoices
    public function index()
    {
        $invoices = Invoice::latest()->get(); // get all invoices
        $emis = PhoneInventory::select('emi', 'phone_type', 'colour', 'capacity')->get();
        return view('phone-shop.createInvoice', compact('invoices', 'emis'));
    }

    // Show create form (if separate page needed)
    public function create()
    {
        $emis = PhoneInventory::select('emi', 'phone_type', 'colour', 'capacity')->get();
        return view('phone-shop.createInvoice', compact('emis'));
    }

    // Store a new invoice
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'emi' => 'required|string|exists:phone_inventories,emi',
            'selling_price' => 'nullable|numeric|min:0',
            'tempered' => 'nullable|numeric|min:0',
            'back_cover' => 'nullable|numeric|min:0',
            'charger' => 'nullable|numeric|min:0',
            'data_cable' => 'nullable|numeric|min:0',
            'hand_free' => 'nullable|numeric|min:0',
            'airpods' => 'nullable|numeric|min:0',
            'power_bank' => 'nullable|numeric|min:0',
        ]);

        $invoice = new Invoice($request->all());

        // Fill phone details from PhoneInventory
        $phone = PhoneInventory::where('emi', $request->emi)->first();
        if ($phone) {
            $invoice->phone_type = $phone->phone_type;
            $invoice->colour = $phone->colour;
            $invoice->capacity = $phone->capacity;
        }

        // Generate unique invoice number
        $invoice->invoice_number = 'INV-' . time() . rand(100, 999);

        // Calculate total amount
        $invoice->total_amount = (
            ($invoice->selling_price ?? 0) +
            ($invoice->tempered ?? 0) +
            ($invoice->back_cover ?? 0) +
            ($invoice->charger ?? 0) +
            ($invoice->data_cable ?? 0) +
            ($invoice->hand_free ?? 0) +
            ($invoice->airpods ?? 0) +
            ($invoice->power_bank ?? 0)
        );

        $invoice->save();

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully!');
    }

    // Delete invoice
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully!');
    }
}
