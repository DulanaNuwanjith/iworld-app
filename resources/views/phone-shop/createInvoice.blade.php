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
                                class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center py-5">
                                <div class="w-full max-w-[700px] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-4 transform transition-all scale-95 max-h-[calc(100vh-10rem)] overflow-y-auto"
                                    onclick="event.stopPropagation()">
                                    <div class="max-w-[600px] mx-auto p-8">
                                        <h2 class="text-2xl font-semibold mb-8 text-gray-900 mt-4 text-center">
                                            Create Invoice
                                        </h2>

                                        <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
                                            @csrf

                                            <!-- Customer Details -->
                                            <div class="mb-6">
                                                <h3 class="text-xl font-semibold mb-4">Customer Details</h3>
                                                <input name="customer_name" placeholder="Customer Name" required
                                                    class="w-full mb-2 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                                <input name="customer_phone" placeholder="Customer Phone" required
                                                    class="w-full mb-2 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                                <textarea name="customer_address" placeholder="Customer Address"
                                                    class="w-full mb-4 p-2 border rounded-md dark:bg-gray-700 dark:text-white"></textarea>
                                            </div>

                                            <!-- Phone Selection -->
                                            <div class="mb-6">
                                                <h3 class="text-xl font-semibold mb-4">Select Phone</h3>
                                                <select id="emi" name="emi" required
                                                    class="w-full mb-2 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                                    <option value="">Select EMI</option>
                                                    @foreach ($emis as $emi)
                                                        <option value="{{ $emi->emi }}"
                                                            data-phone_type="{{ $emi->phone_type }}"
                                                            data-colour="{{ $emi->colour }}"
                                                            data-capacity="{{ $emi->capacity }}">
                                                            {{ $emi->emi }} - {{ $emi->phone_type }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <input id="phone_type" readonly placeholder="Phone Type"
                                                    class="w-full mb-2 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                                <input id="colour" readonly placeholder="Colour"
                                                    class="w-full mb-2 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                                <input id="capacity" readonly placeholder="Capacity"
                                                    class="w-full mb-4 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                            </div>

                                            <!-- Selling Price -->
                                            <div class="mb-6">
                                                <h3 class="text-xl font-semibold mb-4">Selling Price</h3>
                                                <input type="number" name="selling_price" id="selling_price"
                                                    placeholder="Enter Selling Price" min="0"
                                                    class="w-full mb-4 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                            </div>

                                            <!-- Accessories -->
                                            <div class="mb-6">
                                                <h3 class="text-xl font-semibold mb-4">Accessories</h3>
                                                <input type="number" name="tempered" placeholder="Tempered price"
                                                    min="0"
                                                    class="accessory w-full mb-2 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                                <input type="number" name="back_cover" placeholder="Back cover price"
                                                    min="0"
                                                    class="accessory w-full mb-2 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                                <input type="number" name="charger" placeholder="Charger price"
                                                    min="0"
                                                    class="accessory w-full mb-2 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                                <input type="number" name="data_cable" placeholder="Data cable price"
                                                    min="0"
                                                    class="accessory w-full mb-2 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                                <input type="number" name="hand_free" placeholder="Hand free price"
                                                    min="0"
                                                    class="accessory w-full mb-2 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                                <input type="number" name="airpods" placeholder="AirPods price"
                                                    min="0"
                                                    class="accessory w-full mb-2 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                                <input type="number" name="power_bank" placeholder="Power bank price"
                                                    min="0"
                                                    class="accessory w-full mb-4 p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                                            </div>

                                            <!-- Total Amount -->
                                            <div class="mb-6">
                                                <h3 class="text-xl font-semibold mb-2">Total Amount</h3>
                                                <input type="text" id="total_amount" readonly
                                                    class="w-full mb-4 p-2 border rounded-md bg-gray-100 dark:bg-gray-700 dark:text-white">
                                            </div>

                                            <!-- Actions -->
                                            <div class="flex justify-end space-x-3">
                                                <button type="button"
                                                    onclick="document.getElementById('createInvoiceModal').classList.add('hidden')"
                                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded hover:bg-gray-300">
                                                    Cancel
                                                </button>

                                                <button type="submit"
                                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                                    Save Invoice
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- JS -->
                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    // Auto-fill phone details
                                    const emiSelect = document.getElementById('emi');
                                    emiSelect.addEventListener('change', function() {
                                        const selected = this.selectedOptions[0];
                                        if (!selected.value) {
                                            document.getElementById('phone_type').value = '';
                                            document.getElementById('colour').value = '';
                                            document.getElementById('capacity').value = '';
                                            return;
                                        }
                                        document.getElementById('phone_type').value = selected.dataset.phone_type;
                                        document.getElementById('colour').value = selected.dataset.colour;
                                        document.getElementById('capacity').value = selected.dataset.capacity;
                                    });

                                    // Calculate total amount live
                                    const accessories = document.querySelectorAll('.accessory');
                                    const sellingPriceInput = document.getElementById('selling_price');
                                    const totalAmount = document.getElementById('total_amount');

                                    function calculateTotal() {
                                        let total = parseFloat(sellingPriceInput.value) || 0;
                                        accessories.forEach(input => {
                                            total += parseFloat(input.value) || 0;
                                        });
                                        totalAmount.value = total.toFixed(2);
                                    }

                                    accessories.forEach(input => input.addEventListener('input', calculateTotal));
                                    sellingPriceInput.addEventListener('input', calculateTotal);
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

                                                <td class="px-4 py-2 flex justify-center gap-2">
                                                    <!-- Print Invoice Button -->
                                                    <button type="button"
                                                        class="bg-blue-500 h-10 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm"
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
                                                            class="bg-red-600 h-10 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"
                                                            onclick="confirmDelete('{{ $invoice->id }}')">
                                                            Delete
                                                        </button>
                                                    </form>
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
@endsection
