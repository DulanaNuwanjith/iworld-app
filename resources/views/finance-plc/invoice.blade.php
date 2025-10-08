<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans p-6">
    <div class="invoice-box max-w-3xl mx-auto bg-white p-8 border border-gray-200">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="logo text-4xl font-bold text-gray-800">
                Iworld
            </div>
            <div class="shop-details text-right text-sm text-gray-600 leading-relaxed">
                <strong>Iworld</strong> - Mobile Selling Shop<br>
                Tel: 076 411 28 49 | 077 20 87 649<br>
                Email: iworldgampaha@gmail.com<br>
                Main Office: No. 169, 5th Floor,<br>
                Ward City Shopping Complex, Gampaha<br>
                Branch: No. 03, Ja-Ela Road, Gampaha
            </div>
        </div>

        <!-- Invoice Date and Number -->
        <div class="flex justify-between items-center mb-6">
            <div class="text-gray-700">
                <p>Date: {{ \Carbon\Carbon::now()->format('Y-m-d') }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-semibold text-gray-800">Invoice {{ $order->order_number }}</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6 text-sm">
            <!-- Buyer Details -->
            <div>
                <h4 class="text-gray-800 font-semibold mb-2">Buyer Details:</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="font-semibold w-28">Name:</span>
                        <span>{{ $order->buyer_name }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-semibold w-28">ID:</span>
                        <span>{{ $order->buyer_id }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-semibold w-28">Address:</span>
                        <span>{{ $order->buyer_address }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-semibold w-28">Phone:</span>
                        <span>{{ $order->phone_1 }} <br> {{ $order->phone_2 }}</span>
                    </div>
                </div>
            </div>

            <!-- Item Details -->
            <div>
                <h4 class="text-gray-800 font-semibold mb-2">Item Details:</h4>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="font-semibold w-28">Item:</span>
                        <span>{{ $order->item_name }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-semibold w-28">Colour:</span>
                        <span>{{ $order->colour }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-semibold w-28">EMI:</span>
                        <span>{{ $order->emi_number }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-semibold w-28">Price:</span>
                        <span>LKR {{ number_format($order->price, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Minimum Payment Info -->
        <div class="text-right text-gray-700 font-medium mt-4 mb-4">
            @php
                $min_payment =
                    $order->amount_of_installments > 0 ? $order->due_payment / $order->amount_of_installments : 0;
            @endphp
            Minimum payment to be paid per installment:
            <span class="text-blue-600 font-semibold">
                LKR {{ number_format($min_payment, 2) }}
            </span>
        </div>

        <!-- Installments -->
        <h4 class="text-gray-800 font-semibold mb-2">Installments:</h4>
        <table class="w-full table-auto text-sm border border-gray-300 mb-6">
            <thead>
                <tr class="bg-gray-100 font-semibold text-gray-800">
                    <th class="px-3 py-2 border">Installment</th>
                    <th class="px-3 py-2 border">Expected Date</th>
                    <th class="px-3 py-2 border">Overdue Days</th>
                    <th class="px-3 py-2 border">Overdue Amount</th>
                    <th class="px-3 py-2 border">Amount Paid</th>
                    <th class="px-3 py-2 border">Paid Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->payments as $payment)
                    <tr class="text-center">
                        <td class="py-2 border text-sm">{{ $payment->installment_number }}</td>
                        <td class="py-2 border text-sm">
                            {{ \Carbon\Carbon::parse($payment->expected_date)->format('Y-m-d') }}</td>
                        <td class="py-2 border text-sm">{{ $payment->overdue_days ?? 0 }}</td>
                        <td class="py-2 border text-sm">
                            {{ $payment->overdue_amount ? 'LKR ' . number_format($payment->overdue_amount, 2) : 0 }}
                        </td>
                        <td class="py-2 border text-sm">
                            {{ $payment->paid_amount ? 'LKR ' . number_format($payment->paid_amount, 2) : '-' }}</td>
                        <td class="py-2 border text-sm">
                            {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d') : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="text-right space-y-1 mb-4">
            <p class="text-green-600 font-semibold">Total Paid: LKR
                {{ number_format($order->paid_amount_fullamount, 2) }}</p>
            <p class="text-yellow-500 font-semibold">Total Overdue Amount: LKR
                {{ number_format($order->over_due_payment_fullamount, 2) }}</p>
            <p class="text-red-600 font-semibold">Balance: LKR {{ number_format($order->remaining_amount, 2) }}</p>
        </div>

        <!-- Notes -->
        <div class="text-sm text-red-600">
            <p>Note:</p>
            <p>- If you are overdue on an expected installment date, a charge of LKR 200 per day will apply.</p>
        </div>
    </div>
</body>

</html>
