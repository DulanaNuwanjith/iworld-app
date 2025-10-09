<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daily Finance Report - {{ $date }}</title>
    <style>
    body {
        font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #333;
        margin: 0;
        padding: 20px;
        background-color: #f9f9f9;
    }

    h2 {
        font-size: 20px;
        color: #1a202c;
        margin-bottom: 10px;
        border-bottom: 2px solid #1a202c;
        padding-bottom: 5px;
    }

    h3 {
        font-size: 16px;
        color: #1a202c;
        margin-top: 20px;
        margin-bottom: 5px;
    }

    .summary {
        background-color: #e2e8f0;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .summary p {
        margin: 3px 0;
        font-weight: 500;
    }

    .summary p strong {
        font-family: 'DejaVu Sans Mono', monospace; /* Numbers in mono */
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    th, td {
        border: 1px solid #cbd5e1;
        padding: 8px 10px;
        text-align: center;
        font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
    }

    /* Use monospace font for number cells */
    td:nth-child(n+4) { 
        font-family: 'DejaVu Sans Mono', monospace;
        font-weight: 500;
    }

    th {
        background-color: #f1f5f9;
        font-weight: 600;
    }

    tbody tr:nth-child(even) {
        background-color: #f8fafc;
    }

    tbody tr:hover {
        background-color: #e2e8f0;
    }

    td strong {
        color: #1a202c;
    }
</style>

</head>

<body>
    <h2>📅 Daily Finance Report – {{ $date }}</h2>

    <div class="summary">
        <p><strong>New Orders:</strong> {{ $totalOrders }}</p>
        <p><strong>Payments Made:</strong> {{ $totalPayments }}</p>
        <p><strong>Total Investment:</strong> Rs. {{ number_format($totalInvestment, 2) }}</p>
        <p><strong>Total Profit:</strong> Rs. {{ number_format($totalProfit, 2) }} (Selected date created new orders)</p>
        <p><strong>Total Paid:</strong> Rs. {{ number_format($totalPaidAmount, 2) }}</p>
        <p><strong>Overdue Collected:</strong> Rs. {{ number_format($totalOverdueCollected, 2) }}</p>
        <p><strong>Total Income:</strong> Rs. {{ number_format($totalIncome, 2) }}</p>
        <p><strong>Remaining Balance:</strong> Rs. {{ number_format($remainingBalance, 2) }} (All Installments Remaining)</p>
    </div>

    <h3>🆕 New Orders on {{ $date }}</h3>
    <table>
        <thead>
            <tr>
                <th>Order No</th>
                <th>Buyer</th>
                <th>Item Name</th>
                <th>Price</th>
                <th>Due Payment</th>
                <th>Remaining Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($newOrders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->buyer_name }}</td>
                    <td>{{ $order->item_name }}</td>
                    <td>Rs. {{ number_format($order->price, 2) }}</td>
                    <td>Rs. {{ number_format($order->due_payment, 2) }}</td>
                    <td>Rs. {{ number_format($order->remaining_amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No new orders today.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>💰 Payments Made on {{ $date }}</h3>
    <table>
        <thead>
            <tr>
                <th>Order No</th>
                <th>Buyer</th>
                <th>Installment #</th>
                <th>Paid Amount</th>
                <th>Overdue</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr>
                    <td>{{ $payment->financeOrder->order_number }}</td>
                    <td>{{ $payment->financeOrder->buyer_name }}</td>
                    <td>{{ $payment->installment_number }}</td>
                    <td>Rs. {{ number_format($payment->paid_amount, 2) }}</td>
                    <td>
                        Rs. {{ number_format($payment->overdue_amount, 2) }}
                        @if ($payment->overdue_days > 0)
                            ({{ $payment->overdue_days }} days)
                        @endif
                    </td>
                    <td>Rs. {{ number_format($payment->paid_amount + $payment->overdue_amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No payments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
