<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PhoneInventory;
use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\Accessory;
use App\Models\InvoiceAccessory;

class InvoiceController extends Controller
{

    public function index(Request $request)
    {
        $query = Invoice::query();

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

        $invoices = $query->with('invoiceAccessories')->latest()->paginate(15)->withQueryString();

        $allInvoiceNumbers = Invoice::select('invoice_number')->distinct()->pluck('invoice_number');
        $allCustomerNames = Invoice::select('customer_name')->distinct()->pluck('customer_name');
        $filterEmis = Invoice::select('emi')->distinct()->pluck('emi');
        $filterPhoneTypes = Invoice::select('phone_type')->distinct()->pluck('phone_type');

        $addInvoiceEmis = PhoneInventory::select('emi','phone_type','colour','capacity')
            ->where('status', 0)
            ->where('status_availability', 'in_stock')
            ->get();

        $exchangePhones = PhoneInventory::select('emi','phone_type','colour','capacity','cost')
            ->where('stock_type', 'Exchange')
            ->where('status', 0)
            ->get();

        $workers = Worker::select('id','name')->orderBy('name')->get();
        $allAccessories = Accessory::where('quantity', '>', 0)->get();

        return view('phone-shop.createInvoice', array_merge(
            compact(
                'invoices',
                'allInvoiceNumbers',
                'allCustomerNames',
                'filterEmis',
                'filterPhoneTypes',
                'addInvoiceEmis',
                'exchangePhones',
                'workers',
                'allAccessories'
            ),

        ));
    }

    public function store(Request $request)
    {
        // =====================
        // Validate inputs
        // =====================
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'worker_name' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'emi' => 'required|string|exists:phone_inventories,emi',
            'selling_price' => 'required|numeric|min:0',
            'accessories.*.id' => 'nullable|exists:accessories,id',
            'accessories.*.qty' => 'nullable|integer|min:1',
            'accessories.*.price' => 'nullable|numeric|min:0',
            'exchange_emi' => 'nullable|string|exists:phone_inventories,emi',
            'exchange_phone_type' => 'nullable|string|max:255',
            'exchange_colour' => 'nullable|string|max:255',
            'exchange_capacity' => 'nullable|string|max:255',
            'exchange_cost' => 'nullable|numeric|min:0',
            'payable_amount' => 'nullable|numeric|min:0',
        ]);

        // =====================
        // Create invoice
        // =====================
        $invoice = new Invoice();
        $invoice->customer_name = $request->customer_name;
        $invoice->customer_phone = $request->customer_phone;
        $invoice->customer_address = $request->customer_address;
        $invoice->emi = $request->emi;
        $invoice->selling_price = $request->selling_price ?? 0;
        $invoice->payable_amount = $request->payable_amount ?? 0;

        // Exchange phone
        $invoice->exchange_emi = $request->exchange_emi;
        $invoice->exchange_phone_type = $request->exchange_phone_type;
        $invoice->exchange_colour = $request->exchange_colour;
        $invoice->exchange_capacity = $request->exchange_capacity;
        $invoice->exchange_cost = $request->exchange_cost ?? 0;

        // Worker info
        $invoice->worker_id = $request->worker_id;
        $invoice->worker_name = $request->worker_name;

        // =====================
        // Fetch phone details
        // =====================
        $phone = PhoneInventory::where('emi', $request->emi)->first();
        $phoneCommission = 0;
        if ($phone) {
            $invoice->phone_type = $phone->phone_type;
            $invoice->colour = $phone->colour;
            $invoice->capacity = $phone->capacity;

            // Phone commission
            $phoneCommission = $phone->commission ?? 0;
        }

        // =====================
        // Generate invoice number
        // =====================
        $lastInvoiceNumber = Invoice::where('invoice_number', 'like', 'INV-GAM-%')
            ->orderBy('id', 'desc')
            ->value('invoice_number');
        $nextNumber = $lastInvoiceNumber ? (int)substr($lastInvoiceNumber, 8) + 1 : 1;
        $invoice->invoice_number = 'INV-GAM-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // =====================
        // Calculate accessories total & commission
        // =====================
        $accessoriesTotal = 0;
        $accessoriesCommission = 0;
        $selectedAccessories = $request->input('accessories', []);

        foreach ($selectedAccessories as $acc) {
            if (!empty($acc['id']) && $acc['qty'] > 0 && $acc['price'] >= 0) {
                // Accessories total
                $accessoriesTotal += $acc['qty'] * $acc['price'];

                // Accessories commission
                $accessory = Accessory::find($acc['id']);
                if ($accessory) {
                    $accessoriesCommission += ($accessory->commission ?? 0) * $acc['qty'];

                    // Deduct stock
                    $accessory->quantity = max($accessory->quantity - $acc['qty'], 0);
                    $accessory->save();
                }
            }
        }

        $invoice->accessories_total = $accessoriesTotal;

        // =====================
        // Total commission
        // =====================
        $invoice->total_commission = $phoneCommission + $accessoriesCommission;

        // =====================
        // Calculate total amount (Selling + Accessories - Exchange)
        // =====================
        $invoice->total_amount = $invoice->selling_price + $invoice->accessories_total - ($invoice->exchange_cost ?? 0);

        // =====================
        // Save invoice first to get ID
        // =====================
        $invoice->save();

        // =====================
        // Save Invoice Accessories
        // =====================
        foreach ($selectedAccessories as $acc) {
            if (!empty($acc['id']) && $acc['qty'] > 0 && $acc['price'] >= 0) {
                InvoiceAccessory::create([
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'accessory_id' => $acc['id'],
                    'accessory_name' => Accessory::find($acc['id'])->name,
                    'quantity' => $acc['qty'],
                    'selling_price_accessory' => $acc['price'],
                ]);
            }
        }

        // =====================
        // Mark phone as sold
        // =====================
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

    public function payableInvoices(Request $request)
    {
        $query = Invoice::whereNotNull('payable_amount')
            ->where('payable_amount', '>', 0);

        // Apply filters if provided
        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', $request->invoice_number);
        }

        if ($request->filled('customer_name')) {
            $query->where('customer_name', $request->customer_name);
        }

        // Latest first and paginate
        $invoices = $query->latest()->paginate(20);

        // For the dropdown filters
        $allInvoiceNumbers = Invoice::whereNotNull('payable_amount')
            ->where('payable_amount', '>', 0)
            ->pluck('invoice_number')->unique();

        $allCustomerNames = Invoice::whereNotNull('payable_amount')
            ->where('payable_amount', '>', 0)
            ->pluck('customer_name')->unique();

        return view('phone-shop.payables', compact('invoices', 'allInvoiceNumbers', 'allCustomerNames'));
    }

    public function payAmount(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        $invoice = Invoice::findOrFail($id);

        $payAmount = min($request->amount, $invoice->payable_amount);

        $invoice->payable_amount -= $payAmount;
        $invoice->save();

        return redirect()->back()->with('success', 'Payment processed successfully!');
    }

}
