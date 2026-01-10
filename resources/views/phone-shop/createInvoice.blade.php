@php use Carbon\Carbon; @endphp

<head>

    <!-- Import Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title> Iworld-Invoice </title>
</head>
<div class="flex h-full w-full bg-white">
    @extends('layouts.create-invoice')

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

                            <div id="filterFormContainerInvoice" class="mt-4 hidden">
                                <form id="filterFormInvoice" method="GET" action="{{ route('invoices.index') }}"
                                    class="mb-6 flex gap-6 items-center flex-wrap">

                                    {{-- Invoice Number --}}
                                    <div class="relative inline-block text-left w-48">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice No</label>
                                        <input type="hidden" name="invoice_number" id="invoiceNumberInput"
                                            value="{{ request('invoice_number') }}">
                                        <button type="button"
                                            class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                            onclick="toggleDropdown(event, 'invoiceNumberDropdownMenu')">
                                            <span
                                                id="selectedInvoiceNumber">{{ request('invoice_number') ?? 'Select Invoice No' }}</span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div id="invoiceNumberDropdownMenu"
                                            class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                            <input type="text" id="invoiceNumberSearch"
                                                onkeyup="filterDropdown('invoiceNumberSearch','invoice-number-option')"
                                                placeholder="Search..." class="w-full px-2 py-1 text-sm border rounded-md"
                                                autocomplete="off">
                                            <div onclick="selectDropdown('','invoiceNumberInput','selectedInvoiceNumber')"
                                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All Invoice
                                                Numbers</div>
                                            @foreach ($allInvoiceNumbers as $inv)
                                                <div onclick="selectDropdown('{{ $inv }}','invoiceNumberInput','selectedInvoiceNumber')"
                                                    class="invoice-number-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                    {{ $inv }}</div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Customer Name --}}
                                    <div class="relative inline-block text-left w-56">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                                        <input type="hidden" name="customer_name" id="customerNameInput"
                                            value="{{ request('customer_name') }}">
                                        <button type="button"
                                            class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                            onclick="toggleDropdown(event, 'customerNameDropdownMenu')">
                                            <span
                                                id="selectedCustomerName">{{ request('customer_name') ?? 'Select Customer Name' }}</span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div id="customerNameDropdownMenu"
                                            class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                            <input type="text" id="customerNameSearch"
                                                onkeyup="filterDropdown('customerNameSearch','customer-name-option')"
                                                placeholder="Search..." class="w-full px-2 py-1 text-sm border rounded-md"
                                                autocomplete="off">
                                            <div onclick="selectDropdown('','customerNameInput','selectedCustomerName')"
                                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All Customers
                                            </div>
                                            @foreach ($allCustomerNames as $cust)
                                                <div onclick="selectDropdown('{{ $cust }}','customerNameInput','selectedCustomerName')"
                                                    class="customer-name-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                    {{ $cust }}</div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- EMI --}}
                                    <div class="relative inline-block text-left w-56">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">EMI</label>
                                        <input type="hidden" name="emi" id="emiInput" value="{{ request('emi') }}">
                                        <button type="button"
                                            class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                            onclick="toggleDropdown(event, 'emiDropdownMenu')">
                                            <span id="selectedEmi">{{ request('emi') ?? 'Select EMI' }}</span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div id="emiDropdownMenu"
                                            class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                            <input type="text" id="emiSearch"
                                                onkeyup="filterDropdown('emiSearch','emi-option')" placeholder="Search..."
                                                class="w-full px-2 py-1 text-sm border rounded-md" autocomplete="off">
                                            <div onclick="selectDropdown('','emiInput','selectedEmi')"
                                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All EMIs</div>
                                            @foreach ($filterEmis as $emi)
                                                <div onclick="selectDropdown('{{ $emi }}','emiInput','selectedEmi')"
                                                    class="emi-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                    {{ $emi }}</div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Phone Type --}}
                                    <div class="relative inline-block text-left w-48">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Type</label>
                                        <input type="hidden" name="phone_type" id="phoneTypeInput"
                                            value="{{ request('phone_type') }}">
                                        <button type="button"
                                            class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                            onclick="toggleDropdown(event, 'phoneTypeDropdownMenu')">
                                            <span
                                                id="selectedPhoneType">{{ request('phone_type') ?? 'Select Phone Type' }}</span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div id="phoneTypeDropdownMenu"
                                            class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                            <input type="text" id="phoneTypeSearch"
                                                onkeyup="filterDropdown('phoneTypeSearch','phone-type-option')"
                                                placeholder="Search..." class="w-full px-2 py-1 text-sm border rounded-md"
                                                autocomplete="off">
                                            <div onclick="selectDropdown('','phoneTypeInput','selectedPhoneType')"
                                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All Phone Types
                                            </div>
                                            @foreach ($filterPhoneTypes as $type)
                                                <div onclick="selectDropdown('{{ $type }}','phoneTypeInput','selectedPhoneType')"
                                                    class="phone-type-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                    {{ $type }}</div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="flex items-end space-x-2 mt-2">
                                        <button type="submit"
                                            class="mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Apply
                                            Filters</button>
                                        <button type="button" onclick="clearFiltersInvoice()"
                                            class="mt-4 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Clear</button>
                                    </div>

                                </form>
                            </div>

                            <div class="flex-1">

                                <div class="flex justify-between items-center mb-6">
                                    <h1 class="text-2xl font-bold text-gray-800">Invoice Records
                                    </h1>

                                    <div class="flex space-x-3">
                                        <button
                                            onclick="document.getElementById('addExchangePhoneModal').classList.remove('hidden')"
                                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow">
                                            + Add Exchange Phone
                                        </button>
                                        <button
                                            onclick="document.getElementById('createInvoiceModal').classList.remove('hidden')"
                                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow">
                                            + Add Invoice
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Exchange Phone Stock Modal -->
                            <div id="addExchangePhoneModal"
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
                                                    <!-- Stock Type (Fixed) -->
                                                    <div class="mt-1">
                                                        <div
                                                            class="w-full rounded-md bg-gray-100 dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white ring-1 ring-gray-300">
                                                            Exchange
                                                        </div>

                                                        <input type="hidden" name="stock_type" value="Exchange">
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

                                            <div id="supplierIdUpload" class="mt-3">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Upload Supplier ID (Front & Back)
                                                </label>
                                                <div class="grid grid-cols-2 gap-4 mt-2">
                                                    <div>
                                                        <input type="file" name="supplier_id_front" accept="image/*"
                                                            class="w-full border rounded-md p-2 dark:bg-gray-700 dark:text-white text-sm">
                                                        <p class="text-xs text-gray-500 mt-1">Front side</p>
                                                    </div>
                                                    <div>
                                                        <input type="file" name="supplier_id_back" accept="image/*"
                                                            class="w-full border rounded-md p-2 dark:bg-gray-700 dark:text-white text-sm">
                                                        <p class="text-xs text-gray-500 mt-1">Back side</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- ACTIONS -->
                                            <div class="flex justify-end mt-6 space-x-3">
                                                <button type="button"
                                                    onclick="document.getElementById('addExchangePhoneModal').classList.add('hidden')"
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

                            <!-- Create Invoice Modal -->
                            <div id="createInvoiceModal"
                                class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center px-4 py-6">

                                <form id="invoiceForm" action="{{ route('invoices.store') }}" method="POST"
                                    class="w-full max-w-6xl bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden transform transition-all scale-95 max-h-[90vh] flex flex-col"
                                    x-data="invoiceModal()">

                                    @csrf

                                    <!-- Header -->
                                    <div
                                        class="flex justify-between items-center px-8 py-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                                        <h2
                                            class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 3v18h18V3H3z M16 3v18 M3 9h18" />
                                            </svg>
                                            Create Invoice
                                        </h2>
                                        <button type="button" onclick="closeInvoiceModal()"
                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-4xl font-bold">&times;
                                        </button>
                                    </div>

                                    <!-- Body -->
                                    <div class="px-8 py-6 overflow-y-auto flex-1 space-y-8">

                                        <!-- Customer Coordinator -->
                                        <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-6"
                                            x-data="workerDropdown()">
                                            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">
                                                Customer Coordinator</h3>

                                            <!-- Customer Coordinator Dropdown -->
                                            <div class="relative w-full inline-block text-left mb-6">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Customer
                                                    Coordinator</label>
                                                <input type="hidden" name="worker_id" id="workerIdInput" required>
                                                <input type="hidden" name="worker_name" id="workerNameInput">

                                                <button type="button"
                                                    class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                                    onclick="toggleDropdown(event, 'workerDropdownMenu')">
                                                    <span id="selectedWorker">Select Worker</span>
                                                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>

                                                <div id="workerDropdownMenu"
                                                    class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-60 overflow-y-auto p-2">

                                                    <input type="text" id="workerSearch"
                                                        onkeyup="filterDropdown('workerSearch','worker-option')"
                                                        placeholder="Search..."
                                                        class="w-full px-2 py-1 text-sm border rounded-md mb-2"
                                                        autocomplete="off">

                                                    {{-- Worker options --}}
                                                    @foreach ($workers as $worker)
                                                        <div onclick="selectWorker('{{ $worker->id }}','{{ $worker->name }}')"
                                                            class="worker-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                            {{ $worker->name }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer Details -->
                                        <div
                                            class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">
                                                Customer Details</h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <input name="customer_name" placeholder="Customer Name" required
                                                    class="w-full p-4 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                <input name="customer_phone" placeholder="Customer Phone No" required
                                                    pattern="^(0\d{9}|94\d{9})$"
                                                    title="Enter a valid Sri Lankan phone number, e.g., 0777137830 or 94777137830"
                                                    class="w-full p-4 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            </div>
                                            <textarea name="customer_address" placeholder="Customer Address" rows="3" maxlength="25"
                                                class="w-full mt-4 p-4 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                                        </div>

                                        <!-- Exchange Phone -->
                                        <div>
                                            <div class="flex items-center gap-2 mb-4">
                                                <input type="hidden" name="isExchange" value="0">
                                                <input type="checkbox" id="is_exchange" name="isExchange"
                                                    x-model="isExchange" value="1" class="w-5 h-5">
                                                <label for="is_exchange"
                                                    class="text-gray-700 dark:text-gray-200 font-semibold">Is this an
                                                    Exchange?</label>
                                            </div>

                                            <div x-show="isExchange" x-transition
                                                class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">
                                                    Exchange Phones</h3>

                                                <div class="relative w-full">
                                                    <div @click="open = !open"
                                                        class="cursor-pointer w-full p-4 border rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-white flex justify-between items-center">
                                                        <span
                                                            x-text="exchangeSelected ? exchangeSelected.label : 'Select Exchange Phone'"></span>
                                                        <svg class="w-5 h-5 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </div>

                                                    <div x-show="open" @click.outside="open = false" x-transition
                                                        class="absolute z-10 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-lg">

                                                        <input type="text" x-model="search"
                                                            placeholder="Search Exchange..."
                                                            class="w-full px-4 py-2 border-b border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">

                                                        <template x-for="option in filteredOptions" :key="option.value">
                                                            <div @click="selectExchange(option)"
                                                                class="cursor-pointer px-4 py-2 hover:bg-blue-100 dark:hover:bg-gray-600 text-gray-700 dark:text-white">
                                                                <span
                                                                    x-text="option.label + ' (' + option.phone_type + ')'"></span>
                                                            </div>
                                                        </template>

                                                        <div x-show="filteredOptions.length === 0"
                                                            class="px-4 py-2 text-gray-400 dark:text-gray-300">
                                                            No results found
                                                        </div>
                                                    </div>

                                                    <input type="hidden" name="exchange_emi"
                                                        :value="exchangeSelected ? exchangeSelected.value : ''">
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                                    <input id="exchange_phone_type" name="exchange_phone_type" readonly
                                                        placeholder="Phone Type"
                                                        class="w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white"
                                                        :value="exchangeSelected ? exchangeSelected.phone_type : ''">
                                                    <input id="exchange_colour" name="exchange_colour" readonly
                                                        placeholder="Colour"
                                                        class="w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white"
                                                        :value="exchangeSelected ? exchangeSelected.colour : ''">
                                                    <input id="exchange_capacity" name="exchange_capacity" readonly
                                                        placeholder="Capacity"
                                                        class="w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white"
                                                        :value="exchangeSelected ? exchangeSelected.capacity : ''">
                                                    <input type="hidden" name="exchange_cost"
                                                        :value="exchangeSelected ? exchangeSelected.cost : ''">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Phone Selection -->
                                        <div
                                            class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Select
                                                Phone</h3>
                                            <div class="relative w-full inline-block text-left">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                                <input type="hidden" name="emi" id="phoneEmiInput" required>
                                                <button type="button"
                                                    class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                                    onclick="toggleDropdown(event, 'phoneDropdownMenu')">
                                                    <span id="selectedPhone">Select EMI</span>
                                                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <div id="phoneDropdownMenu"
                                                    class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-60 overflow-y-auto p-2">
                                                    <input type="text" id="phoneSearch"
                                                        onkeyup="filterDropdown('phoneSearch','phone-option')"
                                                        placeholder="Search..."
                                                        class="w-full px-2 py-1 text-sm border rounded-md mb-2"
                                                        autocomplete="off">
                                                    {{-- Phone options --}}
                                                    @foreach ($addInvoiceEmis as $phone)
                                                        <div onclick="selectPhone('{{ $phone->id }}','{{ $phone->emi }}','{{ $phone->phone_type }}','{{ $phone->colour }}','{{ $phone->capacity }}')"
                                                            class="phone-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                            {{ $phone->emi }} - {{ $phone->phone_type }}
                                                            ({{ $phone->colour }}, {{ $phone->capacity }})
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                                <input id="phone_type" name="phone_type" readonly
                                                    placeholder="Phone Type"
                                                    class="w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white">
                                                <input id="colour" name="colour" readonly placeholder="Colour"
                                                    class="w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white">
                                                <input id="capacity" name="capacity" readonly placeholder="Capacity"
                                                    class="w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white">
                                            </div>
                                        </div>

                                        <!-- Accessories & Selling Price -->
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                                            <!-- Selling Price -->
                                            <div
                                                class="col-span-1 md:col-span-4 bg-gray-50 p-6 rounded-xl border shadow-sm">
                                                <h3 class="text-xl font-semibold mb-4">Selling Price</h3>
                                                <input type="number" min="0" name="selling_price"
                                                    x-model.number="sellingPrice" placeholder="Enter Selling Price"
                                                    class="w-full p-4 border rounded-lg">
                                            </div>

                                            <!-- Accessories Cart -->
                                            <div
                                                class="col-span-1 md:col-span-8 bg-gray-50 p-6 rounded-xl border shadow-sm">

                                                <h3 class="text-xl font-semibold mb-4">Accessories</h3>

                                                <template x-for="(item, index) in accessoriesCart" :key="index">
                                                    <div class="flex gap-3 items-center mb-3" x-data="{ open: false, search: '' }">

                                                        <div x-data="{
                                                            open: false,
                                                            search: '',
                                                            item: { id: '', name: '' },
                                                            allOptions: [
                                                                @foreach ($allAccessories as $acc)
        { id: '{{ $acc->id }}', name: '{{ $acc->name }}', stock: '{{ $acc->quantity }}' }@if (!$loop->last),@endif @endforeach
                                                            ]
                                                        }" class="relative w-1/2">

                                                            <input type="hidden" :name="'accessories[' + index + '][id]'"
                                                                x-model="item.id" required>

                                                            <button type="button"
                                                                class="w-full text-left px-3 py-2 border rounded-md bg-white dark:bg-gray-700 dark:text-white flex justify-between items-center"
                                                                @click.stop="open = !open">
                                                                <span
                                                                    x-text="item.name ? item.name : 'Select Accessory'"></span>
                                                                <svg class="ml-2 h-5 w-5 text-gray-400"
                                                                    fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.08z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </button>

                                                            <div x-show="open" @click.outside="open = false" x-transition
                                                                class="absolute z-40 mt-1 w-full bg-white dark:bg-gray-700 border rounded-lg shadow-lg max-h-60 overflow-y-auto p-2">

                                                                <!-- Search Box -->
                                                                <input type="text" x-model="search"
                                                                    placeholder="Search..."
                                                                    class="w-full px-2 py-1 text-sm border rounded-md mb-2 dark:bg-gray-600 dark:text-white">

                                                                <!-- Filtered Accessories -->
                                                                <template
                                                                    x-for="option in allOptions.filter(o => !search || o.name.toLowerCase().includes(search.toLowerCase()))"
                                                                    :key="option.id">
                                                                    <div @click="item.id = option.id; item.name = option.name; open=false"
                                                                        class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-sm">
                                                                        <span
                                                                            x-text="option.name + ' (Stock: ' + option.stock + ')'"></span>
                                                                    </div>
                                                                </template>

                                                                <!-- No Results -->
                                                                <div x-show="allOptions.filter(o => !search || o.name.toLowerCase().includes(search.toLowerCase())).length === 0"
                                                                    class="px-4 py-2 text-gray-400 dark:text-gray-300">
                                                                    No results found
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Quantity -->
                                                        <input type="number" min="1"
                                                            :name="'accessories[' + index + '][qty]'"
                                                            x-model.number="item.qty"
                                                            class="w-1/4 h-10 px-3 border rounded-md" placeholder="Qty">

                                                        <!-- Price -->
                                                        <input type="number" min="0"
                                                            :name="'accessories[' + index + '][price]'"
                                                            x-model.number="item.price"
                                                            class="w-1/4 h-10 px-3 border rounded-md" placeholder="Price">

                                                        <!-- Remove Button -->
                                                        <button type="button" @click="accessoriesCart.splice(index,1)"
                                                            class="px-2 py-1 bg-red-500 text-white rounded-md">X</button>

                                                    </div>
                                                </template>

                                                <!-- Add New Accessory Button -->
                                                <button type="button" @click="addAccessory()"
                                                    class="mt-2 px-4 py-2 bg-green-500 text-white rounded-md">+ Add
                                                    Accessory</button>

                                                <!-- Accessories Total -->
                                                <div
                                                    class="mt-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600">
                                                    <span
                                                        class="font-semibold text-gray-700 dark:text-gray-200">Accessories
                                                        Total: </span>
                                                    <span class="font-bold text-lg text-gray-800 dark:text-white"
                                                        x-text="accessoriesTotal.toFixed(2)"></span>
                                                </div>

                                            </div>
                                        </div>

                                        <!-- Payable Section -->
                                        <div class="flex flex-col gap-2"> <!-- Checkbox to toggle payable -->
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center gap-2"> <input type="hidden"
                                                        name="isPayable" value="0"> <input type="checkbox"
                                                        id="is_payable" name="isPayable" x-model="isPayable"
                                                        value="1" class="w-5 h-5"> <label for="is_payable"
                                                        class="text-gray-700 dark:text-gray-200 font-semibold"> Is this
                                                        Payable? </label> </div> <!-- Right-side note --> <span
                                                    class="text-lg text-red-600 font-semibold border border-red-600 px-3 py-1 rounded-md">
                                                    This can only be applied after CEO approval. </span>
                                            </div> <!-- Section that appears when checkbox is checked -->
                                            <div x-show="isPayable" x-transition
                                                class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <h1 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                                    Payable Amount</h1> <!-- Input for payable amount --> <input
                                                    type="number" name="payable_amount" x-model.number="payableAmount"
                                                    placeholder="Enter payable amount" min="0"
                                                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                <!-- Display remaining amount -->
                                                <p class="mt-2 text-gray-700 dark:text-gray-200"> Customer need to pay Now:
                                                    <span x-text="remainingAmount.toFixed(2)"></span>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Total Amount -->
                                        <div
                                            class="bg-blue-50 dark:bg-gray-800 p-6 rounded-xl border border-blue-200 dark:border-gray-700 flex justify-between items-center shadow-md mt-6">
                                            <span class="font-semibold text-gray-700 dark:text-gray-200 text-lg">
                                                Total (Selling - Exchange + Accessories)
                                            </span>
                                            <input type="text" readonly :value="totalAmount.toFixed(2)"
                                                class="w-40 text-right p-4 border rounded-lg bg-blue-100 dark:bg-gray-700 dark:text-white font-bold text-lg">
                                        </div>

                                    </div>

                                    <!-- Footer Actions -->
                                    <div
                                        class="px-8 py-4 bg-gray-50 dark:bg-gray-800 border-t dark:border-gray-700 flex justify-end gap-4">
                                        <button type="button" onclick="closeInvoiceModal()"
                                            class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-3000">
                                            Cancel
                                        </button>
                                        <button type="submit" id="createInvoiceBtn"
                                            class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                            Create Invoice
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <script>
                                function invoiceModal() {
                                    return {

                                        isExchange: false,
                                        sellingPrice: 0,
                                        exchangeSelected: null,

                                        // new reactive accessoriesCart
                                        accessoriesCart: [{
                                            id: '',
                                            qty: 1,
                                            price: 0
                                        }],

                                        open: false,
                                        search: '',
                                        options: [
                                            @foreach ($exchangePhones as $phone)
                                                {
                                                    value: '{{ $phone->emi }}',
                                                    label: '{{ $phone->emi }}',
                                                    phone_type: '{{ $phone->phone_type }}',
                                                    colour: '{{ $phone->colour }}',
                                                    capacity: '{{ $phone->capacity }}',
                                                    cost: Number({{ $phone->cost }})
                                                }
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        ],

                                        isPayable: false,
                                        payableAmount: 0,

                                        // accessories total
                                        get accessoriesTotal() {
                                            return this.accessoriesCart.reduce((sum, item) => {
                                                const qty = Number(item.qty) || 0;
                                                const price = Number(item.price) || 0;
                                                return sum + qty * price;
                                            }, 0);
                                        },

                                        get totalAmount() {
                                            let total = Number(this.sellingPrice) || 0;

                                            if (this.isExchange && this.exchangeSelected) {
                                                total -= Number(this.exchangeSelected.cost) || 0;
                                            }

                                            total += this.accessoriesTotal;

                                            return total;
                                        },

                                        get remainingAmount() {
                                            return this.totalAmount - (Number(this.payableAmount) || 0);
                                        },

                                        get filteredOptions() {
                                            if (!this.search) return this.options;

                                            return this.options.filter(option =>
                                                option.label.toLowerCase().includes(this.search.toLowerCase())
                                            );
                                        },

                                        selectExchange(option) {
                                            this.exchangeSelected = option;
                                            this.open = false;
                                        },

                                        reset() {
                                            this.isExchange = false;
                                            this.sellingPrice = 0;
                                            this.exchangeSelected = null;
                                            this.payableAmount = 0;
                                            this.accessoriesCart = [{
                                                id: '',
                                                qty: 1,
                                                price: 0
                                            }];

                                            Object.keys(this.accessories).forEach(key => {
                                                this.accessories[key] = 0;
                                            });
                                        },

                                        addAccessory() {
                                            this.accessoriesCart.push({
                                                id: '',
                                                qty: 1,
                                                price: 0
                                            });
                                        },

                                        removeAccessory(index) {
                                            this.accessoriesCart.splice(index, 1);
                                        }
                                    }
                                }

                                document.addEventListener('DOMContentLoaded', () => {

                                    // -----------------------------
                                    // Open / Close Modal
                                    // -----------------------------
                                    window.openInvoiceModal = function() {
                                        document.getElementById('createInvoiceModal').classList.remove('hidden');
                                    }

                                    window.closeInvoiceModal = function() {
                                        const modal = document.getElementById('createInvoiceModal');
                                        modal.classList.add('hidden');
                                        const form = document.getElementById('invoiceForm');
                                        form.reset();

                                        // Reset visible text for dropdowns
                                        document.getElementById('selectedPhone').textContent = 'Select EMI';
                                        document.getElementById('selectedWorker').textContent = 'Select Worker';

                                        // Reset all accessory dropdowns
                                        document.querySelectorAll('[x-data]').forEach(drop => {
                                            if (drop.__x) drop.__x.$data.open = false;
                                        });
                                    }

                                    // -----------------------------
                                    // Central Dropdown Toggle
                                    // -----------------------------
                                    window.toggleDropdown = function(event, dropdownId) {
                                        event.stopPropagation();
                                        const dropdown = document.getElementById(dropdownId);
                                        dropdown.classList.toggle('hidden');
                                    }

                                    // -----------------------------
                                    // Central Search Filter
                                    // -----------------------------
                                    window.filterDropdown = function(inputId, optionClass) {
                                        const filter = document.getElementById(inputId).value.toLowerCase();
                                        const options = document.getElementsByClassName(optionClass);

                                        Array.from(options).forEach(option => {
                                            const txt = option.textContent.toLowerCase();
                                            option.style.display = txt.includes(filter) ? "" : "none";
                                        });
                                    }

                                    // -----------------------------
                                    // Select Worker
                                    // -----------------------------
                                    window.selectWorker = function(id, name) {
                                        document.getElementById('workerIdInput').value = id;
                                        document.getElementById('workerNameInput').value = name;
                                        document.getElementById('selectedWorker').textContent = name;

                                        document.getElementById('workerDropdownMenu').classList.add('hidden');
                                    }

                                    // -----------------------------
                                    // Select Phone
                                    // -----------------------------
                                    window.selectPhone = function(id, emi, phone_type, colour, capacity) {
                                        document.getElementById('phoneEmiInput').value = emi;
                                        document.getElementById('selectedPhone').textContent = emi;
                                        document.getElementById('phone_type').value = phone_type;
                                        document.getElementById('colour').value = colour;
                                        document.getElementById('capacity').value = capacity;

                                        document.getElementById('phoneDropdownMenu').classList.add('hidden');
                                        document.getElementById('invoiceForm').dataset.phoneId = id;
                                    }

                                    // -----------------------------
                                    // Global click listener to close dropdowns
                                    // -----------------------------
                                    document.addEventListener('click', function(event) {
                                        // Phone Dropdown
                                        const phoneDropdown = document.getElementById('phoneDropdownMenu');
                                        const phoneButton = document.getElementById('selectedPhone').closest('button');
                                        if (!phoneDropdown.contains(event.target) && !phoneButton.contains(event.target)) {
                                            phoneDropdown.classList.add('hidden');
                                        }

                                        // Worker Dropdown
                                        const workerDropdown = document.getElementById('workerDropdownMenu');
                                        const workerButton = document.getElementById('selectedWorker').closest('button');
                                        if (!workerDropdown.contains(event.target) && !workerButton.contains(event.target)) {
                                            workerDropdown.classList.add('hidden');
                                        }

                                        // Accessory Dropdowns (dynamic, inside x-for)
                                        document.querySelectorAll('[x-data]').forEach(drop => {
                                            if (!drop.contains(event.target) && drop.__x) {
                                                drop.__x.$data.open = false;
                                            }
                                        });
                                    });

                                });
                            </script>

                            <script>
                                function accessoryCart() {
                                    return {
                                        cart: [{
                                                id: '',
                                                qty: 1,
                                                price: 0
                                            } // initial row
                                        ],

                                        get accessoriesTotal() {
                                            return this.cart.reduce((sum, item) => {
                                                const qty = Number(item.qty) || 0;
                                                const price = Number(item.price) || 0;
                                                return sum + qty * price;
                                            }, 0);
                                        },

                                        addItem() {
                                            this.cart.push({
                                                id: '',
                                                qty: 1,
                                                price: 0
                                            });
                                        },

                                        removeItem(index) {
                                            this.cart.splice(index, 1);
                                        }
                                    }
                                }
                            </script>

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
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                        </path>
                                    </svg>
                                    <p class="mt-3 text-gray-700 font-semibold">Loading data...</p>
                                </div>
                                <table class="table-fixed w-full text-sm divide-y divide-gray-200">
                                    <thead class="bg-gray-200 text-left">
                                        <tr class="text-center">
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase break-words">
                                                Invoice No
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase break-words">
                                                Customer Details
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase break-words">
                                                Phone Details
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase break-words">
                                                Prices
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase break-words">
                                                Total Price
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase break-words">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200 text-left">
                                        @forelse($invoices as $invoice)
                                            <tr class="text-center">
                                                <!-- Invoice No with updated date -->
                                                <td class="px-4 py-2">
                                                    <span class="font-semibold">{{ $invoice->invoice_number }}</span><br>
                                                    <span
                                                        class="text-xs text-gray-500">{{ $invoice->worker->name }}</span><br>
                                                    <span
                                                        class="text-xs text-gray-500">{{ $invoice->updated_at->format('d M Y') }}</span>
                                                </td>

                                                <!-- Customer Details -->
                                                <td class="px-4 py-2 text-left">
                                                    <span class="font-semibold">Name:</span>
                                                    {{ $invoice->customer_name }}<br>
                                                    <span class="font-semibold">Phone:</span>
                                                    {{ $invoice->customer_phone }}<br>
                                                    <span class="font-semibold">Address:</span>
                                                    {{ $invoice->customer_address ?? '-' }}
                                                </td>

                                                <!-- Phone Details -->
                                                <td class="px-4 py-2 text-left">
                                                    <span class="font-semibold">EMI:</span> {{ $invoice->emi }}<br>
                                                    <span class="font-semibold">Model:</span>
                                                    {{ $invoice->phone_type }}<br>
                                                    <span class="font-semibold">Capacity:</span>
                                                    {{ $invoice->capacity }}<br>
                                                    <span class="font-semibold">Colour:</span> {{ $invoice->colour }}

                                                    @if ($invoice->exchange_emi)
                                                        <hr class="my-2 border-gray-300">
                                                        <span class="font-semibold text-blue-600">Exchange
                                                            Phone:</span><br>
                                                        <span class="font-semibold">EMI:</span>
                                                        {{ $invoice->exchange_emi }}<br>
                                                        <span class="font-semibold">Model:</span>
                                                        {{ $invoice->exchange_phone_type }}<br>
                                                        <span class="font-semibold">Capacity:</span>
                                                        {{ $invoice->exchange_capacity }}<br>
                                                        <span class="font-semibold">Colour:</span>
                                                        {{ $invoice->exchange_colour }}<br>
                                                        <span class="font-semibold">Cost:</span> LKR
                                                        {{ number_format($invoice->exchange_cost, 2) }}
                                                    @endif
                                                </td>

                                                <!-- Prices -->
                                                <td class="px-4 py-2 text-left">
                                                    @if ($invoice->selling_price > 0)
                                                        <span class="font-semibold">Phone Price:</span> LKR
                                                        {{ number_format($invoice->selling_price, 2) }}<br>
                                                    @endif
                                                    <!-- Accessories for this invoice -->
                                                    @php
                                                        $invoiceAccessories = $invoice->invoiceAccessories;
                                                    @endphp

                                                    @if ($invoiceAccessories->isNotEmpty())
                                                        <hr class="my-1 border-gray-300">
                                                        <span
                                                            class="font-semibold text-gray-600 text-sm">Accessories:</span><br>
                                                        @foreach ($invoiceAccessories as $acc)
                                                            <span class="text-xs text-gray-600">
                                                                {{ $acc->accessory_name }}: LKR
                                                                {{ number_format($acc->selling_price_accessory * $acc->quantity, 2) }}
                                                                (Qty: {{ $acc->quantity }})
                                                            </span><br>
                                                        @endforeach
                                                    @endif

                                                </td>

                                                <!-- Total Price -->
                                                <td class="px-4 py-2 text-center font-bold">
                                                    @if (isset($invoice->payable_amount) && $invoice->payable_amount > 0)
                                                        <div class="text-red-600">
                                                            Payable: LKR
                                                            {{ number_format($invoice->payable_amount, 2) }}
                                                        </div>
                                                        <div class="text-gray-500">
                                                            Total: LKR {{ number_format($invoice->total_amount, 2) }}
                                                        </div>
                                                    @else
                                                        <div class="text-green-600">
                                                            LKR {{ number_format($invoice->total_amount, 2) }}
                                                        </div>
                                                    @endif
                                                </td>

                                                <td class="px-4 align-middle">
                                                    <div class="inline-flex items-center justify-center gap-2">
                                                        <!-- Print Invoice Button -->
                                                        <button type="button"
                                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm"
                                                            onclick="printInvoice({{ $invoice->id }})">
                                                            Print
                                                        </button>

                                                        <!-- Delete Invoice -->
                                                        @if (auth()->user()->hasRole('SUPERADMIN'))
                                                            <form id="delete-form-{{ $invoice->id }}"
                                                                action="{{ route('invoices.destroy', $invoice->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button"
                                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm mt-3"
                                                                    onclick="confirmDelete('{{ $invoice->id }}')">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        @endif

                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-2 text-center text-gray-500">No
                                                    invoices
                                                    found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="py-6 flex justify-center">
                                {{ $invoices->links() }}
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
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('#createInvoiceModal form');
            const submitBtn = document.getElementById('createInvoiceBtn');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Submitting...';
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('#addExchangePhoneModal form');
            const submitBtn = document.getElementById('createPhoneBtn');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Submitting...';
            });
        });
    </script>

    <script>
        function printInvoice(invoiceId) {
            const url = `/phone-shop/invoice-print/${invoiceId}`;
            const printWindow = window.open(url, '_blank', 'width=800,height=600');
            printWindow.focus();
            printWindow.print();
        }
    </script>

    <script>
        function toggleFilterForm() {
            const form = document.getElementById('filterFormContainerInvoice');
            form.classList.toggle('hidden');
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const menus = [
                "invoiceNumberDropdownMenu",
                "customerNameDropdownMenu",
                "emiDropdownMenu",
                "phoneTypeDropdownMenu"
            ].map(id => document.getElementById(id));

            menus.forEach(menu => {
                if (menu) menu.addEventListener("click", e => e.stopPropagation());
            });

            // Toggle dropdown
            window.toggleDropdown = function(event, id) {
                event.stopPropagation();
                closeAllDropdowns();
                const menu = document.getElementById(id);
                if (menu) menu.classList.toggle("hidden");
            }

            // Select value
            window.selectDropdown = function(value, inputId, spanId) {
                const input = document.getElementById(inputId);
                const span = document.getElementById(spanId);

                if (input) input.value = value;
                if (span) {
                    let placeholder = "Select";
                    if (spanId === "selectedInvoiceNumber") placeholder = "Select Invoice No";
                    if (spanId === "selectedCustomerName") placeholder = "Select Customer Name";
                    if (spanId === "selectedEmi") placeholder = "Select EMI";
                    if (spanId === "selectedPhoneType") placeholder = "Select Phone Type";
                    span.textContent = value || placeholder;
                }
                closeAllDropdowns();
            }

            // Filter options
            window.filterDropdown = function(searchId, optionClass) {
                const searchInput = document.getElementById(searchId);
                if (!searchInput) return;
                const filter = searchInput.value.toLowerCase();
                document.querySelectorAll('.' + optionClass).forEach(option => {
                    option.style.display = option.textContent.toLowerCase().includes(filter) ? "block" :
                        "none";
                });
            }

            // Close all dropdowns
            function closeAllDropdowns() {
                menus.forEach(menu => {
                    if (menu) menu.classList.add("hidden");
                });
            }

            document.addEventListener("click", closeAllDropdowns);

            // Clear filters
            window.clearFiltersInvoice = function() {
                window.location.href = window.location.pathname;
            }

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
@endsection
