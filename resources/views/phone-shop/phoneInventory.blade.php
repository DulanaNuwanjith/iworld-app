@php use Carbon\Carbon; @endphp

<head>

    <!-- Import Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title> Iworld-Inventory </title>
</head>
<div class="flex h-full w-full bg-white">
    @extends('layouts.inventory')

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
                            </div>

                            <div id="filterFormContainerInventory" class="mt-4 hidden">
                                <form id="filterFormInventory" method="GET" action="{{ route('inventory.index') }}"
                                    class="mb-6 flex gap-6 items-center flex-wrap">

                                    {{-- Phone Type --}}
                                    <div class="relative inline-block text-left w-48">
                                        <label for="phoneTypeDropdown"
                                            class="block text-sm font-medium text-gray-700 mb-1">Phone Type</label>
                                        <input type="hidden" name="phone_type" id="phoneTypeInput"
                                            value="{{ request('phone_type') }}">
                                        <button id="phoneTypeDropdown" type="button"
                                            class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                            onclick="togglePhoneTypeDropdown(event)">
                                            <span
                                                id="selectedPhoneType">{{ request('phone_type') ?? 'Select Phone Type' }}</span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div id="phoneTypeDropdownMenu"
                                            class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                            <input type="text" id="phoneTypeSearch" onkeyup="filterPhoneTypes()"
                                                placeholder="Search..." class="w-full px-2 py-1 text-sm border rounded-md"
                                                autocomplete="off">
                                            <div onclick="selectPhoneType('')"
                                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All Phone Types
                                            </div>
                                            @foreach ($phoneTypes as $type)
                                                <div onclick="selectPhoneType('{{ $type }}')"
                                                    class="phone-type-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                    {{ $type }}</div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- EMI --}}
                                    <div class="relative inline-block text-left w-48">
                                        <label for="emiDropdown"
                                            class="block text-sm font-medium text-gray-700 mb-1">EMI</label>
                                        <input type="hidden" name="emi" id="emiInput" value="{{ request('emi') }}">
                                        <button id="emiDropdown" type="button"
                                            class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                            onclick="toggleEmiDropdown(event)">
                                            <span id="selectedEmi">{{ request('emi') ?? 'Select EMI' }}</span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div id="emiDropdownMenu"
                                            class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                            <input type="text" id="emiSearch" onkeyup="filterEmis()"
                                                placeholder="Search..." class="w-full px-2 py-1 text-sm border rounded-md"
                                                autocomplete="off">
                                            <div onclick="selectEmi('')"
                                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All EMIs</div>
                                            @foreach ($emis as $emi)
                                                <div onclick="selectEmi('{{ $emi }}')"
                                                    class="emi-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                    {{ $emi }}</div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Supplier --}}
                                    <div class="relative inline-block text-left w-48">
                                        <label for="supplierDropdown"
                                            class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                                        <input type="hidden" name="supplier" id="supplierInput"
                                            value="{{ request('supplier') }}">
                                        <button id="supplierDropdown" type="button"
                                            class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                            onclick="toggleSupplierDropdown(event)">
                                            <span
                                                id="selectedSupplier">{{ request('supplier') ?? 'Select Supplier' }}</span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div id="supplierDropdownMenu"
                                            class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                            <input type="text" id="supplierSearch" onkeyup="filterSuppliers()"
                                                placeholder="Search..." class="w-full px-2 py-1 text-sm border rounded-md"
                                                autocomplete="off">
                                            <div onclick="selectSupplier('')"
                                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All Suppliers
                                            </div>
                                            @foreach ($suppliers as $supplier)
                                                <div onclick="selectSupplier('{{ $supplier }}')"
                                                    class="supplier-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                    {{ $supplier }}</div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Stock Type --}}
                                    <div class="relative inline-block text-left w-48">
                                        <label for="stockTypeDropdown"
                                            class="block text-sm font-medium text-gray-700 mb-1">Stock Type</label>
                                        <input type="hidden" name="stock_type" id="stockTypeInput"
                                            value="{{ request('stock_type') }}">
                                        <button id="stockTypeDropdown" type="button"
                                            class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                            onclick="toggleStockTypeDropdown(event)">
                                            <span
                                                id="selectedStockType">{{ request('stock_type') ?? 'Select Stock Type' }}</span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div id="stockTypeDropdownMenu"
                                            class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                            <input type="text" id="stockTypeSearch" onkeyup="filterStockTypes()"
                                                placeholder="Search..." class="w-full px-2 py-1 text-sm border rounded-md"
                                                autocomplete="off">
                                            <div onclick="selectStockType('')"
                                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All Stock Types
                                            </div>
                                            <div onclick="selectStockType('Direct Import')"
                                                class="stock-type-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                Direct Import</div>
                                            <div onclick="selectStockType('Exchange')"
                                                class="stock-type-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                Exchange</div>
                                        </div>
                                    </div>

                                    {{-- Created Date --}}
                                    <div class="inline-block text-left w-48">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Created Date</label>
                                        <input type="date" name="date" value="{{ request('date') }}"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm">
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="flex items-end space-x-2 mt-2">
                                        <button type="submit"
                                            class="mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Apply
                                            Filters</button>
                                        <button type="button" onclick="clearFiltersInventory()"
                                            class="mt-4 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Clear</button>
                                    </div>
                                </form>
                            </div>

                            <div class="flex-1">

                                <div class="flex justify-between items-center mb-6">
                                    <h1 class="text-2xl font-bold text-gray-800">Phone Inventory Records
                                    </h1>

                                    <div class="flex space-x-3">

                                        <button
                                            onclick="document.getElementById('addPhoneModal').classList.remove('hidden')"
                                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow">
                                            + Add Phone Stocks
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Phone Stock Modal -->
                            <div id="addPhoneModal"
                                class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center py-5">
                                <div class="w-full max-w-[700px] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-4 transform transition-all scale-95 max-h-[calc(100vh-10rem)] overflow-y-auto"
                                    onclick="event.stopPropagation()">
                                    <div class="max-w-[600px] mx-auto p-8">

                                        <h2 class="text-2xl font-semibold mb-8 text-gray-900 mt-4 text-center">
                                            Add Phone Stock
                                        </h2>

                                        <!-- Unified Form -->
                                        <form id="unifiedOrderForm" action="{{ route('inventory.store') }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf

                                            <div id="itemsContainer"></div>

                                            <button type="button" id="addItemBtn"
                                                class="mt-4 px-4 py-2 bg-green-500 text-white rounded text-sm">
                                                + Add Phone
                                            </button>

                                            <!-- MASTER FIELDS -->
                                            <div class="grid grid-cols-2 gap-4 mt-3">
                                                <div class="mt-6">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                                        Stock Type
                                                    </label>
                                                    <div class="relative w-full text-left">
                                                        <button type="button"
                                                            class="dropdown-btn inline-flex justify-between w-full rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                                            onclick="toggleDropdownStock(this)">
                                                            <span class="selected-stock">Select Stock Type</span>
                                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                                fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </button>

                                                        <div
                                                            class="dropdown-menu-stock hidden absolute z-10 mt-2 w-full rounded-md bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black/5 max-h-48 overflow-y-auto">
                                                            <div class="py-1 options-container flex flex-col"
                                                                role="listbox" tabindex="-1">
                                                                <button type="button"
                                                                    class="dropdown-option w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600"
                                                                    onclick="selectDropdownStock(this, 'Direct Import Used')">Direct
                                                                    Import Used</button>
                                                                <button type="button"
                                                                    class="dropdown-option w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600"
                                                                    onclick="selectDropdownStock(this, 'Direct Import Brand New')">Direct
                                                                    Import Brand New</button>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="stock_type" class="input-stock"
                                                            value="">
                                                    </div>
                                                </div>

                                                <div class="mt-6">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Date
                                                    </label>
                                                    <input type="date" name="date" required
                                                        class="w-full mt-1 px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white text-sm">
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Supplier
                                                </label>
                                                <input type="text" name="supplier" required
                                                    class="w-full mt-1 px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white text-sm">
                                            </div>

                                            <!-- ACTIONS -->
                                            <div class="flex justify-end mt-6 space-x-3">
                                                <button type="button"
                                                    onclick="document.getElementById('addPhoneModal').classList.add('hidden')"
                                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded hover:bg-gray-300">
                                                    Cancel
                                                </button>

                                                <button type="submit" id="createPhoneBtn"
                                                    class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                                    Save Stock
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Main Table --}}
                            <div id="sampleInquiryRecordsScroll" class="overflow-x-auto bg-white shadow rounded-lg">
                                <!-- Spinner -->
                                <div id="pageLoadingSpinner"
                                    class="fixed inset-0 z-50 bg-white bg-opacity-80 flex flex-col items-center justify-center">
                                    <svg class="animate-spin h-10 w-10 text-gray-600" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4">
                                        </circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    <p class="mt-3 text-gray-700 font-semibold">Loading data...</p>
                                </div>
                                <table class="table-fixed w-full text-sm divide-y divide-gray-200">
                                    <thead class="bg-gray-200 text-left">
                                        <tr class="text-center">
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-48 text-xs text-gray-600 uppercase break-words">
                                                EMI Number
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase break-words">
                                                Model Details
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-40 text-xs text-gray-600 uppercase break-words">
                                                Supplier
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase break-words">
                                                Stock Type
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-40 text-xs text-gray-600 uppercase break-words">
                                                Cost
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-52 text-xs text-gray-600 uppercase break-words">
                                                Status
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase break-words">
                                                Note
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase break-words">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($inventories as $inventory)
                                            <tr class="text-center">
                                                <td class="px-4 py-2">
                                                    <span class="font-semibold">{{ $inventory->emi }}</span> <br>
                                                    <span
                                                        class="text-xs text-gray-500">{{ $inventory->date->format('d M Y') }}</span>
                                                </td>
                                                <td class="px-4 py-2 text-left">
                                                    <span class="font-semibold">Model:</span>
                                                    {{ $inventory->phone_type }}<br>
                                                    <span class="font-semibold">Capacity:</span>
                                                    {{ $inventory->capacity }}<br>
                                                    <span class="font-semibold">Colour:</span> {{ $inventory->colour }}
                                                </td>
                                                <td class="px-4 py-2 text-left">{{ $inventory->supplier }}<br>
                                                    @if ($inventory->stock_type === 'Exchange' && $inventory->supplier_id_front)
                                                        <button
                                                            onclick="openSupplierIdModal(
            '{{ asset('storage/' . $inventory->supplier_id_front) }}',
            '{{ asset('storage/' . $inventory->supplier_id_back) }}'
        )"
                                                            class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                                            Supplier ID
                                                        </button>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2">{{ $inventory->stock_type }}</td>
                                                <td class="px-4 py-2 text-center">
                                                    LKR: {{ number_format($inventory->cost, 2) }}

                                                    @if ($inventory->repairs->count())
                                                        <div class="mt-1">
                                                            <button type="button"
                                                                class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600"
                                                                onclick="openRepairDetailsModal({{ $inventory->id }})">
                                                                Repair Details
                                                            </button>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div x-data="{ status: '{{ $inventory->status_availability }}' }"
                                                        class="flex flex-col items-center gap-1 w-full">

                                                        <!-- Status Dropdown -->
                                                        <form action="{{ route('inventory.updateStatusAvailability') }}"
                                                            method="POST" class="w-full">
                                                            @csrf
                                                            <input type="hidden" name="inventory_id"
                                                                value="{{ $inventory->id }}">

                                                            <div class="relative w-full">
                                                                <select name="status_availability" x-model="status"
                                                                    @change="if(status === 'with_person'){ openPersonNameModal({{ $inventory->id }}, '{{ $inventory->person_name ?? '' }}') } else { $el.form.submit() }"
                                                                    :class="{
                                                                        'bg-green-100 text-green-800 border-green-400': status === 'in_stock',
                                                                        'bg-red-100 text-red-800 border-red-400': status === 'in_repair',
                                                                        'bg-blue-100 text-blue-800 border-blue-400': status === 'with_person',
                                                                    }"
                                                                    class="appearance-none w-full text-sm font-medium rounded-md px-3 py-2
                               border shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1
                               focus:ring-opacity-50 pr-8 transition-colors duration-200 hover:brightness-95">
                                                                    <option value="in_stock">✅ In Stock</option>
                                                                    <option value="in_repair">🛠 In Repair</option>
                                                                    <option value="with_person">👤 With Another Person
                                                                    </option>
                                                                </select>

                                                                <!-- Custom arrow -->
                                                                <div
                                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                                    <svg class="h-4 w-4"
                                                                        :class="{
                                                                            'text-green-800': status === 'in_stock',
                                                                            'text-red-800': status === 'in_repair',
                                                                            'text-blue-800': status === 'with_person',
                                                                        }"
                                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M19 9l-7 7-7-7" />
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </form>

                                                        <!-- Person Name Display -->
                                                        <template
                                                            x-if="status === 'with_person' && '{{ $inventory->person_name }}'">
                                                            <span
                                                                class="mt-1 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full
                         font-medium italic truncate max-w-[140px]"
                                                                title="{{ $inventory->person_name }}">
                                                                {{ $inventory->person_name }}
                                                            </span>
                                                        </template>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-2 text-left">{{ $inventory->note ?? '-' }}</td>
                                                <td class="px-4 py-2">
                                                    <div class="inline-flex items-center justify-center gap-2">

                                                        <!-- Repair Button -->
                                                        <button type="button"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm"
                                                            onclick="openRepairModal({{ $inventory->id }})">
                                                            Repair
                                                        </button>

                                                        <!-- Delete Button -->
                                                        <form id="delete-form-{{ $inventory->id }}"
                                                            action="{{ route('inventory.destroy', $inventory->id) }}"
                                                            method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm mt-3"
                                                                onclick="confirmDelete('{{ $inventory->id }}')">
                                                                Delete
                                                            </button>
                                                        </form>

                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="px-4 py-2 text-center text-gray-500">No
                                                    inventory records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="py-6 flex justify-center">
                                {{ $inventories->links() }}
                            </div>

                            <div id="personNameModal"
                                class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center px-4 py-6">
                                <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                                    <h3 class="text-lg font-semibold mb-4">Enter Person Name</h3>
                                    <form id="personNameForm" method="POST"
                                        action="{{ route('inventory.updateStatusAvailability') }}">
                                        @csrf
                                        <input type="hidden" name="inventory_id" id="personModalInventoryId">
                                        <input type="hidden" name="status_availability" value="with_person">

                                        <input type="text" name="person_name" id="personModalName"
                                            class="w-full border p-2 rounded mb-4" placeholder="Person Name" required>

                                        <div class="flex justify-end gap-2">
                                            <button type="button" onclick="closePersonNameModal()"
                                                class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancel</button>
                                            <button type="submit"
                                                class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <script>
                                function openPersonNameModal(id, name = '') {
                                    document.getElementById('personModalInventoryId').value = id;
                                    document.getElementById('personModalName').value = name;
                                    document.getElementById('personNameModal').classList.remove('hidden');
                                }

                                function closePersonNameModal() {
                                    document.getElementById('personNameModal').classList.add('hidden');
                                }
                            </script>

                            <!-- Supplier ID Modal -->
                            <div id="supplierIdModal"
                                class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">

                                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl p-6 max-w-2xl w-full relative">
                                    <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white text-center">
                                        Supplier ID Details
                                    </h2>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600 mb-2 text-center">Front Side</p>
                                            <img id="supplierIdFrontImg"
                                                class="w-full h-64 object-contain border rounded">
                                        </div>

                                        <div>
                                            <p class="text-sm text-gray-600 mb-2 text-center">Back Side</p>
                                            <img id="supplierIdBackImg" class="w-full h-64 object-contain border rounded">
                                        </div>
                                    </div>

                                    <div class="flex justify-end mt-6">
                                        <button onclick="closeSupplierIdModal()"
                                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function openSupplierIdModal(frontUrl, backUrl) {
                                    document.getElementById('supplierIdFrontImg').src = frontUrl;
                                    document.getElementById('supplierIdBackImg').src = backUrl;
                                    document.getElementById('supplierIdModal').classList.remove('hidden');
                                }

                                function closeSupplierIdModal() {
                                    document.getElementById('supplierIdModal').classList.add('hidden');
                                    document.getElementById('supplierIdFrontImg').src = '';
                                    document.getElementById('supplierIdBackImg').src = '';
                                }
                            </script>

                            <div id="repairModal"
                                class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50">
                                <div class="bg-white rounded-lg shadow-lg w-96 p-6 relative">
                                    <h2 class="text-lg font-semibold mb-4">Add Repair Info</h2>

                                    <form id="repairForm" method="POST">
                                        @csrf

                                        <div class="mb-3">
                                            <label class="block text-sm font-medium">Repair Reason</label>
                                            <input type="text" name="repair_reason"
                                                class="border rounded w-full px-3 py-2"
                                                placeholder="Eg: Display replacement" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="block text-sm font-medium">Repair Cost</label>
                                            <input type="number" step="0.01" name="repair_cost"
                                                class="border rounded w-full px-3 py-2" placeholder="0.00" required>
                                        </div>

                                        <div class="flex justify-end gap-2 mt-4">
                                            <button type="button" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400"
                                                onclick="closeRepairModal()">
                                                Cancel
                                            </button>

                                            <button type="submit"
                                                class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                                                Save Repair
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div id="repairDetailsModal"
                                class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50">
                                <div class="bg-white rounded-lg shadow-lg w-3/4 max-w-3xl p-6 relative">
                                    <h2 class="text-xl font-bold mb-4 text-gray-700">Repair Details</h2>

                                    <div id="repairDetailsContent" class="space-y-3">
                                        <!-- Dynamic content injected here -->
                                    </div>

                                    <div class="flex justify-end mt-4">
                                        <button type="button"
                                            class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 font-semibold"
                                            onclick="closeRepairDetailsModal()">Close</button>
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
            const form = document.getElementById('filterFormContainerInventory');
            form.classList.toggle('hidden');
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('#addPhoneModal form');
            const submitBtn = document.getElementById('createPhoneBtn');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Submitting...';
            });
        });
    </script>

    <script>
        let itemIndex = 0;

        // Array of iPhone models
        const iPhoneModels = [
            'iPhone 4', 'iPhone 4S', 'iPhone 5', 'iPhone 5C', 'iPhone 5S',
            'iPhone 6', 'iPhone 6 Plus', 'iPhone 6S', 'iPhone 6S Plus',
            'iPhone SE (1st generation)', 'iPhone 7', 'iPhone 7 Plus',
            'iPhone 8', 'iPhone 8 Plus', 'iPhone X', 'iPhone XR', 'iPhone XS', 'iPhone XS Max',
            'iPhone 11', 'iPhone 11 Pro', 'iPhone 11 Pro Max',
            'iPhone 12', 'iPhone 12 Mini', 'iPhone 12 Pro', 'iPhone 12 Pro Max',
            'iPhone 13', 'iPhone 13 Mini', 'iPhone 13 Pro', 'iPhone 13 Pro Max',
            'iPhone 14', 'iPhone 14 Plus', 'iPhone 14 Pro', 'iPhone 14 Pro Max',
            'iPhone 15', 'iPhone 15 Plus', 'iPhone 15 Pro', 'iPhone 15 Pro Max',
            'iPhone 16', 'iPhone 16 Plus', 'iPhone 16 Pro', 'iPhone 16 Pro Max',
            'iPhone 17', 'iPhone 17 Plus', 'iPhone 17 Pro', 'iPhone 17 Pro Max'
        ];

        // Array of Capacities
        const iPhoneCapacities = ['64GB', '128GB', '256GB', '512GB', '1TB'];

        document.addEventListener('DOMContentLoaded', () => {
            const addBtn = document.getElementById('addItemBtn');
            if (addBtn) addBtn.addEventListener('click', addItem);

            // Keep at least one item on page load
            addItem();
        });

        /* ---------- ITEMS ---------- */
        function addItem() {
            const container = document.getElementById('itemsContainer');

            // Build phone options dynamically
            const phoneOptionsHTML = iPhoneModels.map(model =>
                `<button type="button" class="dropdown-option w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600" onclick="selectDropdownPhone(this, '${model}')">${model}</button>`
            ).join('');

            // Build capacity options dynamically
            const capacityOptionsHTML = iPhoneCapacities.map(cap =>
                `<button type="button" class="dropdown-option w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600" onclick="selectDropdownCapacity(this, '${cap}')">${cap}</button>`
            ).join('');

            const phoneDropdownHTML = `
<div class="relative w-full text-left">
    <label class="block text-sm font-medium mb-1">Phone Type</label>
    <button type="button"
        class="dropdown-btn inline-flex justify-between w-full rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
        onclick="toggleDropdownPhone(this)">
        <span class="selected-phone">Select Phone Type</span>
        <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                clip-rule="evenodd" />
        </svg>
    </button>

    <div class="dropdown-menu-phone hidden absolute z-10 mt-2 w-full rounded-md bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black/5 max-h-48 overflow-y-auto">
        <div class="p-2 sticky top-0 bg-white dark:bg-gray-700 z-10">
            <input type="text"
                class="search-phone w-full px-2 py-1 text-sm border rounded-md dark:bg-gray-600 dark:text-white dark:placeholder-gray-300"
                placeholder="Search iPhone..." onkeyup="filterPhoneOptions(this)">
        </div>
        <div class="py-1 options-container flex flex-col" role="listbox" tabindex="-1">
            <button type="button" class="dropdown-option w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600" onclick="selectDropdownPhone(this, '')">None</button>
            ${phoneOptionsHTML}
        </div>
    </div>

    <input type="hidden" name="items[${itemIndex}][phone_type]" class="input-phone" value="">
</div>
`;

            const capacityDropdownHTML = `
<div class="relative w-full text-left">
    <label class="block text-sm font-medium mb-1">Capacity</label>
    <button type="button"
        class="dropdown-btn inline-flex justify-between w-full rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
        onclick="toggleDropdownCapacity(this)">
        <span class="selected-capacity">Select Capacity</span>
        <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                clip-rule="evenodd" />
        </svg>
    </button>

    <div class="dropdown-menu-capacity hidden absolute z-10 mt-2 w-full rounded-md bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black/5 max-h-48 overflow-y-auto">
        <div class="p-2 sticky top-0 bg-white dark:bg-gray-700 z-10">
            <input type="text"
                class="search-capacity w-full px-2 py-1 text-sm border rounded-md dark:bg-gray-600 dark:text-white dark:placeholder-gray-300"
                placeholder="Search Capacity..." onkeyup="filterCapacityOptions(this)">
        </div>
        <div class="py-1 options-container flex flex-col" role="listbox" tabindex="-1">
            <button type="button" class="dropdown-option w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600" onclick="selectDropdownCapacity(this, '')">None</button>
            ${capacityOptionsHTML}
        </div>
    </div>

    <input type="hidden" name="items[${itemIndex}][capacity]" class="input-capacity" value="">
</div>
`;

            const itemHTML = `
<div class="item-group border rounded-md p-4 mb-4 bg-gray-50" data-index="${itemIndex}">
    ${phoneDropdownHTML}

    <div class="grid grid-cols-2 gap-4 mt-3">
        <div>
            <label class="block text-sm font-medium">Colour</label>
            <input type="text" name="items[${itemIndex}][colour]"
                class="w-full mt-1 px-3 py-2 border rounded-md text-sm">
        </div>
        <div>
            ${capacityDropdownHTML}
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mt-3">
        <div>
            <label class="block text-sm font-medium">EMI</label>
            <input type="text" name="items[${itemIndex}][emi]"
                class="w-full mt-1 px-3 py-2 border rounded-md text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Cost</label>
            <input type="number" step="0.01" name="items[${itemIndex}][cost]"
                class="w-full mt-1 px-3 py-2 border rounded-md text-sm">
        </div>
    </div>

    <div class="mt-3">
        <label class="block text-sm font-medium">Note</label>
        <textarea name="items[${itemIndex}][note]"
            class="w-full mt-1 px-3 py-2 border rounded-md text-sm"
            rows="2" placeholder="Enter note here"></textarea>
    </div>

    <div class="flex justify-end mt-4">
        <button type="button" onclick="removeItem(this)"
            class="px-3 py-1 bg-red-500 text-white rounded text-sm">
            Remove
        </button>
    </div>
</div>
`;

            container.insertAdjacentHTML('beforeend', itemHTML);
            itemIndex++;
        }

        // Remove item function (keeps at least one)
        function removeItem(button) {
            const container = document.getElementById('itemsContainer');
            const allItems = container.querySelectorAll('.item-group');

            if (allItems.length > 1) {
                button.closest('.item-group').remove();
            } else {
                // SweetAlert2 toast for warning
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'You must keep at least one item.',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'swal2-toast swal2-shadow'
                    },
                    iconColor: '#6c757d'
                });
            }
        }

        /* ---------- DROPDOWN FUNCTIONS ---------- */
        function toggleDropdownPhone(button) {
            const dropdownMenu = button.nextElementSibling;
            document.querySelectorAll('.dropdown-menu-phone').forEach(menu => {
                if (menu !== dropdownMenu) menu.classList.add('hidden');
            });
            dropdownMenu.classList.toggle('hidden');
        }

        function selectDropdownPhone(button, value) {
            const dropdown = button.closest('.relative');
            dropdown.querySelector('.selected-phone').innerText = value || 'None';
            dropdown.querySelector('.input-phone').value = value;
            dropdown.querySelector('.dropdown-menu-phone').classList.add('hidden');
        }

        function filterPhoneOptions(input) {
            const filter = input.value.toLowerCase();
            const container = input.closest('.dropdown-menu-phone').querySelector('.options-container');
            Array.from(container.querySelectorAll('.dropdown-option')).forEach(option => {
                option.style.display = option.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        }

        function toggleDropdownCapacity(button) {
            const dropdownMenu = button.nextElementSibling;
            document.querySelectorAll('.dropdown-menu-capacity').forEach(menu => {
                if (menu !== dropdownMenu) menu.classList.add('hidden');
            });
            dropdownMenu.classList.toggle('hidden');
        }

        function selectDropdownCapacity(button, value) {
            const dropdown = button.closest('.relative');
            dropdown.querySelector('.selected-capacity').innerText = value || 'None';
            dropdown.querySelector('.input-capacity').value = value;
            dropdown.querySelector('.dropdown-menu-capacity').classList.add('hidden');
        }

        function filterCapacityOptions(input) {
            const filter = input.value.toLowerCase();
            const container = input.closest('.dropdown-menu-capacity').querySelector('.options-container');
            Array.from(container.querySelectorAll('.dropdown-option')).forEach(option => {
                option.style.display = option.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        }
    </script>

    <script>
        function toggleDropdownStock(button) {
            const dropdownMenu = button.nextElementSibling;

            // Hide other dropdowns
            document.querySelectorAll('.dropdown-menu-stock').forEach(menu => {
                if (menu !== dropdownMenu) menu.classList.add('hidden');
            });

            dropdownMenu.classList.toggle('hidden');
        }

        function selectDropdownStock(button, value) {
            const dropdown = button.closest('.relative');

            // Update selected text and hidden input
            dropdown.querySelector('.selected-stock').innerText = value;
            dropdown.querySelector('.input-stock').value = value;

            // Hide the dropdown menu
            dropdown.querySelector('.dropdown-menu-stock').classList.add('hidden');
        }
    </script>

    <script>
        function toggleDropdownPhone(button) {
            const dropdownMenu = button.nextElementSibling;
            document.querySelectorAll('.dropdown-menu-phone').forEach(menu => {
                if (menu !== dropdownMenu) menu.classList.add('hidden');
            });
            dropdownMenu.classList.toggle('hidden');
        }

        function selectDropdownPhone(button, value) {
            const dropdown = button.closest('.relative');
            dropdown.querySelector('.selected-phone').innerText = value || 'None';
            dropdown.querySelector('.input-phone').value = value;
            dropdown.querySelector('.dropdown-menu-phone').classList.add('hidden');
        }

        document.addEventListener('click', function(event) {
            document.querySelectorAll('.dropdown-menu-phone').forEach(menu => {
                if (!menu.contains(event.target) && !menu.previousElementSibling.contains(event.target)) {
                    menu.classList.add('hidden');
                }
            });
        });

        function filterPhoneOptions(input) {
            const filter = input.value.toLowerCase();
            const dropdownMenu = input.closest('.dropdown-menu-phone');
            const container = dropdownMenu.querySelector('.options-container');

            Array.from(container.querySelectorAll('.dropdown-option')).forEach(option => {
                option.style.display = option.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        }
    </script>
    <script>
        function toggleDropdownStock(button) {
            const dropdownMenu = button.nextElementSibling;
            // Hide other dropdowns
            document.querySelectorAll('.dropdown-menu-stock').forEach(menu => {
                if (menu !== dropdownMenu) menu.classList.add('hidden');
            });
            // Toggle this dropdown
            dropdownMenu.classList.toggle('hidden');
        }

        function selectDropdownStock(button, value) {
            const dropdown = button.closest('.relative');
            // Update selected text and hidden input
            dropdown.querySelector('.selected-stock').innerText = value;
            dropdown.querySelector('.input-stock').value = value;

            // Hide the dropdown menu
            dropdown.querySelector('.dropdown-menu-stock').classList.add('hidden');

            // Show/hide Supplier ID upload section
            const supplierIdDiv = document.getElementById('supplierIdUpload');
            if (value === 'Exchange') {
                supplierIdDiv.classList.remove('hidden');
            } else {
                supplierIdDiv.classList.add('hidden');
                // Clear file inputs when hiding
                supplierIdDiv.querySelectorAll('input[type="file"]').forEach(input => input.value = '');
            }
        }
    </script>

    <script>
        function openRepairModal(inventoryId) {
            const modal = document.getElementById('repairModal');
            const form = document.getElementById('repairForm');

            // Set correct action dynamically
            form.action = `/inventory/${inventoryId}/repair`;

            // Reset previous values (VERY IMPORTANT)
            form.reset();

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRepairModal() {
            const modal = document.getElementById('repairModal');
            const form = document.getElementById('repairForm');

            form.reset();

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Optional: close modal when clicking outside
        document.getElementById('repairModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRepairModal();
            }
        });
    </script>
    <script>
        function openRepairDetailsModal(inventoryId) {
            const modal = document.getElementById('repairDetailsModal');
            const contentDiv = document.getElementById('repairDetailsContent');
            contentDiv.innerHTML = '';

            fetch(`/inventory/${inventoryId}/repairs`)
                .then(res => res.json())
                .then(data => {
                    if (data.repairs.length === 0) {
                        contentDiv.innerHTML = '<p class="text-gray-500 italic">No repair records found.</p>';
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        return;
                    }

                    // Header
                    let html = `
                <div class="p-2 rounded bg-gray-100">
                    <div><span class="font-semibold text-gray-700">EMI:</span> ${data.emi}</div>
                    <div><span class="font-semibold text-gray-700">Original Cost:</span> 
                        <span class="text-green-600">LKR ${parseFloat(data.original_cost).toFixed(2)}</span>
                    </div>
                </div>
            `;

                    // Repairs table
                    html += `<div class="mt-2 border rounded shadow-sm overflow-hidden">
                        <div class="bg-gray-50 p-2 font-semibold text-gray-700">Repairs</div>`;

                    let totalRepairCost = 0;
                    data.repairs.forEach((r, i) => {
                        totalRepairCost += parseFloat(r.repair_cost);
                        html += `
                    <div class="p-2 border-t hover:bg-gray-50">
                        <div><span class="font-semibold text-gray-700">Repair ${i + 1}:</span></div>
                        <div><span class="text-gray-600">Reason:</span> <span class="text-blue-600 font-medium">${r.repair_reason}</span></div>
                        <div><span class="text-gray-600">Cost:</span> <span class="text-red-600 font-bold">LKR ${parseFloat(r.repair_cost).toFixed(2)}</span></div>
                        <div><span class="text-gray-600">Updated At:</span> <span class="text-gray-600">${r.updated_at}</span></div>
                    </div>
                `;
                    });

                    html += `
                <div class="p-2 bg-gray-50 border-t font-semibold">
                    Total Repair Cost: <span class="text-red-600">LKR ${totalRepairCost.toFixed(2)}</span>
                </div>
                <div class="p-2 bg-green-50 font-semibold border-t">
                    Total Cost After Repairs: <span class="text-green-700">LKR ${parseFloat(data.total_cost).toFixed(2)}</span>
                </div>
            `;

                    html += `</div>`;

                    contentDiv.innerHTML = html;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                })
                .catch(err => console.error(err));
        }

        function closeRepairDetailsModal() {
            const modal = document.getElementById('repairDetailsModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // ---- PHONE TYPE ----
            const phoneTypeMenu = document.getElementById("phoneTypeDropdownMenu");
            phoneTypeMenu.addEventListener("click", e => e.stopPropagation());
            window.togglePhoneTypeDropdown = function(e) {
                e.stopPropagation();
                closeAllDropdowns();
                phoneTypeMenu.classList.toggle("hidden");
            }
            window.selectPhoneType = function(val) {
                document.getElementById("phoneTypeInput").value = val;
                document.getElementById("selectedPhoneType").textContent = val || "Select Phone Type";
                closeAllDropdowns();
            }
            window.filterPhoneTypes = function() {
                const f = document.getElementById("phoneTypeSearch").value.toLowerCase();
                document.querySelectorAll(".phone-type-option").forEach(o => o.style.display = o.textContent
                    .toLowerCase().includes(f) ? "block" : "none");
            }

            // ---- EMI ----
            const emiMenu = document.getElementById("emiDropdownMenu");
            emiMenu.addEventListener("click", e => e.stopPropagation());
            window.toggleEmiDropdown = function(e) {
                e.stopPropagation();
                closeAllDropdowns();
                emiMenu.classList.toggle("hidden");
            }
            window.selectEmi = function(val) {
                document.getElementById("emiInput").value = val;
                document.getElementById("selectedEmi").textContent = val || "Select EMI";
                closeAllDropdowns();
            }
            window.filterEmis = function() {
                const f = document.getElementById("emiSearch").value.toLowerCase();
                document.querySelectorAll(".emi-option").forEach(o => o.style.display = o.textContent
                    .toLowerCase().includes(f) ? "block" : "none");
            }

            // ---- SUPPLIER ----
            const supplierMenu = document.getElementById("supplierDropdownMenu");
            supplierMenu.addEventListener("click", e => e.stopPropagation());
            window.toggleSupplierDropdown = function(e) {
                e.stopPropagation();
                closeAllDropdowns();
                supplierMenu.classList.toggle("hidden");
            }
            window.selectSupplier = function(val) {
                document.getElementById("supplierInput").value = val;
                document.getElementById("selectedSupplier").textContent = val || "Select Supplier";
                closeAllDropdowns();
            }
            window.filterSuppliers = function() {
                const f = document.getElementById("supplierSearch").value.toLowerCase();
                document.querySelectorAll(".supplier-option").forEach(o => o.style.display = o.textContent
                    .toLowerCase().includes(f) ? "block" : "none");
            }

            // ---- STOCK TYPE ----
            const stockMenu = document.getElementById("stockTypeDropdownMenu");
            stockMenu.addEventListener("click", e => e.stopPropagation());
            window.toggleStockTypeDropdown = function(e) {
                e.stopPropagation();
                closeAllDropdowns();
                stockMenu.classList.toggle("hidden");
            }
            window.selectStockType = function(val) {
                document.getElementById("stockTypeInput").value = val;
                document.getElementById("selectedStockType").textContent = val || "Select Stock Type";
                closeAllDropdowns();
            }
            window.filterStockTypes = function() {
                const f = document.getElementById("stockTypeSearch").value.toLowerCase();
                document.querySelectorAll(".stock-type-option").forEach(o => o.style.display = o.textContent
                    .toLowerCase().includes(f) ? "block" : "none");
            }

            // ---- Close dropdowns on outside click ----
            function closeAllDropdowns() {
                phoneTypeMenu.classList.add("hidden");
                emiMenu.classList.add("hidden");
                supplierMenu.classList.add("hidden");
                stockMenu.classList.add("hidden");
            }

            document.addEventListener("click", closeAllDropdowns);

            window.clearFiltersInventory = function() {
                window.location.href = window.location.pathname;
            }

        });
    </script>
@endsection
