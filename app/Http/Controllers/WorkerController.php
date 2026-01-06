<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        $query = Worker::query();

        // Filter by name
        if ($request->has('name') && $request->name != '') {
            $query->where('name', $request->name);
        }

        $workers = $query->latest()->paginate(10)->withQueryString(); // Pagination keeps filters
        $allWorkerNames = Worker::pluck('name')->unique(); // For dropdown

        return view('Compensation.workersDetails', compact('workers', 'allWorkerNames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'national_id'   => 'required|string|max:20|unique:workers,national_id',
            'address'       => 'required|string',
            'phone_1'       => 'required|string',
            'phone_2'       => 'nullable|string',
            'joined_date'   => 'required|date',
            'job_title'     => 'required|string',
            'basic_salary'  => 'required|numeric|min:0',
            'note'          => 'nullable|string',
        ]);

        Worker::create($request->all());

        return redirect()->back()->with('success', 'Worker added successfully');
    }

    public function updateNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $worker = Worker::findOrFail($id);
        $worker->note = $request->note;
        $worker->save();

        return redirect()->back()->with('success', 'Worker note updated successfully');
    }

    public function updateInline(Request $request, $id)
    {
        $request->validate([
            'job_title'    => 'required|string|max:255',
            'phone_1'      => 'required|string|max:20',
            'phone_2'      => 'nullable|string|max:20',
            'address'      => 'required|string|max:1000',
            'basic_salary' => 'required|numeric|min:0',
        ]);

        $worker = Worker::findOrFail($id);

        $worker->update([
            'job_title'    => $request->job_title,
            'phone_1'      => $request->phone_1,
            'phone_2'      => $request->phone_2,
            'address'      => $request->address,
            'basic_salary' => $request->basic_salary,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Worker updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $worker = Worker::findOrFail($id);

        $worker->delete();

        return redirect()->back()->with('success', 'Worker deleted successfully!');
    }


}
