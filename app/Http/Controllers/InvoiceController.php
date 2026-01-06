<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PhoneInventory;
use Illuminate\Http\Request;
use App\Models\Worker;

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

        // Add Exchange phones
        $exchangePhones = PhoneInventory::select('emi','phone_type','colour','capacity','cost')
            ->where('stock_type', 'Exchange')
            ->where('status', 0) 
            ->get();

        $workers = Worker::select('id','name')->orderBy('name')->get();

        return view('phone-shop.createInvoice', compact(
            'invoices',
            'allInvoiceNumbers',
            'allCustomerNames',
            'filterEmis',
            'filterPhoneTypes',
            'addInvoiceEmis',
            'exchangePhones',
            'workers'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'worker_name' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'emi' => 'required|string|exists:phone_inventories,emi',
            'selling_price' => 'required|numeric|min:0',
            'tempered' => 'nullable|numeric|min:0',
            'back_cover' => 'nullable|numeric|min:0',
            'charger' => 'nullable|numeric|min:0',
            'data_cable' => 'nullable|numeric|min:0',
            'cam_glass' => 'nullable|numeric|min:0',
            'hand_free' => 'nullable|numeric|min:0',
            'airpods' => 'nullable|numeric|min:0',
            'power_bank' => 'nullable|numeric|min:0',
            'isExchange' => 'sometimes|boolean',
            'exchange_emi' => 'nullable|string|exists:phone_inventories,emi',
            'exchange_phone_type' => 'nullable|string|max:255',
            'exchange_colour' => 'nullable|string|max:255',
            'exchange_capacity' => 'nullable|string|max:255',
            'exchange_cost' => 'nullable|numeric|min:0',
        ]);

        // Only assign fillable fields
        $invoice = new Invoice($request->only([
            'customer_name', 'customer_phone', 'customer_address',
            'emi', 'selling_price', 'tempered', 'back_cover', 'charger',
            'data_cable', 'cam_glass', 'hand_free', 'airpods', 'power_bank'
        ]));

        $invoice->worker_id = $request->worker_id;
        $invoice->worker_name = $request->worker_name;

        // Assign phone details
        $phone = PhoneInventory::where('emi', $request->emi)->first();
        if ($phone) {
            $invoice->phone_type = $phone->phone_type;
            $invoice->colour = $phone->colour;
            $invoice->capacity = $phone->capacity;
        }

        // Assign exchange phone if selected
        if ((bool)$request->isExchange && $request->exchange_emi) {
            $invoice->exchange_emi = $request->exchange_emi;
            $invoice->exchange_phone_type = $request->exchange_phone_type;
            $invoice->exchange_colour = $request->exchange_colour;
            $invoice->exchange_capacity = $request->exchange_capacity;
            $invoice->exchange_cost = $request->exchange_cost;
        }

        // Generate invoice number
        $lastInvoiceNumber = Invoice::where('invoice_number', 'like', 'INV-GAM-%')
            ->orderBy('id', 'desc')
            ->value('invoice_number');

        $nextNumber = $lastInvoiceNumber ? (int)substr($lastInvoiceNumber, 8) + 1 : 1;
        $invoice->invoice_number = 'INV-GAM-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // Calculate total
        $total = ($invoice->selling_price ?? 0) +
                ($invoice->tempered ?? 0) +
                ($invoice->back_cover ?? 0) +
                ($invoice->charger ?? 0) +
                ($invoice->data_cable ?? 0) +
                ($invoice->hand_free ?? 0) +
                ($invoice->cam_glass ?? 0) +
                ($invoice->airpods ?? 0) +
                ($invoice->power_bank ?? 0);

        if ($invoice->exchange_cost) $total -= floatval($invoice->exchange_cost);

        $invoice->total_amount = $total;

        $invoice->save();

        // Update phone status
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
