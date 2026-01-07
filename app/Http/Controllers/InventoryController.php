<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\PhoneInventory;
use App\Models\PhoneRepair;
use App\Models\Invoice;
use App\Models\Accessory;

class InventoryController extends Controller
{

    public function index(Request $request)
    {
        // Base query → ONLY unsold phones
        $inventoriesQuery = PhoneInventory::where('status', 0);

        // Filters
        if ($request->filled('phone_type')) {
            $inventoriesQuery->where('phone_type', $request->phone_type);
        }

        if ($request->filled('emi')) {
            $inventoriesQuery->where('emi', $request->emi);
        }

        if ($request->filled('supplier')) {
            $inventoriesQuery->where('supplier', $request->supplier);
        }

        if ($request->filled('stock_type')) {
            $inventoriesQuery->where('stock_type', $request->stock_type);
        }

        if ($request->filled('status_availability')) {
            $inventoriesQuery->where('status_availability', $request->status_availability);
        }


        if ($request->filled('date')) {
            $inventoriesQuery->whereDate('date', $request->date);
        }

        $inventories = $inventoriesQuery
            ->orderBy('date', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Dropdown data → ONLY unsold phones
        $phoneTypes = PhoneInventory::where('status', 0)
            ->select('phone_type')
            ->distinct()
            ->pluck('phone_type');

        $emis = PhoneInventory::where('status', 0)
            ->select('emi')
            ->distinct()
            ->pluck('emi');

        $suppliers = PhoneInventory::where('status', 0)
            ->select('supplier')
            ->distinct()
            ->pluck('supplier');

        $stockTypes = PhoneInventory::where('status', 0)
            ->select('stock_type')
            ->distinct()
            ->pluck('stock_type');

        return view(
            'phone-shop.phoneInventory',
            compact('inventories', 'phoneTypes', 'emis', 'suppliers', 'stockTypes')
        );
    }

    public function sold(Request $request)
    {
        // Step 1: Get latest invoice ID per EMI
        $latestInvoiceIds = Invoice::selectRaw('MAX(id) as id')
            ->groupBy('emi')
            ->pluck('id');

        // Step 2: Join only latest invoices
        $soldQuery = PhoneInventory::where('phone_inventories.status', 1)
            ->leftJoin('invoices', 'phone_inventories.emi', '=', 'invoices.emi')
            ->whereIn('invoices.id', $latestInvoiceIds) // only latest invoice per EMI
            ->select(
                'phone_inventories.*',
                'invoices.customer_name',
                'invoices.customer_phone',
                'invoices.customer_address',
                'invoices.invoice_number',
                'invoices.selling_price',
                'invoices.created_at as sold_date'
            );

        // Filters
        if ($request->filled('phone_type')) {
            $soldQuery->where('phone_inventories.phone_type', $request->phone_type);
        }

        if ($request->filled('emi')) {
            $soldQuery->where('phone_inventories.emi', $request->emi);
        }

        $soldInventories = $soldQuery
            ->orderByDesc('invoices.created_at')
            ->paginate(15)
            ->withQueryString();

        // Dropdowns (only sold)
        $phoneTypes = PhoneInventory::where('status', 1)->distinct()->pluck('phone_type');
        $emis       = PhoneInventory::where('status', 1)->distinct()->pluck('emi');

        return view(
            'phone-shop.sold-phone-inventory',
            compact('soldInventories', 'phoneTypes', 'emis')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        // Base rules
        $rules = [
            'date' => 'required|date',
            'supplier' => 'required|string|max:255',
            'stock_type' => 'required|string|max:50',
            'commission' => 'nullable|numeric|min:0',

            'items' => 'required|array|min:1',
            'items.*.phone_type' => 'required|string|max:255',
            'items.*.colour' => 'required|string|max:255',
            'items.*.capacity' => 'required|string|max:50',
            'items.*.emi' => 'required|string|max:255',
            'items.*.cost' => 'required|numeric|min:0',
            'items.*.note' => 'nullable|string|max:255',
        ];

        // ✅ ONLY require images if Exchange
        if ($request->stock_type === 'Exchange') {
            $rules['supplier_id_front'] = 'required|image|mimes:jpg,jpeg,png|max:2048';
            $rules['supplier_id_back']  = 'required|image|mimes:jpg,jpeg,png|max:2048';
        }

        $validated = $request->validate($rules);

        // Prepare default paths
        $supplierIdFrontPath = null;
        $supplierIdBackPath = null;

        if ($validated['stock_type'] === 'Exchange') {

            // Use first item for naming
            $firstItem = $validated['items'][0];

            // Clean values for filenames
            $emi = preg_replace('/\s+/', '_', $firstItem['emi']);
            $phoneType = preg_replace('/\s+/', '_', $firstItem['phone_type']);
            $supplier = preg_replace('/\s+/', '_', $validated['supplier']);

            // Extensions
            $frontExt = $request->file('supplier_id_front')->getClientOriginalExtension();
            $backExt  = $request->file('supplier_id_back')->getClientOriginalExtension();

            // Custom filenames
            $frontFileName = "{$emi}-{$phoneType}-{$supplier}-id-front.{$frontExt}";
            $backFileName  = "{$emi}-{$phoneType}-{$supplier}-id-back.{$backExt}";

            // Store files
            $supplierIdFrontPath = $request->file('supplier_id_front')
                ->storeAs('supplier_ids', $frontFileName, 'public');

            $supplierIdBackPath = $request->file('supplier_id_back')
                ->storeAs('supplier_ids', $backFileName, 'public');
        }

        foreach ($validated['items'] as $item) {
            PhoneInventory::create([
                'date' => $validated['date'],
                'supplier' => $validated['supplier'],
                'stock_type' => $validated['stock_type'],
                'commission' => $validated['commission'] ?? 0,
                'phone_type' => $item['phone_type'],
                'colour' => $item['colour'],
                'capacity' => $item['capacity'],
                'emi' => $item['emi'],
                'cost' => $item['cost'],
                'note' => $item['note'] ?? null,
                'status' => 0,
                'supplier_id_front' => $supplierIdFrontPath,
                'supplier_id_back' => $supplierIdBackPath,
            ]);
        }

        return redirect()->back()->with('success', 'Phones added successfully.');
    }

    /**
     * Delete a phone inventory item.
     */
    public function destroy(PhoneInventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    public function storeRepair(Request $request, PhoneInventory $inventory): RedirectResponse
    {
        $request->validate([
            'repair_reason' => 'required|string|max:255',
            'repair_cost'   => 'required|numeric|min:0',
        ]);

        // Create a new repair record
        PhoneRepair::create([
            'phone_inventory_id' => $inventory->id,
            'emi'               => $inventory->emi, // SAME EMI
            'repair_reason'     => $request->repair_reason,
            'repair_cost'       => $request->repair_cost,
        ]);

        // Update PhoneInventory cost by adding repair cost
        $inventory->increment('cost', $request->repair_cost);

        return redirect()->back()->with('success', 'Repair details added successfully and inventory cost updated.');
    }

    public function getRepairs(PhoneInventory $inventory)
    {
        // Original cost before any repair
        $originalCost = $inventory->cost - $inventory->repairs->sum('repair_cost');

        return response()->json([
            'emi' => $inventory->emi,
            'original_cost' => $originalCost,
            'total_cost' => $inventory->cost,
            'repairs' => $inventory->repairs->map(function ($r) {
                return [
                    'repair_reason' => $r->repair_reason,
                    'repair_cost' => $r->repair_cost,
                    'updated_at' => $r->updated_at->toDateTimeString(),
                ];
            }),
        ]);
    }

    public function getByEmi($emi)
    {
        $phone = PhoneInventory::where('emi', $emi)
                    ->where('status', 0) // only unsold
                    ->first();

        if (!$phone) {
            return response()->json(['error' => 'Phone not found or already sold'], 404);
        }

        return response()->json([
            'phone_type' => $phone->phone_type,
            'colour' => $phone->colour,
            'capacity' => $phone->capacity,
        ]);
    }

    public function exchange(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:phone_inventories,id',
            'cost' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255', // optional
        ]);

        $inventory = PhoneInventory::findOrFail($request->inventory_id);

        // Get buyer name from invoice
        $invoice = Invoice::where('emi', $inventory->emi)->first();
        $buyerName = $invoice->customer_name ?? 'Unknown Buyer';

        // **Delete related repairs for this phone (under the same EMI)**
        PhoneRepair::where('emi', $inventory->emi)->delete();

        $inventory->update([
            'date' => now(), // today
            'supplier' => $buyerName, // buyer name
            'cost' => $request->cost, // cost from popup
            'stock_type' => 'Exchange',
            'status' => 0, // back to inventory
            'note' => $request->note ?? null, // save note or null
        ]);

        return redirect()->back()->with('success', 'Phone returned to inventory successfully!');
    }

    // Show the form to select date range
    public function generateRepairsReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        // Fetch repairs in date range
        $repairs = PhoneRepair::with('inventory')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRepairs = $repairs->count();
        $totalRepairCost = $repairs->sum('repair_cost');

        return view('report.templates.RepairReportDateRange', compact(
            'repairs',
            'totalRepairs',
            'totalRepairCost',
            'startDate',
            'endDate'
        ));
    }

