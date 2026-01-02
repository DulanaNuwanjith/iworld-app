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
                                    <div class="relative inline-block text-left w-48">
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
                                            onclick="document.getElementById('createInvoiceModal').classList.remove('hidden')"
                                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow">
                                            + Add Invoice
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Create Invoice Modal -->
                            <div id="createInvoiceModal"
                                class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center px-4 py-6">

                                <form id="invoiceForm" action="{{ route('invoices.store') }}" method="POST"
                                    class="w-full max-w-6xl bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden transform transition-all scale-95 max-h-[90vh] flex flex-col">

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
                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-4xl font-bold">&times;</button>
                                    </div>

                                    <!-- Body -->
                                    <div class="px-8 py-6 overflow-y-auto flex-1 space-y-8">

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
                                            <textarea name="customer_address" placeholder="Customer Address" rows="3"
                                                class="w-full mt-4 p-4 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                                        </div>

                                        <!-- Phone Selection -->
                                        <div
                                            class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Select
                                                Phone</h3>
                                            <!-- Custom EMI Dropdown with Search -->
                                            <div class="relative w-full" x-data="{
                                                open: false,
                                                search: '',
                                                selected: null,
                                                options: [
                                                    @foreach ($addInvoiceEmis as $emi)
        { value: '{{ $emi->emi }}', label: '{{ $emi->emi }} - {{ $emi->phone_type }}', phone_type: '{{ $emi->phone_type }}', colour: '{{ $emi->colour }}', capacity: '{{ $emi->capacity }}' }, @endforeach
                                                ],
                                                get filteredOptions() {
                                                    if (this.search === '') return this.options;
                                                    return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
                                                }
                                            }">
                                                <label
                                                    class="block mb-2 text-gray-700 dark:text-gray-200 font-semibold">Select
                                                    EMI</label>

                                                <!-- Selected value -->
                                                <div @click="open = !open"
                                                    class="cursor-pointer w-full p-4 border rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-white flex justify-between items-center focus:ring-2 focus:ring-blue-500">
                                                    <span x-text="selected ? selected.label : 'Select EMI'"></span>
                                                    <svg class="w-5 h-5 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </div>

                                                <!-- Dropdown options -->
                                                <div x-show="open" @click.outside="open = false" x-transition
                                                    class="absolute z-10 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-lg">

                                                    <!-- Search input -->
                                                    <input type="text" x-model="search" placeholder="Search EMI..."
                                                        class="w-full px-4 py-2 border-b border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">

                                                    <!-- Options list -->
                                                    <template x-for="option in filteredOptions" :key="option.value">
                                                        <div @click="selected = option; open = false;
                         document.getElementById('phone_type').value = option.phone_type;
                         document.getElementById('colour').value = option.colour;
                         document.getElementById('capacity').value = option.capacity;"
                                                            class="cursor-pointer px-4 py-2 hover:bg-blue-100 dark:hover:bg-gray-600 text-gray-700 dark:text-white">
                                                            <span x-text="option.label"></span>
                                                        </div>
                                                    </template>

                                                    <!-- No results -->
                                                    <div x-show="filteredOptions.length === 0"
                                                        class="px-4 py-2 text-gray-400 dark:text-gray-300">
                                                        No results found
                                                    </div>
                                                </div>

                                                <!-- Hidden input for form submission -->
                                                <input type="hidden" name="emi"
                                                    :value="selected ? selected.value : ''" required>
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

                                        <!-- Selling Price & Accessories -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Selling Price -->
                                            <div
                                                class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">
                                                    Selling Price</h3>
                                                <input type="number" name="selling_price" id="selling_price"
                                                    placeholder="Enter Selling Price" min="0"
                                                    class="w-full p-4 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            </div>

                                            <!-- Accessories -->
                                            <div
                                                class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">
                                                    Accessories</h3>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <input type="number" name="tempered" placeholder="Tempered price"
                                                        min="0"
                                                        class="accessory w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white">
                                                    <input type="number" name="back_cover"
                                                        placeholder="Back cover price" min="0"
                                                        class="accessory w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white">
                                                    <input type="number" name="charger" placeholder="Charger price"
                                                        min="0"
                                                        class="accessory w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white">
                                                    <input type="number" name="data_cable"
                                                        placeholder="Data cable price" min="0"
                                                        class="accessory w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white">
                                                    <input type="number" name="hand_free" placeholder="Hand free price"
                                                        min="0"
                                                        class="accessory w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white">
                                                    <input type="number" name="cam_glass" placeholder="Camera glass price"
                                                        min="0"
                                                        class="accessory w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white">
                                                    <input type="number" name="airpods" placeholder="AirPods price"
                                                        min="0"
                                                        class="accessory w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white">
                                                    <input type="number" name="power_bank"
                                                        placeholder="Power bank price" min="0"
                                                        class="accessory w-full p-4 border rounded-lg dark:bg-gray-700 dark:text-white">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Total Amount -->
                                        <div
                                            class="bg-blue-50 dark:bg-gray-800 p-4 rounded-lg border border-blue-200 dark:border-gray-700 flex justify-between items-center shadow-sm">
                                            <span class="font-semibold text-gray-700 dark:text-gray-200">Total
                                                Amount</span>
                                            <input type="text" id="total_amount" name="total_amount" readonly
                                                class="w-36 text-right p-3 border rounded-lg bg-blue-100 dark:bg-gray-700 dark:text-white font-semibold">
                                        </div>

                                    </div>

                                    <!-- Footer Actions -->
                                    <div
                                        class="px-8 py-4 bg-gray-50 dark:bg-gray-800 border-t dark:border-gray-700 flex justify-end gap-4">
                                        <button type="button" onclick="closeInvoiceModal()"
                                            class="px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded hover:bg-gray-300">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-semibold">
                                            Save Invoice
                                        </button>
                                    </div>

                                </form>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    const accessories = document.querySelectorAll('.accessory');
                                    const sellingPriceInput = document.getElementById('selling_price');
                                    const totalAmount = document.getElementById('total_amount');
                                    const invoiceForm = document.getElementById('invoiceForm');

                                    // Function to calculate total
                                    function calculateTotal() {
                                        let total = parseFloat(sellingPriceInput.value) || 0;
                                        accessories.forEach(input => {
                                            total += parseFloat(input.value) || 0;
                                        });
                                        totalAmount.value = total.toFixed(2);
                                    }

                                    // Listen for input changes
                                    sellingPriceInput.addEventListener('input', calculateTotal);
                                    accessories.forEach(input => input.addEventListener('input', calculateTotal));

                                    // Reset form when modal closes
                                    window.closeInvoiceModal = function() {
                                        document.getElementById('createInvoiceModal').classList.add('hidden');
                                        invoiceForm.reset();
                                        totalAmount.value = '';
                                        document.getElementById('phone_type').value = '';
                                        document.getElementById('colour').value = '';
                                        document.getElementById('capacity').value = '';
                                    };

                                    // Open modal and focus first input
                                    window.openInvoiceModal = function() {
                                        document.getElementById('createInvoiceModal').classList.remove('hidden');
                                        invoiceForm.querySelector('[name="customer_name"]').focus();
                                    };
                                });
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
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
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
                                                        class="text-xs text-gray-500">{{ $invoice->updated_at->format('d-m-Y') }}</span>
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
                                                </td>

                                                <!-- Prices -->
                                                <td class="px-4 py-2 text-left">
                                                    @if ($invoice->selling_price > 0)
                                                        <span class="font-semibold">Phone Price:</span> LKR
                                                        {{ number_format($invoice->selling_price, 2) }}<br>
                                                    @endif
                                                    @if ($invoice->tempered > 0)
                                                        <span class="font-semibold">Tempered:</span> LKR
                                                        {{ number_format($invoice->tempered, 2) }}<br>
                                                    @endif
                                                    @if ($invoice->back_cover > 0)
                                                        <span class="font-semibold">Back Cover:</span> LKR
                                                        {{ number_format($invoice->back_cover, 2) }}<br>
                                                    @endif
                                                    @if ($invoice->charger > 0)
                                                        <span class="font-semibold">Charger:</span> LKR
                                                        {{ number_format($invoice->charger, 2) }}<br>
                                                    @endif
                                                    @if ($invoice->data_cable > 0)
                                                        <span class="font-semibold">Data Cable:</span> LKR
                                                        {{ number_format($invoice->data_cable, 2) }}<br>
                                                    @endif
                                                    @if ($invoice->hand_free > 0)
                                                        <span class="font-semibold">Hand Free:</span> LKR
                                                        {{ number_format($invoice->hand_free, 2) }}<br>
                                                    @endif
                                                    @if ($invoice->airpods > 0)
                                                        <span class="font-semibold">AirPods:</span> LKR
                                                        {{ number_format($invoice->airpods, 2) }}<br>
                                                    @endif
                                                    @if ($invoice->power_bank > 0)
                                                        <span class="font-semibold">Power Bank:</span> LKR
                                                        {{ number_format($invoice->power_bank, 2) }}<br>
                                                    @endif
                                                </td>

                                                <!-- Total Price -->
                                                <td class="px-4 py-2 text-center font-bold">LKR
                                                    {{ number_format($invoice->total_amount, 2) }}</td>

                                                <td class="px-4 align-middle">
                                                    <div class="inline-flex items-center justify-center gap-2">
                                                        <!-- Print Invoice Button -->
                                                        <button type="button"
                                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm"
                                                            onclick="printInvoice({{ $invoice->id }})">
                                                            Print
                                                        </button>

                                                        <!-- Delete Invoice -->
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
                                                    </div>
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-2 text-center text-gray-500">No invoices
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
            const form = document.querySelector('#addPhoneModal form');
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
@endsection
