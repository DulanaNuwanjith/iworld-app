@extends('layouts.finance')

@section('content')
    <div class="flex-1 overflow-y-auto max-h-screen">
        <div class="w-full px-6 lg:px-2">
            <div class="bg-white overflow-hidden">
                <div class="p-4 text-gray-900">

                    {{-- Filters --}}
                    <div class="flex justify-start">
                        <button onclick="toggleFilterForm()"
                            class="bg-white border border-gray-500 text-gray-500 hover:text-gray-600 hover:border-gray-600 font-semibold py-1 px-3 rounded shadow flex items-center gap-2 mb-6">
                            <img src="{{ asset('icons/filter.png') }}" class="w-6 h-6" alt="Filter Icon">
                            Filters
                        </button>
                    </div>

                    <div id="filterFormContainerFinance" class="mt-4 hidden">
                        <form id="filterFormFinance" method="GET" action="{{ route('finance.settledPayments') }}"
                            class="mb-6 flex gap-6 items-center">

                            <div class="flex items-center gap-4 flex-wrap">

                                {{-- Order Number Dropdown --}}
                                <div class="relative inline-block text-left w-48">
                                    <label for="orderDropdownFinance"
                                        class="block text-sm font-medium text-gray-700 mb-1">Order No</label>
                                    <input type="hidden" name="order_number" id="orderInputFinance"
                                        value="{{ request('order_number') }}">
                                    <button id="orderDropdownFinance" type="button"
                                        class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                        onclick="toggleOrderDropdownFinance(event)">
                                        <span
                                            id="selectedOrderNoFinance">{{ request('order_number') ?? 'Select Order No' }}</span>
                                        <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div id="orderDropdownMenuFinance"
                                        class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                        <input type="text" id="orderSearchInputFinance" onkeyup="filterOrdersFinance()"
                                            placeholder="Search..." class="w-full px-2 py-1 text-sm border rounded-md"
                                            autocomplete="off">
                                        <div onclick="selectOrderFinance('')"
                                            class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All Orders
                                        </div>
                                        @foreach ($orderNumbers as $order)
                                            <div onclick="selectOrderFinance('{{ $order }}')" tabindex="0"
                                                class="order-option-finance px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                {{ $order }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Buyer Name Dropdown --}}
                                <div class="relative inline-block text-left w-56">
                                    <label for="buyerNameDropdownFinance"
                                        class="block text-sm font-medium text-gray-700 mb-1">Buyer Name</label>
                                    <input type="hidden" name="buyer_name" id="buyerNameInputFinance"
                                        value="{{ request('buyer_name') }}">
                                    <button id="buyerNameDropdownFinance" type="button"
                                        class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                        onclick="toggleBuyerNameDropdownFinance(event)">
                                        <span
                                            id="selectedBuyerNameFinance">{{ request('buyer_name') ?? 'Select Buyer' }}</span>
                                        <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div id="buyerNameDropdownMenuFinance"
                                        class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                        <input type="text" id="buyerNameSearchInputFinance"
                                            onkeyup="filterBuyerNamesFinance()" placeholder="Search..."
                                            class="w-full px-2 py-1 text-sm border rounded-md" autocomplete="off">
                                        <div onclick="selectBuyerNameFinance('')"
                                            class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All Buyers
                                        </div>
                                        @foreach ($buyerNames as $buyer)
                                            <div onclick="selectBuyerNameFinance('{{ $buyer }}')" tabindex="0"
                                                class="buyer-option-finance px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                {{ $buyer }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Buyer ID Dropdown --}}
                                <div class="relative inline-block text-left w-56">
                                    <label for="buyerIdDropdownFinance"
                                        class="block text-sm font-medium text-gray-700 mb-1">Buyer ID</label>
                                    <input type="hidden" name="buyer_id" id="buyerIdInputFinance"
                                        value="{{ request('buyer_id') }}">
                                    <button id="buyerIdDropdownFinance" type="button"
                                        class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                        onclick="toggleBuyerIdDropdownFinance(event)">
                                        <span
                                            id="selectedBuyerIdFinance">{{ request('buyer_id') ?? 'Select Buyer ID' }}</span>
                                        <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div id="buyerIdDropdownMenuFinance"
                                        class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                        <input type="text" id="buyerIdSearchInputFinance"
                                            onkeyup="filterBuyerIdsFinance()" placeholder="Search..."
                                            class="w-full px-2 py-1 text-sm border rounded-md" autocomplete="off">
                                        <div onclick="selectBuyerIdFinance('')"
                                            class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All Buyer
                                            IDs</div>
                                        @foreach ($buyerIds as $id)
                                            <div onclick="selectBuyerIdFinance('{{ $id }}')" tabindex="0"
                                                class="buyerid-option-finance px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                {{ $id }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Filter Buttons --}}
                                <div class="flex items-end space-x-2 mt-2">
                                    <button type="submit"
                                        class="mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                                        Apply Filters
                                    </button>
                                    <button type="button" id="clearFiltersBtnFinance"
                                        class="mt-4 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                                        Clear
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Settled Payments</h2>
                    </div>

                    <div class="overflow-x-auto bg-white shadow rounded-lg">

                        <!-- Spinner -->
                        <div id="pageLoadingSpinner"
                            class="fixed inset-0 z-50 bg-white bg-opacity-80 flex flex-col items-center justify-center">
                            <svg class="animate-spin h-10 w-10 text-gray-600" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
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
                                    <th class="px-4 py-3 w-64">Total Paid (LKR)</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($financeOrders as $order)
                                    <tr class="odd:bg-white even:bg-gray-50 text-left">
                                        <td class="text-center px-4 py-3 font-bold">
                                            {{ $order->order_number }}
                                        </td>
                                        <td class="px-4 py-3">{{ $order->buyer_name }}</td>
                                        <td class="px-4 py-3">{{ $order->buyer_id }}</td>
                                        <td class="px-4 py-3">{{ $order->buyer_address }}</td>
                                        <td class="px-4 py-3">{{ $order->phone_1 }}</td>
                                        <td class="px-4 py-3">{{ $order->phone_2 }}</td>
                                        <td class="px-4 py-3 font-semibold text-green-700 text-center">
                                            {{ number_format($order->paid_amount_fullamount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-3 text-center text-gray-500">
                                            No settled payments found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="py-6 flex justify-center">
                        {{ $financeOrders->links() }}
                    </div>

                </div>
            </div>
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
    <script>
        function toggleFilterForm() {
            const form = document.getElementById('filterFormContainerFinance');
            form.classList.toggle('hidden');
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // ---- ORDER NUMBER ----
            const orderMenu = document.getElementById("orderDropdownMenuFinance");
            orderMenu.addEventListener("click", e => e.stopPropagation());

            window.toggleOrderDropdownFinance = function(e) {
                e.stopPropagation();
                closeAllDropdowns();
                orderMenu.classList.toggle("hidden");
            };

            window.selectOrderFinance = function(val) {
                document.getElementById("orderInputFinance").value = val;
                document.getElementById("selectedOrderNoFinance").textContent = val || "Select Order No";
                closeAllDropdowns();
            };

            window.filterOrdersFinance = function() {
                const filter = document.getElementById("orderSearchInputFinance").value.toLowerCase();
                document.querySelectorAll(".order-option-finance").forEach(option => {
                    option.style.display = option.textContent.toLowerCase().includes(filter) ? "block" :
                        "none";
                });
            };

            // ---- BUYER NAME ----
            const buyerMenu = document.getElementById("buyerNameDropdownMenuFinance");
            buyerMenu.addEventListener("click", e => e.stopPropagation());

            window.toggleBuyerNameDropdownFinance = function(e) {
                e.stopPropagation();
                closeAllDropdowns();
                buyerMenu.classList.toggle("hidden");
            };

            window.selectBuyerNameFinance = function(val) {
                document.getElementById("buyerNameInputFinance").value = val;
                document.getElementById("selectedBuyerNameFinance").textContent = val || "Select Buyer";
                closeAllDropdowns();
            };

            window.filterBuyerNamesFinance = function() {
                const filter = document.getElementById("buyerNameSearchInputFinance").value.toLowerCase();
                document.querySelectorAll(".buyer-option-finance").forEach(option => {
                    option.style.display = option.textContent.toLowerCase().includes(filter) ? "block" :
                        "none";
                });
            };

            // ---- BUYER ID ----
            const buyerIdMenu = document.getElementById("buyerIdDropdownMenuFinance");
            buyerIdMenu.addEventListener("click", e => e.stopPropagation());

            window.toggleBuyerIdDropdownFinance = function(e) {
                e.stopPropagation();
                closeAllDropdowns();
                buyerIdMenu.classList.toggle("hidden");
            };

            window.selectBuyerIdFinance = function(val) {
                document.getElementById("buyerIdInputFinance").value = val;
                document.getElementById("selectedBuyerIdFinance").textContent = val || "Select Buyer ID";
                closeAllDropdowns();
            };

            window.filterBuyerIdsFinance = function() {
                const filter = document.getElementById("buyerIdSearchInputFinance").value.toLowerCase();
                document.querySelectorAll(".buyerid-option-finance").forEach(option => {
                    option.style.display = option.textContent.toLowerCase().includes(filter) ? "block" :
                        "none";
                });
            };

            // ---- Close all dropdowns when clicking outside ----
            function closeAllDropdowns() {
                orderMenu.classList.add("hidden");
                buyerMenu.classList.add("hidden");
                buyerIdMenu.classList.add("hidden");
            }

            document.addEventListener("click", closeAllDropdowns);

            // ---- Clear Filters Button ----
            document.getElementById("clearFiltersBtnFinance").addEventListener("click", function() {
                window.location.href = window.location.pathname;
            });

        });
    </script>
@endsection
