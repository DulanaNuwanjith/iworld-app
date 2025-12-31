<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PhoneInventory;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{

    public function index()
    {
        $invoices = Invoice::latest()->paginate(1);
        $emis = PhoneInventory::select('emi', 'phone_type', 'colour', 'capacity')->get();

        return view('phone-shop.createInvoice', compact('invoices', 'emis'));
    }

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

        // Fill phone details
        $phone = PhoneInventory::where('emi', $request->emi)->first();
        if ($phone) {
            $invoice->phone_type = $phone->phone_type;
            $invoice->colour = $phone->colour;
            $invoice->capacity = $phone->capacity;
        }

        // Generate unique invoice number (INV-GAM-00001, 00002, ...)
        $lastInvoiceNumber = Invoice::where('invoice_number', 'like', 'INV-GAM-%')
            ->orderBy('id', 'desc')
            ->value('invoice_number');

        if ($lastInvoiceNumber) {
            $lastNumber = (int)substr($lastInvoiceNumber, 8); // get numeric part
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $invoice->invoice_number = 'INV-GAM-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

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
