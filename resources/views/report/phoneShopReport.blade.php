@extends('layouts.reports')

@section('content')
    <div class="flex-1 overflow-y-auto p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Reports Generator</h2>
        </div>

        <!-- Flex container for forms -->
        <div class="flex flex-col md:flex-row md:space-x-8 space-y-8 md:space-y-0 max-w-7xl mx-auto">

            <!-- Repair Report Form -->
            <form action="{{ route('repairs.dateRangeReport') }}" method="GET" target="_blank"
                class="flex-1 bg-white dark:bg-gray-800 p-6 rounded shadow space-y-6 max-w-md mx-auto">
                @csrf
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Phone Repair Report</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm">
                    Select a start date and an end date to generate a phone repair report. The report will display
                    total repair costs and detailed repair entries.
                </p>

                <div>
                    <label for="repair_start_date" class="block font-medium mb-2 text-gray-700 dark:text-gray-300">
                        Start Date
                    </label>
                    <input type="date" name="start_date" id="repair_start_date" required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-gray-900 dark:bg-gray-700 dark:text-white"
                        value="{{ request('start_date') ?? now()->toDateString() }}">
                </div>

                <div>
                    <label for="repair_end_date" class="block font-medium mb-2 text-gray-700 dark:text-gray-300">
                        End Date
                    </label>
                    <input type="date" name="end_date" id="repair_end_date" required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-gray-900 dark:bg-gray-700 dark:text-white"
                        value="{{ request('end_date') ?? now()->toDateString() }}">
                </div>

                <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 rounded transition">
                    Generate Repair Report
                </button>
            </form>

            <!-- Sales Report Form -->
            <form action="{{ route('sales.dateRangeReport') }}" method="GET" target="_blank"
                class="flex-1 bg-white dark:bg-gray-800 p-6 rounded shadow space-y-6 max-w-md mx-auto">
                @csrf
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Sales Report</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm">
                    Select a start date and an end date to generate a sales report. The report will display total
                    profit and detailed invoice entries.
                </p>

                <div>
                    <label for="sales_start_date" class="block font-medium mb-2 text-gray-700 dark:text-gray-300">
                        Start Date
                    </label>
                    <input type="date" name="start_date" id="sales_start_date" required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-gray-900 dark:bg-gray-700 dark:text-white"
                        value="{{ request('start_date') ?? now()->toDateString() }}">
                </div>

                <div>
                    <label for="sales_end_date" class="block font-medium mb-2 text-gray-700 dark:text-gray-300">
                        End Date
                    </label>
                    <input type="date" name="end_date" id="sales_end_date" required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-gray-900 dark:bg-gray-700 dark:text-white"
                        value="{{ request('end_date') ?? now()->toDateString() }}">
                </div>

                <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 rounded transition">
                    Generate Sales Report
                </button>
            </form>

        </div>
    </div>
@endsection
