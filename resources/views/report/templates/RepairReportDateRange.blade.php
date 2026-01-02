<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Repair Report - {{ $startDate }} to {{ $endDate }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h2 { font-size: 20px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: center; }
        th { background-color: #f1f5f9; }
        tbody tr:nth-child(even) { background-color: #f8fafc; }
    </style>
</head>

<body>
<h2>📋 Phone Repairs Report – {{ $startDate }} to {{ $endDate }}</h2>

<div class="summary">
    <p><strong>Total Repairs:</strong> {{ $totalRepairs }}</p>
    <p><strong>Total Repair Cost:</strong> Rs. {{ number_format($totalRepairCost, 2) }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>EMI</th>
            <th>Phone Type</th>
            <th>Colour</th>
            <th>Capacity</th>
            <th>Repair Reason</th>
            <th>Repair Cost</th>
            <th>Repair Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($repairs as $repair)
        <tr>
            <td>{{ $repair->emi }}</td>
            <td>{{ $repair->inventory->phone_type ?? '-' }}</td>
            <td>{{ $repair->inventory->colour ?? '-' }}</td>
            <td>{{ $repair->inventory->capacity ?? '-' }}</td>
            <td>{{ $repair->repair_reason }}</td>
            <td>Rs. {{ number_format($repair->repair_cost, 2) }}</td>
            <td>{{ $repair->created_at->format('Y-m-d') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7">No repairs found in this date range.</td>
        </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
