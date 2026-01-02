<head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<div class="flex h-full w-full">
    @extends('layouts.reports')

    @section('content')
        <div class="flex-1 overflow-y-auto p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Phone Repair Reports Generator</h2>
            </div>

            <!-- Flex container for forms -->
            <div class="flex flex-col md:flex-row md:space-x-8 space-y-8 md:space-y-0 max-w-7xl mx-auto">

                <!-- Date Range Form -->
                <form action="{{ route('repairs.dateRangeReport') }}" method="GET" target="_blank"
                    class="flex-1 bg-white dark:bg-gray-800 p-6 rounded shadow space-y-6 max-w-md mx-auto">
                    @csrf
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Generate Repairs by Date Range</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm">
                        Select a start date and an end date to generate a phone repair report for all repairs recorded
                        within the selected period. The report will open in a new tab and display total repair costs
                        and detailed repair entries.
                    </p>

                    <div>
                        <label for="start_date" class="block font-medium mb-2 text-gray-700 dark:text-gray-300">Start
                            Date</label>
                        <input type="date" name="start_date" id="start_date" required
                            class="w-full border border-gray-300 rounded px-3 py-2 text-gray-900 dark:bg-gray-700 dark:text-white"
                            value="{{ request('start_date') ?? now()->toDateString() }}">
                    </div>

                    <div>
                        <label for="end_date" class="block font-medium mb-2 text-gray-700 dark:text-gray-300">End
                            Date</label>
                        <input type="date" name="end_date" id="end_date" required
                            class="w-full border border-gray-300 rounded px-3 py-2 text-gray-900 dark:bg-gray-700 dark:text-white"
                            value="{{ request('end_date') ?? now()->toDateString() }}">
                    </div>

                    <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 rounded transition">
                        Generate Repair Report
                    </button>
                </form>

            </div>
        </div>
    </div>
@endsection
