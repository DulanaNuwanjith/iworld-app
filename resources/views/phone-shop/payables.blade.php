@php use Carbon\Carbon; @endphp
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                                .swal2-toast {
                                    font-size: 0.875rem;
                                    padding: 0.75rem 1rem;
                                    border-radius: 8px;
                                    background-color: #ffffff !important;
                                    position: relative;
                                    box-sizing: border-box;
                                    color: #6c757d !important;
                                }

                                .swal2-toast .swal2-title,
                                .swal2-toast .swal2-html-container {
                                    color: #495057 !important;
                                }

                                .swal2-toast .swal2-icon {
                                    color: #6c757d !important;
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
                                    border-radius: 0 0 8px 8px;
                                }
                            </style>

                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
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
                            </script>

                            {{-- Filters --}}
                            <div class="flex justify-start">
                                <button onclick="toggleFilterForm()"
                                    class="bg-white border border-gray-500 text-gray-500 hover:text-gray-600 hover:border-gray-600 font-semibold py-1 px-3 rounded shadow flex items-center gap-2 mb-6">
                                    <img src="{{ asset('icons/filter.png') }}" class="w-6 h-6" alt="Filter Icon">
                                    Filters
                                </button>
                            </div>

                            <div id="filterFormContainerPayables" class="mt-4 hidden">
                                <form id="filterFormInvoice" method="GET" action="{{ route('invoices.payables') }}"
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
                                                Numbers
                                            </div>
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

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {

                                    const menus = [
                                        "invoiceNumberDropdownMenu",
                                        "customerNameDropdownMenu"
                                    ].map(id => document.getElementById(id));

                                    // Prevent clicks inside dropdown from closing it
                                    menus.forEach(menu => {
                                        if (menu) menu.addEventListener("click", e => e.stopPropagation());
                                    });

                                    // Toggle dropdown
                                    window.toggleDropdown = function(event, id) {
                                        event.stopPropagation(); // prevent immediate close
                                        closeAllDropdowns(); // close other open dropdowns
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

                                    // Close dropdowns when clicking outside
                                    document.addEventListener("click", closeAllDropdowns);

                                    // Clear filters
                                    window.clearFiltersInvoice = function() {
                                        window.location.href = window.location.pathname;
                                    }

                                });
                            </script>

                            {{-- Header --}}
                            <div class="flex justify-between items-center mb-6">
                                <h1 class="text-2xl font-bold text-gray-800">Payable Invoices</h1>
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
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase break-words">
                                                Customer Name
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase break-words">
                                                Customer Phone
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase break-words">
                                                Customer Address
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase break-words">
                                                Payable Amount
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
                                                <td class="px-4 py-2">
                                                    <span class="font-semibold">{{ $invoice->invoice_number }}</span><br>
                                                    <span
                                                        class="text-xs text-gray-500">{{ $invoice->created_at->format('d M Y') }}</span>
                                                </td>

                                                <td class="px-4 py-2 text-center">
                                                    {{ $invoice->customer_name }}
                                                </td>

                                                <td class="px-4 py-2 text-center">
                                                    {{ $invoice->customer_phone }}
                                                </td>

                                                <td class="px-4 py-2 text-center">
                                                    {{ $invoice->customer_address }}
                                                </td>

                                                <td class="px-4 py-2 text-center font-bold text-red-600">
                                                    {{ number_format($invoice->payable_amount, 2) }}
                                                </td>

                                                <td class="px-4 align-middle">
                                                    <div class="inline-flex items-center justify-center gap-2">
                                                        <!-- Pay Button -->
                                                        <button type="button"
                                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm"
                                                            onclick="payInvoice({{ $invoice->id }}, {{ $invoice->payable_amount }})">
                                                            Pay
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-2 text-center text-gray-500">No payable
                                                    invoices found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            <div class="py-6 flex justify-center">
                                {{ $invoices->links() }}
                            </div>

                        </div>

                        <!-- Payable Payment Modal -->
                        <div id="payInvoiceModal"
                            class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center px-4 py-6">
                            <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 relative">
                                <h2 class="text-xl font-bold mb-4">Settle Payment</h2>

                                <form id="payInvoiceForm" method="POST">
                                    @csrf
                                    <input type="hidden" name="invoice_id" id="modalInvoiceId">

                                    <div class="mb-4">
                                        <label class="block text-gray-700 font-semibold mb-1">Payable Amount</label>
                                        <input type="number" name="amount" id="modalPayableAmount"
                                            class="w-full border rounded px-3 py-2" min="0" step="0.01"
                                            required>
                                    </div>

                                    <div class="flex justify-end gap-2">
                                        <button type="button" onclick="closePayModal()"
                                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded text-gray-700">Cancel</button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">Paid</button>
                                    </div>
                                </form>
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
            spinner.classList.remove("hidden");
            window.requestAnimationFrame(() => {
                spinner.classList.add("hidden");
            });
        });
    </script>
    <script>
        function payInvoice(invoiceId, currentPayable) {
            document.getElementById('modalInvoiceId').value = invoiceId;
            document.getElementById('modalPayableAmount').value = currentPayable.toFixed(2);
            document.getElementById('modalPayableAmount').max = currentPayable.toFixed(2);

            document.getElementById('payInvoiceModal').classList.remove('hidden');
        }

        function closePayModal() {
            document.getElementById('payInvoiceModal').classList.add('hidden');
        }

        // Update form action dynamically before submit
        document.getElementById('payInvoiceForm').addEventListener('submit', function(e) {
            const invoiceId = document.getElementById('modalInvoiceId').value;
            this.action = `/invoices/pay/${invoiceId}`; // set dynamic route
        });
    </script>

    <script>
        function toggleFilterForm() {
            const form = document.getElementById('filterFormContainerPayables');
            form.classList.toggle('hidden');
        }
    </script>
@endsection
