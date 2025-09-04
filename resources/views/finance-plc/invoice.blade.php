<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header img {
            max-height: 80px;
        }

        .header .shop-details {
            text-align: right;
            font-size: 14px;
        }

        h2,
        h4 {
            margin: 5px 0;
        }

        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table td {
            padding: 5px;
            vertical-align: top;
        }

        table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .total {
            font-weight: bold;
            margin-top: 20px;
        }

        .note {
            margin-top: 15px;
            font-size: 14px;
            color: #d9534f;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/invoicelogo.png') }}" alt="Iworld Logo">
            </div>
            <div class="shop-details">
                <strong>Iworld</strong> - Mobile Selling Shop<br>
                Tel: +076 411 28 49 | 077 20 87 649<br>
                Email: iworldgampaha@gmail.com<br>
                Main Office: #No. 169, 5th Floor, Ward City Shopping Complex, Gampaha<br>
                Branch: No. 03, Ja-Ela Road, Gampaha
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <!-- Left: Date -->
            <div>
                <p>Date: {{ \Carbon\Carbon::now()->format('Y-m-d') }}</p>
            </div>

            <!-- Right: Invoice Number -->
            <div>
                <h2>Invoice {{ $order->order_number }}</h2>
            </div>
        </div>

        <h4>Buyer Details:</h4>
        <table>
            <tr>
                <td style="width: 150px;">Name:</td>
                <td>{{ $order->buyer_name }}</td>
            </tr>
            <tr>
                <td>ID:</td>
                <td>{{ $order->buyer_id }}</td>
            </tr>
            <tr>
                <td>Address:</td>
                <td>{{ $order->buyer_address }}</td>
            </tr>
            <tr>
                <td>Phone:</td>
                <td>{{ $order->phone_1 }} {{ $order->phone_2 }}</td>
            </tr>
        </table>

        <h4 style="margin-top: 2rem;">Item Details:</h4>
        <table>
            <tr>
                <td style="width: 150px;">Item:</td>
                <td>{{ $order->item_name }}</td>
            </tr>
            <tr>
                <td>Colour:</td>
                <td>{{ $order->colour }}</td>
            </tr>
            <tr>
                <td>EMI:</td>
                <td>{{ $order->emi_number }}</td>
            </tr>
            <tr>
                <td>Price:</td>
                <td>LKR {{ number_format($order->price, 2) }}</td>
            </tr>
        </table>

        <h4 style="margin-top: 2rem;">Installments:</h4>
        @php
            $firstPayment = $order->payments->where('installment_number', 1)->first();
            $firstPaidAt = $firstPayment ? \Carbon\Carbon::parse($firstPayment->paid_at) : null;
            $firstPaidAmount = $firstPayment ? $firstPayment->amount - $firstPayment->overdue_amount : 0;

            $secondExpected = $firstPaidAt ? $firstPaidAt->copy()->addMonth() : null;
            $thirdExpected = $firstPaidAt ? $firstPaidAt->copy()->addMonths(2) : null;

            $installments = [
                [
                    'number' => 1,
                    'date' => $firstPaidAt ? $firstPaidAt->format('Y-m-d') : '-',
                    'amount' => $firstPaidAmount,
                ],
                ['number' => 2, 'date' => $secondExpected ? $secondExpected->format('Y-m-d') : '-', 'amount' => null],
                ['number' => 3, 'date' => $thirdExpected ? $thirdExpected->format('Y-m-d') : '-', 'amount' => null],
            ];
        @endphp

        <table style="border-collapse: separate; border-spacing: 0 10px;">
            <tr class="heading">
                <td>Installment</td>
                <td>Expected Date</td>
                <td>Amount Paid</td>
            </tr>
            @foreach ($installments as $installment)
                <tr>
                    <td>{{ $installment['number'] }}</td>
                    <td>{{ $installment['date'] }}</td>
                    <td>
                        @if ($installment['amount'])
                            LKR {{ number_format($installment['amount'], 2) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>

        <h3 class="total">
            Balance: LKR
            {{ number_format(max($order->price + $order->payments->sum('overdue_amount') - $order->payments->sum('amount'), 0), 2) }}
        </h3>

        <div class="note">
            <p>Note:</p>
            <p>- If you are overdue on an expected installment date, a charge of LKR 200 per day will apply.</p>
            <p>- If you are overdue on your last installment payment, a charge of LKR 500 per day will apply.</p>
        </div>
    </div>
</body>

</html>