    public function updateStatusAvailability(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:phone_inventories,id',
            'status_availability' => 'required|in:in_stock,in_repair,with_person',
            'person_name' => 'nullable|string|max:255',
        ]);

        $inventory = PhoneInventory::findOrFail($request->inventory_id);

        $inventory->update([
            'status_availability' => $request->status_availability,
            'person_name' => $request->status_availability === 'with_person' ? $request->person_name : null,
        ]);

        return redirect()->back()->with('success', 'Status updated successfully!');
    }

    public function accessories(Request $request)
    {
        $query = Accessory::query();

        if ($request->filled('name')) {
            $query->where('name', $request->name);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $accessories = $query->latest()->paginate(15)->appends($request->all());

        return view('phone-shop.accessories', compact('accessories'));
    }

    public function storeAccessory(Request $request)
    {
        $validated = $request->validate([
            'supplier' => 'required|string|max:255',
            'date' => 'required|date',
            'commission' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|string|max:100',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            Accessory::create([
                'supplier' => $validated['supplier'],
                'date' => $validated['date'],
                'commission' => $validated['commission'] ?? 0,
                'type' => $item['type'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'cost' => $item['cost'],
            ]);
        }

        return back()->with('success', 'Accessories added successfully');
    }

    public function destroyAccessory($id)
    {
        // Find the accessory by ID
        $accessory = Accessory::findOrFail($id);

        // Delete the accessory
        $accessory->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Accessory deleted successfully.');
    }

    public function removeDamaged(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $accessory = Accessory::findOrFail($id);

        // Ensure quantity to remove does not exceed current quantity
        if ($request->qty > $accessory->quantity) {
            return back()->withErrors(['qty' => 'Quantity to remove cannot exceed available quantity.']);
        }

        $accessory->quantity -= $request->qty;
        $accessory->save();

        return back()->with('success', "Removed {$request->qty} damaged items from {$accessory->name}.");
    }


}
