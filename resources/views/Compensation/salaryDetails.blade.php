@php use Carbon\Carbon; @endphp

@php
    $salaries = $salaries ?? collect();
@endphp

<div class="flex h-full w-full bg-white">
    @extends('layouts.compensation')

    @section('content')
        <div class="flex-1 overflow-y-hidden bg-white">
            <div class="w-full px-6 lg:px-2">
                <div class="bg-white overflow-hidden shadow rounded-lg p-4 text-gray-900">

                    {{-- Month Filter Form --}}
                    <form method="GET" action="{{ route('salary.report') }}" class="flex gap-4 mb-4 items-end">
                        <div>
                            <label for="month_year" class="block text-sm font-medium text-gray-700">Month</label>
                            <input type="month" name="month_year" id="month_year"
                                value="{{ request('month_year', now()->format('Y-m')) }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            Generate Report
                        </button>

                        <a href="{{ route('salary.report.print', ['month_year' => request('month_year')]) }}"
                            target="_blank" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Print Report
                        </a>
                    </form>

                    {{-- Table --}}
                    <div class="overflow-x-auto bg-white shadow rounded-lg">
                        <table class="table-fixed w-full text-sm divide-y divide-gray-200">
                            <thead class="bg-gray-200 text-gray-600 text-xs uppercase text-center sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 w-48">Worker Name</th>
                                    <th class="px-4 py-3 w-36">Basic Salary</th>
                                    <th class="px-4 py-3 w-36">Total Commission</th>
                                    <th class="px-4 py-3 w-36">Total Sales</th>
                                    <th class="px-4 py-3 w-36">Invoice Count</th>
                                    <th class="px-4 py-3 w-56">Month-Year</th>
                                    <th class="px-4 py-3 w-36">Monthly Salary</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-center">
                                @forelse($salaries as $salary)
                                    @php
                                        $monthlySalary = $salary['basic_salary'] + $salary['total_commission'];
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-2 font-semibold">{{ $salary['worker_name'] }}</td>
                                        <td class="px-4 py-2">{{ number_format($salary['basic_salary'], 2) }}</td>
                                        <td class="px-4 py-2">{{ number_format($salary['total_commission'], 2) }}</td>
                                        <td class="px-4 py-2">{{ number_format($salary['total_sales'], 2) }}</td>
                                        <td class="px-4 py-2">{{ $salary['invoice_count'] }}</td>
                                        <td class="px-4 py-2">
                                            {{ Carbon::createFromDate($salary['year'], $salary['month'], 1)->format('M Y') }}
                                        </td>
                                        <td class="px-4 py-2 font-semibold text-green-600">
                                            {{ number_format($monthlySalary, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-4 text-gray-500">No salary records found for
                                            selected
                                            month.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
