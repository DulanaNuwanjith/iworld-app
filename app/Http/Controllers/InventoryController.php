<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\PhoneInventory;
    use App\Models\PhoneRepair;

class InventoryController extends Controller
{

    public function index(Request $request)
    {
        $inventoriesQuery = PhoneInventory::query();

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
        if ($request->filled('date')) {
            $inventoriesQuery->whereDate('date', $request->date);
        }

        $inventories = $inventoriesQuery->latest()->paginate(20)->withQueryString();

        // Dropdown data
        $phoneTypes = PhoneInventory::select('phone_type')->distinct()->pluck('phone_type');
        $emis = PhoneInventory::select('emi')->distinct()->pluck('emi');
        $suppliers = PhoneInventory::select('supplier')->distinct()->pluck('supplier');
        $stockTypes = PhoneInventory::select('stock_type')->distinct()->pluck('stock_type');

        return view('phone-shop.phoneInventory', compact('inventories', 'phoneTypes', 'emis', 'suppliers', 'stockTypes'));
    }

    /**
     * Store newly added phones into inventory.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request
        $request->validate([
            'date' => 'required|date',
            'supplier' => 'required|string|max:255',
            'stock_type' => 'required|string|max:50', // master field
            'items' => 'required|array|min:1',
            'items.*.phone_type' => 'required|string|max:255',
            'items.*.colour' => 'required|string|max:255',
            'items.*.capacity' => 'required|string|max:50',
            'items.*.emi' => 'required|string|max:255',
            'items.*.cost' => 'required|numeric|min:0',
            'items.*.note' => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        // Loop through each item and store it
        foreach ($data['items'] as $item) {
            PhoneInventory::create([
                'date' => $data['date'],
                'supplier' => $data['supplier'],
                'stock_type' => $data['stock_type'], // use master field
                'phone_type' => $item['phone_type'],
                'colour' => $item['colour'],
                'capacity' => $item['capacity'],
                'emi' => $item['emi'],
                'cost' => $item['cost'],
                'note' => $item['note'] ?? null,
                'status' => 0,
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


}
