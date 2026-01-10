@php use Carbon\Carbon; @endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Salary Report - {{ \Carbon\Carbon::parse($monthYear)->format('F Y') }}</title>
    <style>
        /* A4 Page Setup */
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f3f4f6;
            color: #000;
        }

        .report-box {
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            padding: 15mm;
            background: #fff;
            border: 1px solid #d1d5db;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
        }

        .header .company-logo img {
            height: 60px;
        }

        .header .company-info {
            text-align: right;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin: 20px 0;
            font-size: 20px;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #444;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tfoot td {
            font-weight: bold;
            background-color: #e0e0e0;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            position: absolute;
            bottom: 15mm;
            width: 100%;
        }

        @media print {
            body {
                background: #fff;
            }

            .report-box {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>

<body>
    <div class="report-box">
        <!-- Header -->
        <div class="header">
            <div class="company-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Company Logo">
            </div>
            <div class="company-info">
                <strong>My Company Pvt Ltd</strong><br>
                Address Line 1, City, Country<br>
                Tel: 076 411 28 49 | 077 20 87 649<br>
                Email: info@company.com
            </div>
        </div>

        <!-- Title -->
        <h2>Salary Report - {{ Carbon::parse($monthYear)->format('F Y') }}</h2>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>Worker Name</th>
                    <th>Basic Salary</th>
                    <th>Total Commission</th>
                    <th>Total Sales</th>
                    <th>Invoice Count</th>
                    <th>Month-Year</th>
                    <th>Monthly Salary</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalBasic = 0;
                    $totalCommission = 0;
                    $totalMonthly = 0;
                @endphp

                @forelse($salaries as $salary)
                    @php
                        $monthlySalary = $salary['basic_salary'] + $salary['total_commission'];
                        $totalBasic += $salary['basic_salary'];
                        $totalCommission += $salary['total_commission'];
                        $totalMonthly += $monthlySalary;
                    @endphp
                    <tr>
                        <td>{{ $salary['worker_name'] }}</td>
                        <td>{{ number_format($salary['basic_salary'], 2) }}</td>
                        <td>{{ number_format($salary['total_commission'], 2) }}</td>
                        <td>{{ number_format($salary['total_sales'], 2) }}</td>
                        <td>{{ $salary['invoice_count'] }}</td>
                        <td>{{ Carbon::createFromDate($salary['year'], $salary['month'], 1)->format('M Y') }}</td>
                        <td>{{ number_format($monthlySalary, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No salary records found for this month.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td>{{ number_format($totalBasic, 2) }}</td>
                    <td>{{ number_format($totalCommission, 2) }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>{{ number_format($totalMonthly, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div class="footer">
            My Company Pvt Ltd | Tel: 076 411 28 49 | info@company.com
        </div>
    </div>
</body>

</html>
