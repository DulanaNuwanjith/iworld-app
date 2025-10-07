@php use Carbon\Carbon; @endphp

<head>

    <!-- Import Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title> Iworld-Finance </title>
</head>
<div class="flex h-full w-full bg-white">
    @extends('layouts.finance')

    @section('content')
        <div class="flex-1 overflow-y-hidden">
            <div class="">
                <div class="w-full px-6 lg:px-2">
                    <div class="bg-white overflow-hidden">
                        <div class="p-4 text-gray-900">
                            {{-- Style for Sweet Alert --}}
                            <style>
                                /* Toast style */
                                .swal2-toast {
                                    font-size: 0.875rem;
                                    padding: 0.75rem 1rem;
                                    border-radius: 8px;
                                    background-color: #ffffff !important;
                                    position: relative;
                                    box-sizing: border-box;
                                    color: #6c757d !important;
                                    /* Medium gray */
                                }

                                .swal2-toast .swal2-title,
                                .swal2-toast .swal2-html-container {
                                    color: #495057 !important;
                                    /* Darker gray */
                                }

                                .swal2-toast .swal2-icon {
                                    color: #6c757d !important;
                                    /* Icon gray */
                                }

                                .swal2-shadow {
                                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
                                }

                                .swal2-toast::after {
                                    content: '';
                                    position: absolute;
                                    bottom: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 3px;
                                    background-color: #6c757d;
                                    /* Gray underline */
                                    border-radius: 0 0 8px 8px;
                                }
                            </style>

                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    // Success toast
                                    @if (session('success'))
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'success',
                                            title: '{{ session('success') }}',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true,
                                            customClass: {
                                                popup: 'swal2-toast swal2-shadow'
                                            },
                                        });
                                    @endif

                                    // Error toast
                                    @if (session('error'))
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'error',
                                            title: '{{ session('error') }}',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true,
                                            customClass: {
                                                popup: 'swal2-toast swal2-shadow'
                                            },
                                            iconColor: '#6c757d'
                                        });
                                    @endif

                                    // Validation errors toast
                                    @if ($errors->any())
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'warning',
                                            title: 'Validation Errors',
                                            html: `{!! implode('<br>', $errors->all()) !!}`,
                                            showConfirmButton: false,
                                            timer: 3000,
                                            timerProgressBar: true,
                                            customClass: {
                                                popup: 'swal2-toast swal2-shadow'
                                            },
                                            iconColor: '#6c757d'
                                        });
                                    @endif
                                });

                                // Delete confirmation
                                function confirmDelete(id) {
                                    Swal.fire({
                                        title: '<span style="color:#495057;">Are you sure?</span>',
                                        html: '<span style="color:#6c757d;">This record will be permanently deleted!</span>',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc3545', // Red
                                        cancelButtonColor: '#adb5bd', // Light gray
                                        confirmButtonText: 'Yes, delete it!',
                                        cancelButtonText: 'Cancel',
                                        background: '#ffffff',
                                        customClass: {
                                            popup: 'swal2-toast swal2-shadow'
                                        },
                                        iconColor: '#6c757d'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            document.getElementById(`delete-form-${id}`).submit();
                                        }
                                    });
                                }
                            </script>

                            {{-- Filters --}}
                            <div class="flex justify-start">
                                <button onclick="toggleFilterForm()"
                                    class="bg-white border border-gray-500 text-gray-500 hover:text-gray-600 hover:border-gray-600 font-semibold py-1 px-3 rounded shadow flex items-center gap-2 mb-6">
                                    <img src="{{ asset('icons/filter.png') }}" class="w-6 h-6" alt="Filter Icon">
                                    Filters
                                </button>
                                <button onclick="toggleCalForm()"
                                    class="bg-white border border-gray-500 text-gray-500 hover:text-gray-600 hover:border-gray-600 font-semibold py-1 px-3 rounded shadow flex items-center gap-2 mb-6 ml-2">
                                    Pricing Calculator
                                </button>
                            </div>

                            <div id="filterFormContainerFinance" class="mt-4 hidden">
                                <form id="filterFormFinance" method="GET" action="{{ route('finance.index') }}"
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
                                                <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>

                                            <div id="orderDropdownMenuFinance"
                                                class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                                <input type="text" id="orderSearchInputFinance"
                                                    onkeyup="filterOrdersFinance()" placeholder="Search..."
                                                    class="w-full px-2 py-1 text-sm border rounded-md" autocomplete="off">
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
                                                <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                    fill="currentColor">
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
                                                    <div onclick="selectBuyerNameFinance('{{ $buyer }}')"
                                                        tabindex="0"
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
                                                <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                    fill="currentColor">
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
                                                    <div onclick="selectBuyerIdFinance('{{ $id }}')"
                                                        tabindex="0"
                                                        class="buyerid-option-finance px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                        {{ $id }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Item Created Date --}}
                                        <div class="inline-block text-left w-48">
                                            <label for="itemCreatedDateFinance"
                                                class="block text-sm font-medium text-gray-700">Item Date</label>
                                            <input type="date" name="item_created_date" id="itemCreatedDateFinance"
                                                value="{{ request('item_created_date') }}"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm">
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

                            <div id="pricingCalculatorContainer" class="hidden">
                                <div class="flex-1 overflow-y-auto bg-white p-6">

                                    <div class="max-w-md bg-gray-50 p-6 rounded-lg shadow">
                                        {{-- Amount Input --}}
                                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Enter
                                            Amount</label>
                                        <input type="number" id="amount"
                                            class="w-full px-4 py-2 border rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                                            placeholder="Enter amount">

                                        {{-- 18% Value --}}
                                        <div class="mt-6 bg-indigo-50 p-3 rounded-lg">
                                            <p class="text-gray-700">18% of Amount:
                                                <span id="tax" class="font-semibold text-indigo-700">0.00</span>
                                            </p>
                                        </div>

                                        {{-- Total --}}
                                        <div class="mt-3 bg-green-50 p-3 rounded-lg">
                                            <p class="text-gray-700">Total (Amount + 18%):
                                                <span id="total" class="font-semibold text-green-700">0.00</span>
                                            </p>
                                        </div>
                                    </div>

                                </div>

                                <script>
                                    document.getElementById('amount').addEventListener('input', function() {
                                        let amount = parseFloat(this.value) || 0;
                                        let tax = (amount * 0.18).toFixed(2);
                                        let total = (amount + parseFloat(tax)).toFixed(2);

                                        document.getElementById('tax').textContent = tax;
                                        document.getElementById('total').textContent = total;
                                    });
                                </script>
                            </div>


                            <div class="flex-1">

                                <div class="flex justify-between items-center mb-6">
                                    <h1 class="text-2xl font-bold text-gray-800">Finance PLC Records
                                    </h1>

                                    <div class="flex space-x-3">

                                        <button
                                            onclick="document.getElementById('addFinanceModal').classList.remove('hidden')"
                                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow">
                                            + Add Finance Order
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Main Table --}}
                            <div id="sampleInquiryRecordsScroll"
                                class="overflow-x-auto max-h-[1200px] bg-white shadow rounded-lg">
                                <table class="table-fixed w-full text-sm divide-y divide-gray-200">
                                    <thead class="bg-gray-200 text-left">
                                        <tr class="text-center">
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200  px-4 py-3 w-40 text-xs text-gray-600  uppercase whitespace-normal break-words">
                                                Order No
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Buyer Details
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Item Details
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Mails & Passwords
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-64 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Price & Payment
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Note
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($financeOrders as $order)
                                            <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200  text-left">
                                                <!-- Order No -->
                                                <td class="text-center px-4 py-3">
                                                    <div class="font-bold">{{ $order->order_number }}</div>
                                                    <div class="text-xs">
                                                        {{ \Carbon\Carbon::parse($order->item_created_date)->format('Y-m-d') }}
                                                    </div>
                                                    <div class="text-xs">({{ $order->coordinator_name }})</div>
                                                </td>

                                                <!-- Buyer Details -->
                                                <td class="px-4 py-3 text-xs text-left break-words">
                                                    <div class="font-bold">Name: <span
                                                            class="font-normal">{{ $order->buyer_name }}</span></div>
                                                    <div class="font-bold">ID: <span
                                                            class="font-normal">{{ $order->buyer_id }}</span></div>
                                                    <div class="font-bold">Address: <span
                                                            class="font-normal">{{ $order->buyer_address }}</span></div>
                                                    <div class="font-bold">Phone 1: <span
                                                            class="font-normal">{{ $order->phone_1 }}</span></div>
                                                    <div class="font-bold">Phone 2: <span
                                                            class="font-normal">{{ $order->phone_2 }}</span></div>
                                                    <div class="mt-1 flex gap-1">
                                                        @if ($order->id_photo)
                                                            <a href="{{ asset('storage/' . $order->id_photo) }}"
                                                                target="_blank">
                                                                <img src="{{ asset('storage/' . $order->id_photo) }}"
                                                                    alt="ID Photo" class="w-16 h-16 object-cover rounded">
                                                            </a>
                                                        @endif
                                                        @if ($order->electricity_bill_photo)
                                                            <a href="{{ asset('storage/' . $order->electricity_bill_photo) }}"
                                                                target="_blank">
                                                                <img src="{{ asset('storage/' . $order->electricity_bill_photo) }}"
                                                                    alt="Electricity Bill"
                                                                    class="w-16 h-16 object-cover rounded">
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Item Details -->
                                                <td class="px-4 py-3 text-xs text-left break-words">
                                                    <div class="font-bold">Item: <span
                                                            class="font-normal">{{ $order->item_name }}</span></div>
                                                    <div class="font-bold">EMI: <span
                                                            class="font-normal">{{ $order->emi_number }}</span></div>
                                                    <div class="font-bold">Colour: <span
                                                            class="font-normal">{{ $order->colour }}</span></div>
                                                    <div class="mt-1 flex gap-1">
                                                        @if ($order->photo_1)
                                                            <a href="{{ asset('storage/' . $order->photo_1) }}"
                                                                target="_blank">
                                                                <img src="{{ asset('storage/' . $order->photo_1) }}"
                                                                    alt="Photo 1" class="w-16 h-16 object-cover rounded">
                                                            </a>
                                                        @endif
                                                        @if ($order->photo_2)
                                                            <a href="{{ asset('storage/' . $order->photo_2) }}"
                                                                target="_blank">
                                                                <img src="{{ asset('storage/' . $order->photo_2) }}"
                                                                    alt="Photo 2" class="w-16 h-16 object-cover rounded">
                                                            </a>
                                                        @endif
                                                        @if ($order->photo_about)
                                                            <a href="{{ asset('storage/' . $order->photo_about) }}"
                                                                target="_blank">
                                                                <img src="{{ asset('storage/' . $order->photo_about) }}"
                                                                    alt="About Photo"
                                                                    class="w-16 h-16 object-cover rounded">
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Mails & Passwords -->
                                                <td class="px-4 py-3 text-xs text-left break-words">
                                                    <div class="font-bold">iCloud: <span
                                                            class="font-normal">{{ $order->icloud_mail }}</span></div>
                                                    <div class="font-bold">Password: <span
                                                            class="font-normal">{{ $order->icloud_password }}</span></div>
                                                    <div class="font-bold">Screen Time Lock: <span
                                                            class="font-normal">{{ $order->screen_lock_password }}</span>
                                                    </div>
                                                </td>

                                                @php
                                                    $firstPayment = $order->payments
                                                        ->where('installment_number', 1)
                                                        ->first();

                                                    $secondPaymentDate = $firstPayment
                                                        ? \Carbon\Carbon::parse($firstPayment->paid_at)->addDays(30)
                                                        : null;
                                                    $thirdPaymentDate = $firstPayment
                                                        ? \Carbon\Carbon::parse($firstPayment->paid_at)->addDays(60)
                                                        : null;
                                                @endphp

                                                <td class="px-4 py-3 text-xs text-left break-words">
                                                    <div class="font-bold">
                                                        Phone Price: LKR <span
                                                            class="font-normal">{{ number_format($order->price, 2) }}</span>
                                                    </div>
                                                    <div class="font-bold">
                                                        Rate: <span
                                                            class="font-normal">{{ number_format($order->rate) }}%</span>
                                                    </div>
                                                    <div class="font-bold">
                                                        Amount of Installments: <span
                                                            class="font-normal">{{ number_format($order->amount_of_installments) }}</span>
                                                    </div>
                                                    <div class="font-bold">
                                                        Due Payment: LKR <span
                                                            class="font-normal">{{ number_format($order->due_payment, 2) }}</span>
                                                    </div>
                                                    <div class="font-bold">
                                                        Remaining Balance: LKR <span
                                                            class="font-normal text-red-500">{{ number_format($order->remaining_amount, 2) }}</span>
                                                    </div>

                                                    <div class="flex justify-center mt-3">
                                                        <button onclick="openModal({{ $order->id }})"
                                                            class="px-2 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600">
                                                            Payment Details
                                                        </button>
                                                    </div>
                                                </td>

                                                <!-- Note -->
                                                <td class="px-4 py-3 text-xs text-center whitespace-normal break-words">
                                                    <form action="{{ route('finance.update-note', $order->id) }}"
                                                        method="POST" class="w-full">
                                                        @csrf
                                                        @method('PATCH')

                                                        <textarea name="note" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm" rows="2" required>{{ old('note', $order->note) }}</textarea>

                                                        <button type="submit"
                                                            class="w-full mt-1 px-2 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition-all duration-200 text-xs">
                                                            Save
                                                        </button>
                                                    </form>
                                                </td>

                                                <!-- Actions -->
                                                <td class="px-4 py-3 text-xs text-center break-words">
                                                    @if (auth()->user() && auth()->user()->role === 'SUPERADMIN')
                                                        <form id="delete-form-{{ $order->id }}"
                                                            action="{{ route('finance.destroy', $order->id) }}"
                                                            method="POST" class="flex justify-center">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                onclick="confirmDelete('{{ $order->id }}')"
                                                                class="bg-red-600 h-10 mt-1 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <button onclick="printInvoice({{ $order->id }})"
                                                        class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                                        Print Invoice
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="py-6 flex justify-center">
                                {{ $financeOrders->links() }}
                            </div>
                            @foreach ($financeOrders as $order)
                                @php
                                    $overdueChargePerDay = 200;
                                    $totalPaid = 0;
                                    $totalOverdue = 0;
                                    $payments = $order->payments->sortBy('installment_number');
                                    $lastPayment = $payments->whereNull('paid_at')->last();
                                @endphp

                                <!-- Pay Modal -->
                                <div id="payModal-{{ $order->id }}"
                                    class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                                    <div class="bg-white rounded shadow-lg w-[90%] max-w-4xl p-4 relative">
                                        <h3 class="text-lg font-bold mb-4 text-center">Installments for
                                            {{ $order->order_number }}</h3>

                                        <div class="overflow-x-auto">
                                            <table class="min-w-full border text-xs text-center">
                                                <thead>
                                                    <tr class="bg-gray-100 border-b">
                                                        <th>#</th>
                                                        <th>Amount (LKR)</th>
                                                        <th>Expected Date</th>
                                                        <th>Overdue Days</th>
                                                        <th>Overdue Amount (LKR)</th>
                                                        <th>Amount to Pay</th>
                                                        <th>Paid</th>
                                                        <th>Paid Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($payments as $payment)
                                                        @php
                                                            $overdueAmount =
                                                                $payment->overdue_days * $overdueChargePerDay;
                                                            $totalPaid += $payment->paid_amount ?? 0;
                                                            $totalOverdue += $payment->overdue_amount ?? 0;

                                                            $isLastUnpaid = $payment->id === optional($lastPayment)->id;

                                                            $amountToPay = $payment->amount + $overdueAmount;

                                                            if ($isLastUnpaid) {
                                                                $paidSoFar = $payments
                                                                    ->whereNotNull('paid_at')
                                                                    ->sum('paid_amount');
                                                                $totalOverduePaid = $payments
                                                                    ->whereNotNull('paid_at')
                                                                    ->sum('overdue_amount');
                                                                $remainingBalance =
                                                                    $order->due_payment -
                                                                    ($paidSoFar - $totalOverduePaid);

                                                                $unpaidOverdue = $payments
                                                                    ->whereNull('paid_at')
                                                                    ->where('id', '<>', $payment->id)
                                                                    ->sum('overdue_amount');

                                                                $amountToPay = max(
                                                                    $remainingBalance + $unpaidOverdue + $overdueAmount,
                                                                    0,
                                                                );
                                                            }
                                                        @endphp

                                                        <tr class="border-b">
                                                            <td>{{ $payment->installment_number }}</td>
                                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($payment->expected_date)->format('Y-m-d') }}
                                                            </td>

                                                            <td>
                                                                @if (!$payment->paid_at)
                                                                    <input type="number" name="overdue_days"
                                                                        form="form-{{ $payment->id }}"
                                                                        value="{{ $payment->overdue_days }}"
                                                                        min="0"
                                                                        class="w-16 px-2 py-1 border rounded text-xs text-right overdue-days"
                                                                        data-amount="{{ $payment->amount }}"
                                                                        data-target="amount-to-pay-{{ $payment->id }}"
                                                                        data-overdue="overdue-amount-{{ $payment->id }}"
                                                                        @if ($isLastUnpaid) readonly @endif>
                                                                @else
                                                                    <span
                                                                        class="text-gray-500 text-xs">{{ $payment->overdue_days }}</span>
                                                                @endif
                                                            </td>

                                                            <td><span
                                                                    id="overdue-amount-{{ $payment->id }}">{{ number_format($overdueAmount, 2) }}</span>
                                                            </td>
                                                            <td><span
                                                                    id="amount-to-pay-{{ $payment->id }}">{{ number_format($amountToPay, 2) }}</span>
                                                            </td>
                                                            <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d') : 'No' }}
                                                            </td>

                                                            <td>
                                                                @if (!$payment->paid_at)
                                                                    <form id="form-{{ $payment->id }}"
                                                                        action="{{ route('finance.pay.installment', $payment->id) }}"
                                                                        method="POST"
                                                                        class="flex gap-1 items-center justify-center">
                                                                        @csrf
                                                                        <input type="number" name="paid_amount"
                                                                            step="0.01" min="{{ $amountToPay }}"
                                                                            class="w-20 px-2 py-1 border rounded text-xs text-right paid-amount bg-gray-200"
                                                                            value="{{ $amountToPay }}"
                                                                            @if ($isLastUnpaid) readonly @endif
                                                                            required>
                                                                    @else
                                                                        <span
                                                                            class="text-gray-500 text-xs">{{ number_format($payment->paid_amount, 2) }}</span>
                                                                @endif
                                                            </td>

                                                            <td>
                                                                @if (!$payment->paid_at)
                                                                    <button type="submit"
                                                                        class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs">Pay</button>
                                                                    </form>
                                                                @else
                                                                    <span class="text-gray-500 text-xs">Paid</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Summary -->
                                        @php
                                            $paidInitialAmount = $totalPaid - $totalOverdue;
                                            $remaining = $order->due_payment - $paidInitialAmount;
                                        @endphp
                                        <div class="text-sm font-bold text-right mt-4 border-t pt-2">
                                            <p>Total Due: <span class="font-bold">LKR
                                                    {{ number_format($order->due_payment, 2) }}</span></p>
                                            <p>Total Paid: <span class="font-bold text-green-600">LKR
                                                    {{ number_format($totalPaid, 2) }}</span></p>
                                            <p>Total Overdue: <span class="font-bold text-orange-500">LKR
                                                    {{ number_format($totalOverdue, 2) }}</span></p>
                                            <p>Remaining Balance: <span class="font-bold text-red-500">LKR
                                                    {{ number_format(max($remaining, 0), 2) }}</span></p>
                                        </div>

                                        <button onclick="closeModal({{ $order->id }})"
                                            class="absolute top-2 right-2 px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">Close</button>
                                    </div>
                                </div>

                                <!-- JS for live update -->
                                <script>
                                    document.addEventListener('input', function(e) {
                                        if (!e.target.classList.contains('overdue-days')) return;

                                        const row = e.target.closest('tr');
                                        const table = e.target.closest('table');
                                        const summaryDiv = e.target.closest('div').querySelector('div.text-sm');

                                        const overdueDays = parseFloat(e.target.value) || 0;
                                        const baseAmount = parseFloat(e.target.dataset.amount) || 0;
                                        const overdueChargePerDay = 200;
                                        const overdueAmount = overdueDays * overdueChargePerDay;
                                        const paidInput = row.querySelector('.paid-amount');
                                        const isLast = paidInput.hasAttribute('readonly');

                                        // Update overdue
                                        const overdueId = e.target.dataset.overdue;
                                        if (overdueId && document.getElementById(overdueId)) {
                                            document.getElementById(overdueId).textContent = overdueAmount.toFixed(2);
                                        }

                                        // Update amount to pay
                                        const targetId = e.target.dataset.target;
                                        if (targetId && document.getElementById(targetId)) {
                                            let newAmount = isLast ? parseFloat(paidInput.value) : baseAmount + overdueAmount;
                                            document.getElementById(targetId).textContent = newAmount.toFixed(2);
                                            if (!isLast) {
                                                paidInput.value = newAmount.toFixed(2);
                                                paidInput.min = newAmount.toFixed(2);
                                            }
                                        }

                                        // Update summary
                                        let totalPaid = 0;
                                        let totalOverdue = 0;
                                        table.querySelectorAll('tr').forEach(r => {
                                            const amt = parseFloat(r.querySelector('.paid-amount')?.value || r.querySelector(
                                                'td:nth-child(8)')?.innerText || 0);
                                            const overdue = parseFloat(r.querySelector('span[id^="overdue-amount-"]')?.innerText || 0);
                                            totalPaid += amt;
                                            totalOverdue += overdue;
                                        });

                                        const duePayment = parseFloat({{ $order->due_payment }});
                                        const remaining = duePayment - (totalPaid - totalOverdue);

                                        if (summaryDiv) {
                                            summaryDiv.querySelector('p:nth-child(2) span').textContent = totalPaid.toFixed(2);
                                            summaryDiv.querySelector('p:nth-child(3) span').textContent = totalOverdue.toFixed(2);
                                            summaryDiv.querySelector('p:nth-child(4) span').textContent = Math.max(remaining, 0).toFixed(2);
                                        }
                                    });
                                </script>
                            @endforeach

                            <!-- Add Finance Modal -->
                            <div id="addFinanceModal"
                                class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center py-5">
                                <div class="w-full max-w-[700px] bg-white rounded-2xl shadow-2xl p-4 transform transition-all scale-95 max-h-[calc(100vh-10rem)] overflow-y-auto"
                                    onclick="event.stopPropagation()">
                                    <div class="max-w-[600px] mx-auto p-8">
                                        <h2 class="text-2xl font-semibold mb-8 text-gray-900 mt-4 text-center">
                                            Add New Finance Order
                                        </h2>

                                        <form action="{{ route('financeOrders.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="space-y-4">

                                                <!-- Item Created Date -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700 ">Item
                                                            Created Date</label>
                                                        <input type="date" name="item_created_date" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700 ">Coordinator
                                                            Name</label>
                                                        <input type="text" name="coordinator_name" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                </div>

                                                <h2 class="text-xl font-semibold mb-8 text-gray-900 mt-4 text-left">
                                                    Buyer Details
                                                </h2>

                                                <!-- Buyer Info -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700 ">Buyer
                                                            Name</label>
                                                        <input type="text" name="buyer_name" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">Buyer
                                                            ID</label>
                                                        <input type="text" name="buyer_id" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Buyer
                                                        Address</label>
                                                    <textarea name="buyer_address" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm"></textarea>
                                                </div>

                                                <div class="flex gap-4">
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">Phone
                                                            1</label>
                                                        <input type="text" name="phone_1" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">Phone
                                                            2</label>
                                                        <input type="text" name="phone_2"
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                </div>

                                                <!-- ID Photo & Electricity Bill Photo -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">ID
                                                            Photo</label>
                                                        <input type="file" name="id_photo" accept="image/*"
                                                            class="w-full mt-1 text-sm">
                                                    </div>
                                                    <div class="w-1/2 mb-8">
                                                        <label class="block text-sm font-medium text-gray-700">House
                                                            Electricity Bill</label>
                                                        <input type="file" name="electricity_bill_photo"
                                                            accept="image/*" class="w-full mt-1 text-sm">
                                                    </div>
                                                </div>

                                                <h2 class="text-xl font-semibold mb-8 text-gray-900 mt-4 text-left">
                                                    Phone Details
                                                </h2>

                                                <!-- Item Details -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700 ">Item
                                                            Name</label>
                                                        <input type="text" name="item_name" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">EMI
                                                            Number</label>
                                                        <input type="text" name="emi_number" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                </div>

                                                <!-- Item Details -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/2">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">Colour</label>
                                                        <input type="text" name="colour" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>

                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">Price
                                                            (LKR)</label>
                                                        <input type="number" name="price" id="price" required
                                                            step="0.01" min="0"
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                            placeholder="Enter amount in LKR">
                                                    </div>
                                                </div>

                                                <div class="flex gap-4 mt-4">
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">Rate
                                                            (%)</label>
                                                        <input type="number" name="rate" id="rate" required
                                                            step="0.01" min="0"
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                            placeholder="Enter rate">
                                                    </div>

                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">Amount of
                                                            Installments</label>
                                                        <input type="number" name="amount_of_installments" required
                                                            min="1"
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                            placeholder="Enter number of installments">
                                                    </div>

                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">Due Payment
                                                            (LKR)</label>
                                                        <!-- Disabled visible field -->
                                                        <input type="number" id="due_payment_display" step="0.01"
                                                            min="0" disabled
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100 cursor-not-allowed"
                                                            placeholder="Auto calculated">

                                                        <!-- Hidden input to submit value -->
                                                        <input type="hidden" name="due_payment" id="due_payment">
                                                    </div>
                                                </div>

                                                <!-- Photos -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/3">
                                                        <label class="block text-sm font-medium text-gray-700">Photo
                                                            1</label>
                                                        <input type="file" name="photo_1" accept="image/*"
                                                            class="w-full mt-1 text-sm">
                                                    </div>
                                                    <div class="w-1/3">
                                                        <label class="block text-sm font-medium text-gray-700">Photo
                                                            2</label>
                                                        <input type="file" name="photo_2" accept="image/*"
                                                            class="w-full mt-1 text-sm">
                                                    </div>
                                                    <div class="w-1/3 mb-8">
                                                        <label class="block text-sm font-medium text-gray-700">Photo
                                                            About</label>
                                                        <input type="file" name="photo_about" accept="image/*"
                                                            class="w-full mt-1 text-sm">
                                                    </div>
                                                </div>

                                                <h2 class="text-xl font-semibold mb-8 text-gray-900 mt-4 text-left">
                                                    Security Details
                                                </h2>

                                                <!-- iCloud Details -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/3">
                                                        <label class="block text-sm font-medium text-gray-700">iCloud
                                                            Mail</label>
                                                        <input type="email" name="icloud_mail" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                    <div class="w-1/3">
                                                        <label class="block text-sm font-medium text-gray-700">iCloud
                                                            Password</label>
                                                        <input type="text" name="icloud_password" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                    <div class="w-1/3">
                                                        <label class="block text-sm font-medium text-gray-700">Screen
                                                            Time Password</label>
                                                        <input type="text" name="screen_lock_password" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                </div>

                                            </div>

                                            <!-- Buttons -->
                                            <div class="flex justify-end gap-3 mt-6">
                                                <button type="button"
                                                    onclick="document.getElementById('addFinanceModal').classList.add('hidden')"
                                                    class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">
                                                    Cancel
                                                </button>
                                                <button type="submit"
                                                    class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                                    Create Finance Order
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFilterForm() {
            const form = document.getElementById('filterFormContainerFinance');
            form.classList.toggle('hidden');
        }

        function toggleCalForm() {
            const form = document.getElementById('pricingCalculatorContainer');
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
    <script>
        function printInvoice(orderId) {
            const url = `/finance/invoice/${orderId}`;
            const printWindow = window.open(url, '_blank', 'width=800,height=600');
            printWindow.focus();
            printWindow.print();
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const priceInput = document.getElementById('price');
            const rateInput = document.getElementById('rate');
            const dueDisplay = document.getElementById('due_payment_display');
            const dueHidden = document.getElementById('due_payment');

            function calculateDue() {
                const price = parseFloat(priceInput.value) || 0;
                const rate = parseFloat(rateInput.value) || 0;

                // Calculate due payment = price + (price * rate / 100)
                const due = price + (price * rate / 100);
                dueDisplay.value = due.toFixed(2);
                dueHidden.value = due.toFixed(2);
            }

            // Listen for input changes
            [priceInput, rateInput].forEach(input => {
                input.addEventListener('input', calculateDue);
            });
        });
    </script>

    <script>
        function openModal(orderId) {
            document.getElementById(`payModal-${orderId}`).classList.remove('hidden');
            document.getElementById(`payModal-${orderId}`).classList.add('flex');
        }

        function closeModal(orderId) {
            document.getElementById(`payModal-${orderId}`).classList.add('hidden');
            document.getElementById(`payModal-${orderId}`).classList.remove('flex');
        }
    </script>
@endsection
