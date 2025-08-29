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
                                <button onclick="toggleReportForm()"
                                    class="bg-white border border-gray-500 text-gray-500 hover:text-gray-600 hover:border-gray-600 font-semibold py-1 px-3 rounded shadow flex items-center gap-2 mb-6 ml-2">
                                    Generate Report
                                </button>
                            </div>

                            <div id="filterFormContainer" class="hidden mt-4">

                            </div>

                            {{-- Generate Reports for Customer Coordinator --}}
                            <div class="flex-1">
                                <div id="reportFormContainer" class="hidden mt-4">

                                </div>

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
                                                class="font-bold sticky top-0 bg-gray-200  px-4 py-3 w-32 text-xs text-gray-600  uppercase whitespace-normal break-words">
                                                Order No
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Buyer Details
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-48 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Item Details
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-48 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Mails & Passwords
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Price & Payment
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Note
                                            </th>
                                            {{-- <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Action
                                            </th> --}}
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
                                                </td>

                                                <!-- Buyer Details -->
                                                <td class="px-4 py-3 text-xs text-left">
                                                    <div>Name: {{ $order->buyer_name }}</div>
                                                    <div>ID: {{ $order->buyer_id }}</div>
                                                    <div>Address: {{ $order->buyer_address }}</div>
                                                    <div>Phone 1: {{ $order->phone_1 }}</div>
                                                    <div>Phone 2: {{ $order->phone_2 }}</div>
                                                    <div class="mt-1 flex gap-1">
                                                        @if ($order->id_photo)
                                                            <img src="{{ asset('storage/' . $order->id_photo) }}"
                                                                alt="ID Photo" class="w-16 h-16 object-cover rounded">
                                                        @endif
                                                        @if ($order->electricity_bill_photo)
                                                            <img src="{{ asset('storage/' . $order->electricity_bill_photo) }}"
                                                                alt="Electricity Bill"
                                                                class="w-16 h-16 object-cover rounded">
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Item Details -->
                                                <td class="px-4 py-3 text-xs text-left">
                                                    <div>Item: {{ $order->item_name }}</div>
                                                    <div>EMI: {{ $order->emi_number }}</div>
                                                    <div>Colour: {{ $order->colour }}</div>
                                                    <div class="mt-1 flex gap-1">
                                                        @if ($order->photo_1)
                                                            <img src="{{ asset('storage/' . $order->photo_1) }}"
                                                                alt="Photo 1" class="w-16 h-16 object-cover rounded">
                                                        @endif
                                                        @if ($order->photo_2)
                                                            <img src="{{ asset('storage/' . $order->photo_2) }}"
                                                                alt="Photo 2" class="w-16 h-16 object-cover rounded">
                                                        @endif
                                                        @if ($order->photo_about)
                                                            <img src="{{ asset('storage/' . $order->photo_about) }}"
                                                                alt="About Photo" class="w-16 h-16 object-cover rounded">
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Mails & Passwords -->
                                                <td class="px-4 py-3 text-xs text-left">
                                                    <div>iCloud: {{ $order->icloud_mail }}</div>
                                                    <div>Password: {{ $order->icloud_password }}</div>
                                                    <div>Screen Lock: {{ $order->screen_lock_password }}</div>
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

                                                <td class="px-4 py-3 text-xs text-center">
                                                    <div>Full amount: LKR {{ number_format($order->price, 2) }}</div>

                                                    <div class="mt-2">
                                                        @php
                                                            $paidInstallments = $order->payments
                                                                ->pluck('installment_number')
                                                                ->toArray();
                                                            $totalPaid = $order->payments->sum('amount');
                                                            $totalOverdue = $order->payments->sum('overdue_amount');
                                                            $actualPaid = $totalPaid - $totalOverdue;
                                                            $balance = max($order->price - $actualPaid, 0);
                                                            $installmentAmount = round($order->price / 3, 2); // equal installments
                                                        @endphp

                                                        @for ($i = 1; $i <= 3; $i++)
                                                            @php
                                                                $payment = $order->payments
                                                                    ->where('installment_number', $i)
                                                                    ->first();
                                                                $paidAmount = $payment->amount ?? 0;
                                                                $overdueAmount = $payment->overdue_amount ?? 0;
                                                                $expectedDate = $payment->expected_date ?? null;
                                                                $installmentRemaining = max(
                                                                    $installmentAmount - ($paidAmount - $overdueAmount),
                                                                    0,
                                                                );
                                                                $canPay =
                                                                    $i == 1 ||
                                                                    ($i == 2 && in_array(1, $paidInstallments)) ||
                                                                    ($i == 3 && in_array(2, $paidInstallments));
                                                            @endphp

                                                            @if ($paidAmount > 0)
                                                                <div class="text-green-600 text-xs mt-1">
                                                                    Payment {{ $i }}: LKR
                                                                    {{ number_format($paidAmount - $overdueAmount, 2) }}
                                                                    @if ($overdueAmount > 0)
                                                                        (Overdue: LKR
                                                                        {{ number_format($overdueAmount, 2) }})
                                                                    @endif
                                                                    @if ($payment->paid_at)
                                                                        ({{ \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d') }})
                                                                    @endif
                                                                </div>
                                                            @elseif ($installmentRemaining > 0)
                                                                <div class="text-blue-600 text-xs mt-1">
                                                                    Payment {{ $i }}: Pending
                                                                    @if ($expectedDate)
                                                                        (expected date:
                                                                        {{ \Carbon\Carbon::parse($expectedDate)->format('Y-m-d') }})
                                                                    @endif
                                                                </div>

                                                                @if ($canPay)
                                                                    <button
                                                                        onclick="openPaymentModal({{ $order->id }}, {{ $i }}, {{ $installmentRemaining }}, '{{ $expectedDate }}')"
                                                                        class="px-2 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600">
                                                                        Pay
                                                                    </button>
                                                                @endif
                                                            @endif
                                                        @endfor

                                                        <!-- Remaining Balance (excluding overdue amounts) -->
                                                        @php
                                                            $totalPaid = $order->payments->sum('amount'); // includes overdue
                                                            $totalOverdue = $order->payments->sum('overdue_amount'); // sum of all overdue
                                                            $balance = max(
                                                                $order->price - ($totalPaid - $totalOverdue),
                                                                0,
                                                            ); // excludes overdue
                                                        @endphp

                                                        <div class="mt-2 text-red-600 font-medium">
                                                            Balance: LKR {{ number_format($balance, 2) }}
                                                        </div>

                                                        @if ($totalOverdue > 0)
                                                            <div class="mt-1 text-orange-600 text-xs font-medium">
                                                                Total Overdue Paid: LKR
                                                                {{ number_format($totalOverdue, 2) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Payment Modal -->
                                                <div id="paymentModal"
                                                    class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
                                                    <div class="bg-white rounded-lg p-6 w-96 relative">
                                                        <h2 class="text-lg font-semibold mb-4">Pay Installment</h2>
                                                        <form id="paymentForm" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="installment_number"
                                                                id="installmentNumber">

                                                            <div class="mb-2">
                                                                <label class="block text-sm font-medium">Amount</label>
                                                                <input type="number" name="amount" id="installmentAmount"
                                                                    class="w-full border px-2 py-1 rounded" required>
                                                            </div>

                                                            <!-- Expected Date -->
                                                            <div class="mb-4" id="expectedDateContainer">
                                                                <label class="block text-sm font-medium">Expected
                                                                    Date</label>
                                                                <input type="text" id="expectedDate"
                                                                    class="w-full border px-2 py-1 rounded bg-gray-100"
                                                                    readonly>
                                                            </div>

                                                            <!-- Overdue Days (hide for Payment 1) -->
                                                            <div class="mb-4" id="overdueContainer">
                                                                <label class="block text-sm font-medium">Overdue
                                                                    Days</label>
                                                                <input type="number" name="overdue_days" id="overdueDays"
                                                                    class="w-full border px-2 py-1 rounded" value="0"
                                                                    min="0">
                                                                <div class="text-xs text-gray-500 mt-1"
                                                                    id="overdueAmountText">Overdue Amount: LKR 0</div>
                                                            </div>

                                                            <div class="mb-4">
                                                                <div class="text-xs text-gray-500 mt-1"
                                                                    id="totalPaymentText">Total Payment: LKR 0</div>
                                                            </div>

                                                            <div class="flex justify-end gap-2">
                                                                <button type="button" onclick="closePaymentModal()"
                                                                    class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                                                                <button type="submit"
                                                                    class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                                                    Pay
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                                <!-- Payment Modal -->
                                                <div id="paymentModal"
                                                    class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
                                                    <div class="bg-white rounded-lg p-6 w-96 relative">
                                                        <h2 class="text-lg font-semibold mb-4">Pay Installment</h2>
                                                        <form id="paymentForm" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="installment_number"
                                                                id="installmentNumber">

                                                            <div class="mb-2">
                                                                <label class="block text-sm font-medium">Amount</label>
                                                                <input type="number" name="amount"
                                                                    id="installmentAmount"
                                                                    class="w-full border px-2 py-1 rounded" required>
                                                            </div>

                                                            <div class="mb-4">
                                                                <label class="block text-sm font-medium">Overdue
                                                                    Days</label>
                                                                <input type="number" name="overdue_days"
                                                                    id="overdueDays"
                                                                    class="w-full border px-2 py-1 rounded" value="0"
                                                                    min="0">
                                                                <div class="text-xs text-gray-500 mt-1"
                                                                    id="overdueAmountText">Overdue Amount: LKR 0</div>
                                                            </div>

                                                            <div class="mb-4">
                                                                <div class="text-xs text-gray-500 mt-1"
                                                                    id="totalPaymentText">Total Payment: LKR 0</div>
                                                            </div>

                                                            <div class="flex justify-end gap-2">
                                                                <button type="button" onclick="closePaymentModal()"
                                                                    class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                                                                <button type="submit"
                                                                    class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                                                    Pay
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

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
                                                {{-- <td class="px-4 py-3 text-xs text-center">
                                                    <form id="delete-form-{{ $order->id }}"
                                                        action="{{ route('finance.destroy', $order->id) }}" method="POST"
                                                        class="flex justify-center">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            onclick="confirmDelete('{{ $order->id }}')"
                                                            class="bg-red-600 h-10 mt-1 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="py-6 flex justify-center">
                                {{ $financeOrders->links() }}
                            </div>

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
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 ">Item
                                                        Created Date</label>
                                                    <input type="date" name="item_created_date" required
                                                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                </div>

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
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">House
                                                            Electricity Bill</label>
                                                        <input type="file" name="electricity_bill_photo"
                                                            accept="image/*" class="w-full mt-1 text-sm">
                                                    </div>
                                                </div>

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
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">Price</label>
                                                        <input type="number" name="price" required step="0.01"
                                                            min="0"
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                            placeholder="Enter amount in LKR">
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
                                                    <div class="w-1/3">
                                                        <label class="block text-sm font-medium text-gray-700">Photo
                                                            About</label>
                                                        <input type="file" name="photo_about" accept="image/*"
                                                            class="w-full mt-1 text-sm">
                                                    </div>
                                                </div>

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
                                                            Lock Password</label>
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
            const form = document.getElementById('filterFormContainer');
            form.classList.toggle('hidden');
        }

        function toggleReportForm() {
            const form = document.getElementById('reportFormContainer');
            form.classList.toggle('hidden');
        }
    </script>
    <script>
        function openPaymentModal(orderId, installmentNumber, installmentRemaining, expectedDate = null) {
            const modal = document.getElementById('paymentModal');
            modal.classList.remove('hidden');

            const form = document.getElementById('paymentForm');
            form.action = `/finance/payInstallment/${orderId}/${installmentNumber}`;

            const amountInput = document.getElementById('installmentAmount');
            const overdueInput = document.getElementById('overdueDays');
            const overdueText = document.getElementById('overdueAmountText');
            const totalText = document.getElementById('totalPaymentText');
            const expectedInput = document.getElementById('expectedDate');
            const expectedContainer = document.getElementById('expectedDateContainer');
            const overdueContainer = document.getElementById('overdueContainer');

            document.getElementById('installmentNumber').value = installmentNumber;
            amountInput.value = installmentRemaining;

            // Show expected date if provided
            if (expectedDate) {
                expectedInput.value = expectedDate;
                expectedContainer.style.display = 'block';
            } else {
                expectedContainer.style.display = 'none';
            }

            // Hide overdue input for Payment 1
            if (installmentNumber === 1) {
                overdueContainer.style.display = 'none';
            } else {
                overdueContainer.style.display = 'block';
                overdueInput.value = 0;
            }

            // Remove old listeners
            amountInput.oninput = null;
            overdueInput.oninput = null;

            function updatePayment() {
                const days = parseInt(overdueInput.value) || 0;
                const rate = installmentNumber === 2 ? 200 : (installmentNumber === 3 ? 500 : 0);
                const overdueAmount = days * rate;

                overdueText.innerText = `Overdue Amount: LKR ${overdueAmount.toLocaleString()}`;
                const total = parseFloat(amountInput.value) + overdueAmount;
                totalText.innerText = `Total Payment: LKR ${total.toLocaleString()}`;
            }

            overdueInput.addEventListener('input', updatePayment);
            amountInput.addEventListener('input', updatePayment);

            updatePayment();
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
    </script>
@endsection
