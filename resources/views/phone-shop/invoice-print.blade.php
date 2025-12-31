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
            margin: 0; /* We'll handle margin in container */
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
            padding: 20mm;
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
                <div class="text-4xl font-bold text-gray-800">Iworld</div>
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
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::now()->format('Y-m-d') }}</p>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-semibold text-gray-800">Invoice No : {{ $invoice->invoice_number }}</h2>
                </div>
            </div>

            <!-- Customer & Phone Details -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div>
                    <h4 class="text-gray-800 font-semibold mb-2 border-b pb-1">Customer Details</h4>
                    <p><strong>Name:</strong> {{ $invoice->customer_name }}</p>
                    <p><strong>Phone:</strong> {{ $invoice->customer_phone }}</p>
                    <p><strong>Address:</strong> {{ $invoice->customer_address ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-gray-800 font-semibold mb-2 border-b pb-1">Phone Details</h4>
                    <p><strong>EMI:</strong> {{ $invoice->emi }}</p>
                    <p><strong>Model:</strong> {{ $invoice->phone_type }}</p>
                    <p><strong>Capacity:</strong> {{ $invoice->capacity }}</p>
                    <p><strong>Colour:</strong> {{ $invoice->colour }}</p>
                </div>
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
                        @if ($invoice->selling_price > 0)
                            <tr>
                                <td class="border px-3 py-2">Phone Price</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($invoice->selling_price, 2) }}</td>
                            </tr>
                        @endif
                        @if ($invoice->tempered > 0)
                            <tr>
                                <td class="border px-3 py-2">Tempered</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($invoice->tempered, 2) }}</td>
                            </tr>
                        @endif
                        @if ($invoice->back_cover > 0)
                            <tr>
                                <td class="border px-3 py-2">Back Cover</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($invoice->back_cover, 2) }}</td>
                            </tr>
                        @endif
                        @if ($invoice->charger > 0)
                            <tr>
                                <td class="border px-3 py-2">Charger</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($invoice->charger, 2) }}</td>
                            </tr>
                        @endif
                        @if ($invoice->data_cable > 0)
                            <tr>
                                <td class="border px-3 py-2">Data Cable</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($invoice->data_cable, 2) }}</td>
                            </tr>
                        @endif
                        @if ($invoice->hand_free > 0)
                            <tr>
                                <td class="border px-3 py-2">Hand Free</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($invoice->hand_free, 2) }}</td>
                            </tr>
                        @endif
                        @if ($invoice->airpods > 0)
                            <tr>
                                <td class="border px-3 py-2">AirPods</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($invoice->airpods, 2) }}</td>
                            </tr>
                        @endif
                        @if ($invoice->power_bank > 0)
                            <tr>
                                <td class="border px-3 py-2">Power Bank</td>
                                <td class="border px-3 py-2 text-right">{{ number_format($invoice->power_bank, 2) }}</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 font-bold">
                            <td class="border px-3 py-2 text-right">Total</td>
                            <td class="border px-3 py-2 text-right">LKR {{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes -->
            <div class="text-sm text-gray-700 mt-4">
                <p><strong>Note:</strong> Thank you for your purchase! Please check your phone and accessories at the time of delivery.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="absolute bottom-20 left-0 w-full text-xs text-gray-500 text-center">
            Iworld - Mobile Selling Shop | Tel: 076 411 28 49 | 077 20 87 649 | iworldgampaha@gmail.com
        </div>
    </div>
</body>

</html>
