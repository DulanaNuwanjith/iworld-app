<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PhoneInventory;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{

    public function index(Request $request)
    {
        $query = Invoice::query();

        // Apply filters
        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', $request->invoice_number);
        }
        if ($request->filled('customer_name')) {
            $query->where('customer_name', $request->customer_name);
        }
        if ($request->filled('emi')) {
            $query->where('emi', $request->emi);
        }
        if ($request->filled('phone_type')) {
            $query->where('phone_type', $request->phone_type);
        }

        $invoices = $query->latest()->paginate(15)->withQueryString();

        // For filter dropdowns (only distinct strings)
        $allInvoiceNumbers = Invoice::select('invoice_number')->distinct()->pluck('invoice_number');
        $allCustomerNames = Invoice::select('customer_name')->distinct()->pluck('customer_name');
        $filterEmis = Invoice::select('emi')->distinct()->pluck('emi'); // for filter dropdown
        $filterPhoneTypes = Invoice::select('phone_type')->distinct()->pluck('phone_type'); // for filter dropdown

        // For Add Invoice form (full details)
        $addInvoiceEmis = PhoneInventory::select('emi','phone_type','colour','capacity')
            ->where('status', 0) // optional: only available phones
            ->get();

        return view('phone-shop.createInvoice', compact(
            'invoices',
            'allInvoiceNumbers',
            'allCustomerNames',
            'filterEmis',
            'filterPhoneTypes',
            'addInvoiceEmis'
        ));
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

        // Update phone status to 1
        if ($phone) {
            $phone->status = 1;
            $phone->save();
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully!');
    }

    // Delete invoice
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully!');
    }

    public function printInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        return view('phone-shop.invoice-print', compact('invoice'));
    }

    // Generate Sales Report
    public function generateSalesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Get invoices in date range with related phone inventory
        $invoices = Invoice::with('inventory')
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->get();

        // Calculate totals
        $totalInvoices = $invoices->count();
        $totalSelling  = $invoices->sum(fn($inv) => $inv->selling_price ?? 0);
        $totalCost     = $invoices->sum(fn($inv) => $inv->inventory->cost ?? 0);
        $totalProfit   = $totalSelling - $totalCost;

        return view('report.templates.SalesReportDateRange', [
            'invoices'     => $invoices,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'totalInvoices'=> $totalInvoices,
            'totalSelling' => $totalSelling,
            'totalCost'    => $totalCost,
            'totalProfit'  => $totalProfit,
        ]);
    }

}
