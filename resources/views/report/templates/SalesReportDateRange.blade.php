<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sales Report - {{ $startDate }} to {{ $endDate }}</title>
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
            font-family: 'DejaVu Sans Mono', monospace;
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

        td:nth-child(n+5) { 
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

<h2>📈 Sales Report – {{ $startDate }} to {{ $endDate }}</h2>

<div class="summary">
    <p><strong>Total Invoices:</strong> {{ $totalInvoices }}</p>
    <p><strong>Total Selling:</strong> Rs. {{ number_format($totalSelling, 2) }}</p>
    <p><strong>Total Investment (Cost):</strong> Rs. {{ number_format($totalCost, 2) }}</p>
    <p><strong>Total Profit:</strong> Rs. {{ number_format($totalProfit, 2) }} (Selling – Cost)</p>
</div>

<h3>🧾 Invoice Details</h3>
<table>
    <thead>
        <tr>
            <th>Invoice #</th>
            <th>Customer Name</th>
            <th>Phone EMI</th>
            <th>Phone Type</th>
            <th>Selling Price</th>
            <th>Cost</th>
            <th>Profit</th>
            <th>Invoice Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($invoices as $invoice)
        @php
            $cost = $invoice->inventory->cost ?? 0;
            $profit = ($invoice->selling_price ?? 0) - $cost;
        @endphp
        <tr>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->customer_name }}</td>
            <td>{{ $invoice->emi }}</td>
            <td>{{ $invoice->phone_type }}</td>
            <td>Rs. {{ number_format($invoice->selling_price ?? 0, 2) }}</td>
            <td>Rs. {{ number_format($cost, 2) }}</td>
            <td>Rs. {{ number_format($profit, 2) }}</td>
            <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8">No invoices found in this date range.</td>
        </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
