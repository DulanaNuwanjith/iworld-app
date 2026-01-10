<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* A4 page setup */
        @page {
            size: A4;
            margin: 0;
        }

        body {
            background: #f3f4f6;
            margin: 0;
        }

        .invoice-box {
            position: relative;
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            padding: 12mm 12mm 12mm 12mm;
            box-sizing: border-box;
            background: white;
            border: 1px solid #d1d5db;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
        }

        @media print {
            body {
                background: white;
            }

            .invoice-box {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>

<body class="font-sans">
    <div class="invoice-box">
        <div class="content">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <div class="w-72">
                    <img src="{{ asset('images/logo.png') }}" alt="Iworld Logo" class="w-full h-auto">
                </div>
                <div class="text-sm text-gray-600 leading-relaxed text-right">
                    <strong>Iworld</strong> - Mobile Selling Shop<br>
                    Tel: 076 411 28 49 | 077 20 87 649<br>
                    Email: iworldgampaha@gmail.com<br>
                    Main Office: No. 169, 5th Floor,<br>
                    Ward City Shopping Complex, Gampaha<br>
                    Branch: No. 03, Ja-Ela Road, Gampaha
                </div>
            </div>

            <!-- Invoice Info -->
            <div class="flex justify-between items-center mb-6">
                <div class="text-gray-700">
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::now()->format('d M Y') }}</p><br>
                    <p><strong>Coordinator:</strong> {{ $invoice->worker->name }}</p>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-semibold text-gray-800">Invoice No : {{ $invoice->invoice_number }}</h2>
                </div>
            </div>

            <!-- Customer & Phone Details -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                <div class="break-words">
                    <h4 class="text-gray-800 font-semibold mb-2 border-b pb-1">Customer Details</h4>
                    <p><strong>Name:</strong> {{ $invoice->customer_name }}</p>
                    <p><strong>Phone:</strong> {{ $invoice->customer_phone }}</p>
                    <p><strong>Address:</strong> {{ $invoice->customer_address ?? '-' }}</p>
                </div>
                <!-- Phone Details (only show if available) -->
                @if ($invoice->emi && $invoice->phone_type)
                    <div class="break-words">
                        <h4 class="text-gray-800 font-semibold mb-2 border-b pb-1">Phone Details</h4>
                        <p><strong>EMI:</strong> {{ $invoice->emi }}</p>
                        <p><strong>Model:</strong> {{ $invoice->phone_type }}</p>
                        <p><strong>Capacity:</strong> {{ $invoice->capacity }}</p>
                        <p><strong>Colour:</strong> {{ $invoice->colour }}</p>
                    </div>
                @endif
                <!-- Exchanged Phone Details (only show if available) -->
                @if ($invoice->exchange_emi)
                    <div class="break-words">
                        <h4 class="text-gray-800 font-semibold mb-2 border-b pb-1">Exchanged Phone</h4>
                        <p><strong>EMI:</strong> {{ $invoice->exchange_emi }}</p>
                        <p><strong>Model:</strong> {{ $invoice->exchange_phone_type }}</p>
                        <p><strong>Capacity:</strong> {{ $invoice->exchange_capacity }}</p>
                        <p><strong>Colour:</strong> {{ $invoice->exchange_colour }}</p>
                    </div>
                @endif
            </div>

            <!-- Price Table -->
            <div class="mb-6">
                <h4 class="text-gray-800 font-semibold mb-2 border-b pb-1">Price Details</h4>
                <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-3 py-2 text-left">Item</th>
                            <th class="border px-3 py-2 text-right">Price (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $customerPay = $invoice->selling_price;
                        @endphp

                        @if ($invoice->selling_price > 0)
                            <tr>
                                <td class="border px-3 py-2">Phone Price</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($invoice->selling_price, 2) }}
                                </td>
                            </tr>
                        @endif

                        @if ($invoice->invoiceAccessories->isNotEmpty())
                            @foreach ($invoice->invoiceAccessories as $acc)
                                @php
                                    $totalAccPrice = $acc->selling_price_accessory * $acc->quantity;
                                    $customerPay += $totalAccPrice;
                                @endphp
                                <tr>
                                    <td class="border px-3 py-2">{{ $acc->accessory_name }} (Qty:
                                        {{ $acc->quantity }})</td>
                                    <td class="border px-3 py-2 text-right">{{ number_format($totalAccPrice, 2) }}</td>
                                </tr>
                            @endforeach
                        @endif

                        @if ($invoice->exchange_emi)
                            <tr class="bg-red-100">
                                <td class="border px-3 py-2 font-semibold text-red-700">Exchanged Phone Deduction</td>
                                <td class="border px-3 py-2 text-right text-red-700">
                                    -{{ number_format($invoice->exchange_cost, 2) }}</td>
                            </tr>
                            @php
                                $customerPay -= $invoice->exchange_cost;
                            @endphp
                        @endif

                        @if ($invoice->payable_amount)
                            <tr class="bg-blue-100">
                                <td class="border px-3 py-2 font-semibold text-blue-700">Payable Amount</td>
                                <td class="border px-3 py-2 text-right text-blue-700">
                                    -{{ number_format($invoice->payable_amount, 2) }}</td>
                            </tr>
                            @php
                                $customerPay -= $invoice->payable_amount;
                            @endphp
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 font-bold">
                            <td class="border px-3 py-2 text-right">Total Amount to Pay</td>
                            <td class="border px-3 py-2 text-right">LKR {{ number_format($customerPay, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes -->
            <div class="text-sm text-gray-700 mt-4">
                <p><strong>Note:</strong> Thank you for your purchase! Please check your phone and accessories at the
                    time of delivery.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="absolute bottom-20 left-0 w-full text-xs text-gray-500 text-center">
            Iworld - Mobile Selling Shop | Tel: 076 411 28 49 | 077 20 87 649 | iworldgampaha@gmail.com
        </div>
    </div>
</body>

</html>
