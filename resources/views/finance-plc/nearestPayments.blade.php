@extends('layouts.finance')

@section('content')
    <div class="flex-1 overflow-y-auto bg-white p-6">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Nearest Payments</h2>
        </div>

        <div class="overflow-x-auto max-h-[1200px] bg-white shadow rounded-lg">
            <!-- Spinner -->
            <div id="pageLoadingSpinner"
                class="fixed inset-0 z-50 bg-white bg-opacity-80 flex flex-col items-center justify-center">
                <svg class="animate-spin h-10 w-10 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <p class="mt-3 text-gray-700 font-semibold">Loading data...</p>
            </div>
            <table class="table-fixed w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-200">
                    <tr class="text-center text-xs text-gray-600 uppercase">
                        <th class="px-4 py-3">Order Number</th>
                        <th class="px-4 py-3">Buyer Name</th>
                        <th class="px-4 py-3">Buyer ID</th>
                        <th class="px-4 py-3">Buyer Address</th>
                        <th class="px-4 py-3">Phone No 1</th>
                        <th class="px-4 py-3">Phone No 2</th>
                        <th class="px-4 py-3 w-72 text-left">Next Expected Payment</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($financeOrders as $order)
                        @php
                            // Get the nearest unpaid installment
                            $nextPayment = $order->payments->whereNull('paid_at')->sortBy('expected_date')->first();
                        @endphp
                        <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200 text-left">
                            <td class="text-center px-4 py-3 font-bold break-words">{{ $order->order_number }}</td>
                            <td class="px-4 py-3 break-words">{{ $order->buyer_name }}</td>
                            <td class="px-4 py-3 break-words">{{ $order->buyer_id }}</td>
                            <td class="px-4 py-3 break-words">{{ $order->buyer_address }}</td>
                            <td class="px-4 py-3 break-words">{{ $order->phone_1 }}</td>
                            <td class="px-4 py-3 break-words">{{ $order->phone_2 }}</td>
                            <td class="px-4 py-3 break-words">
                                @if ($nextPayment)
                                    <div>
                                        <span class="font-bold">Amount Due:</span> LKR
                                        {{ number_format($nextPayment->amount, 2) }}
                                    </div>
                                    <div>
                                        <span class="font-bold">Expected Date:</span>
                                        {{ \Carbon\Carbon::parse($nextPayment->expected_date)->format('Y-m-d') }}
                                    </div>
                                @else
                                    <span class="text-gray-500">All Paid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-3 text-center text-gray-500">No finance orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        <div class="py-6 flex justify-center">
            {{ $financeOrders->links() }}
        </div>


    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const spinner = document.getElementById("pageLoadingSpinner");

            // Show spinner immediately
            spinner.classList.remove("hidden");

            // Wait for table to render completely
            window.requestAnimationFrame(() => {
                spinner.classList.add("hidden"); // hide spinner after rendering
            });
        });
    </script>
@endsection
