<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iworld Dashboard</title>
</head>
<div class="flex h-full w-full font-sans bg-white">
    @include('layouts.side-bar')

    <div class="flex h-full w-full font-sans bg-white">

        <div class="flex-1 overflow-y-auto p-6 md:p-10">
            <!-- Header -->
            <div class="flex justify-between items-start md:items-center mb-5">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">
                        Iworld Management Dashboard
                    </h1>
                    <p class="text-gray-500 text-sm">📊 Finance Overview & Monthly Statistics</p>
                </div>
                <div class="text-right">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Welcome, {{ Auth::user()->name }}!
                    </h2>
                </div>
            </div>

            <!-- Finance Dashboard Section -->
            <div class="p-6 bg-white">

                {{-- Summary Cards --}}
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">📈 Financial Overview</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-10">
                    {{-- Total Customers --}}
                    <div
                        class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-2xl shadow hover:shadow-md transition transform hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Total Customers</p>
                                <p class="text-3xl font-bold text-blue-700 mt-2">
                                    {{ number_format($totals['total_customers']) }}
                                </p>
                            </div>
                            <div class="bg-blue-200 text-blue-800 p-3 rounded-full">
                                <i data-lucide="users" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Total Investment --}}
                    <div
                        class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-5 rounded-2xl shadow hover:shadow-md transition transform hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Total Investment</p>
                                <p class="text-3xl font-bold text-yellow-700 mt-2">
                                    LKR {{ number_format($totals['total_investment'], 2) }}
                                </p>
                            </div>
                            <div class="bg-yellow-200 text-yellow-800 p-3 rounded-full">
                                <i data-lucide="briefcase" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Total Paid --}}
                    <div
                        class="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-2xl shadow hover:shadow-md transition transform hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Total Paid</p>
                                <p class="text-3xl font-bold text-green-700 mt-2">
                                    LKR {{ number_format($totals['total_paid'], 2) }}
                                </p>
                            </div>
                            <div class="bg-green-200 text-green-800 p-3 rounded-full">
                                <i data-lucide="check-circle" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Remaining Balance --}}
                    <div
                        class="bg-gradient-to-br from-red-50 to-red-100 p-5 rounded-2xl shadow hover:shadow-md transition transform hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Remaining Balance</p>
                                <p class="text-3xl font-bold text-red-700 mt-2">
                                    LKR {{ number_format($totals['total_remaining'], 2) }}
                                </p>
                            </div>
                            <div class="bg-red-200 text-red-800 p-3 rounded-full">
                                <i data-lucide="wallet" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Total Overdue --}}
                    <div
                        class="bg-gradient-to-br from-orange-50 to-orange-100 p-5 rounded-2xl shadow hover:shadow-md transition transform hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Total Overdue</p>
                                <p class="text-3xl font-bold text-orange-700 mt-2">
                                    LKR {{ number_format($totals['total_overdue'], 2) }}
                                </p>
                            </div>
                            <div class="bg-orange-200 text-orange-800 p-3 rounded-full">
                                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Include Lucide Icons --}}
                <script src="https://unpkg.com/lucide@latest"></script>
                <script>
                    lucide.createIcons();
                </script>

                {{-- Monthly Statistics Table --}}
                <h2 class="text-xl font-semibold text-gray-800 mb-3">📅 Monthly Performance</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="p-4 border rounded-lg shadow">
                        <canvas id="financeOverviewChart"></canvas>
                    </div>
                    <div class="p-4 border rounded-lg shadow">
                        <canvas id="remainingOverdueChart"></canvas>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    const labels = [...new Set(@json($monthlyStats->map(fn($s) => \Carbon\Carbon::create($s->year, $s->month)->format('M Y'))))];
                    const investment = @json($monthlyStats->pluck('total_investment'));
                    const paid = @json($monthlyStats->pluck('total_paid'));
                    const remaining = @json($monthlyStats->pluck('total_remaining'));
                    const overdue = @json($monthlyStats->pluck('total_overdue'));

                    // Chart 1: Investment vs Paid
                    new Chart(document.getElementById('financeOverviewChart'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'Total Investment (LKR)',
                                    data: investment,
                                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                },
                                {
                                    label: 'Total Paid (LKR)',
                                    data: paid,
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                title: {
                                    display: true,
                                    text: 'Monthly Investment vs Paid'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    // Chart 2: Remaining vs Overdue
                    new Chart(document.getElementById('remainingOverdueChart'), {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'Remaining (LKR)',
                                    data: remaining,
                                    borderColor: 'rgba(255, 99, 132, 0.8)',
                                    fill: false,
                                    tension: 0.3
                                },
                                {
                                    label: 'Overdue (LKR)',
                                    data: overdue,
                                    borderColor: 'rgba(255, 159, 64, 0.8)',
                                    fill: false,
                                    tension: 0.3
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                title: {
                                    display: true,
                                    text: 'Remaining vs Overdue Trends'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</div>


<script>
    // Auto-reload the dashboard every 60 seconds
    setTimeout(() => {
        window.location.reload();
    }, 30000);
</script>
