<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Statistics • BruSave</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial,
                         "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji";
            margin: 0;
            padding: 0;
            background: #F6F1E6;
        }
    </style>
</head>

<body class="min-h-screen text-slate-900" style="background:#F6F1E6;">
@php
    $GREEN = '#2F5D46';
    $GOLD = '#D8A24A';
    $CARD = '#FFFBF2';
    
    // Prepare daily data - now using day numbers (1-31)
    $dailyData = $dailyData ?? [];
    $dailyLabels = !empty($dailyData) ? array_keys($dailyData) : [1, 2, 3, 4, 5, 6, 7];
    $dailyValues = !empty($dailyData) ? array_values($dailyData) : [0, 0, 0, 0, 0, 0, 0];
    
    // Default values for summary cards
    $totalIncome = $totalIncome ?? 0;
    $totalExpense = $totalExpense ?? 0;
    $topCategory = $topCategory ?? 'Nothing yet';
    $categoryData = $categoryData ?? collect([]);
    $monthlyData = $monthlyData ?? [];
    $availableMonths = $availableMonths ?? [];
    $selectedYear = $selectedYear ?? null;
    $selectedMonth = $selectedMonth ?? null;
@endphp

<div class="min-h-screen">
    <header class="border-b" style="background:{{ $GREEN }};">
        <div class="mx-auto max-w-6xl px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/brusave-logo.png') }}" alt="BruSave" class="h-10">
                <div>
                    <div class="text-2xl font-extrabold" style="color:{{ $GOLD }};">Bru<i>Save</i></div>
                    <div class="text-sm" style="color:{{ $GOLD }}; opacity:0.75;">Build Wealth, Build Your Town</div>
                </div>
            </div>
            <nav class="flex items-center gap-3">
                <a href="/track-spending" class="px-5 py-3 rounded-xl font-semibold border text-white"
                   style="border-color: rgba(255,255,255,0.25);">← Back</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 pb-16">
        <section class="mt-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold" style="color:{{ $GREEN }};">Spending Insights</h1>
                    <p class="mt-2" style="color: rgba(47,93,70,0.82);">Visual breakdown of your finances</p>
                </div>
                
                {{-- Month Selector --}}
                @if(!empty($availableMonths))
                <div class="flex items-center gap-2">
                    <label class="text-sm" style="color: rgba(47,93,70,0.65);">View:</label>
                    <select id="monthSelector" class="rounded-xl border px-4 py-2 text-sm"
                            style="border-color: rgba(47,93,70,0.18); background:#FFFFFF;">
                        @foreach($availableMonths as $month)
                            <option value="{{ $month['value'] }}" 
                                {{ isset($selectedYear) && isset($selectedMonth) && $month['value'] == $selectedYear . '-' . $selectedMonth ? 'selected' : '' }}>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                        <option value="current" {{ !isset($selectedMonth) ? 'selected' : '' }}>Current Month</option>
                    </select>
                </div>
                @endif
            </div>
        </section>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Category Chart --}}
            <div class="rounded-3xl border shadow-lg p-6" style="background:{{ $CARD }};">
                <h2 class="text-xl font-extrabold mb-4" style="color:{{ $GREEN }};">Spending by Category</h2>
                <canvas id="categoryChart" height="250"></canvas>
            </div>

            {{-- Daily Trend --}}
            <div class="rounded-3xl border shadow-lg p-6" style="background:{{ $CARD }};">
                <h2 class="text-xl font-extrabold mb-4" style="color:{{ $GREEN }};">Daily Spending Trend</h2>
                <canvas id="trendChart" height="250"></canvas>
            </div>

            {{-- Monthly Comparison --}}
            <div class="rounded-3xl border shadow-lg p-6 md:col-span-2" style="background:{{ $CARD }};">
                <h2 class="text-xl font-extrabold mb-4" style="color:{{ $GREEN }};">Monthly Comparison</h2>
                <canvas id="comparisonChart" height="200"></canvas>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Money Left --}}
            <div class="rounded-2xl border p-5" style="background:{{ $CARD }};">
                <div class="text-sm" style="color: rgba(47,93,70,0.65);">💰 Money Left This Month</div>
                <div class="text-2xl font-bold" style="color:{{ $GREEN }};">
                    B${{ number_format($totalIncome - $totalExpense, 2) }}
                </div>
                <div class="text-xs mt-1" style="color: rgba(47,93,70,0.65);">
                    What you have left to spend
                </div>
            </div>

            {{-- Most Spent On --}}
            <div class="rounded-2xl border p-5" style="background:{{ $CARD }};">
                <div class="text-sm" style="color: rgba(47,93,70,0.65);">📊 Most Spent On</div>
                <div class="text-2xl font-bold" style="color:{{ $GREEN }};">
                    {{ $topCategory }}
                </div>
                <div class="text-xs mt-1" style="color: rgba(47,93,70,0.65);">
                    Your biggest expense category
                </div>
            </div>

            {{-- Total Spent --}}
            <div class="rounded-2xl border p-5" style="background:{{ $CARD }};">
                <div class="text-sm" style="color: rgba(47,93,70,0.65);">📉 Total Spent This Month</div>
                <div class="text-2xl font-bold" style="color:{{ $GREEN }};">
                    B${{ number_format($totalExpense, 2) }}
                </div>
                <div class="text-xs mt-1" style="color: rgba(47,93,70,0.65);">
                    Out of B${{ number_format($totalIncome, 2) }} earned
                </div>
            </div>
        </div>
        <footer class="text-center text-xs pt-8 pb-2" style="color: rgba(47,93,70,0.75); margin-top: 40px;">
            © {{ date('Y') }} Bru<i>Save</i>
        </footer>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category Chart
    @if(!empty($categoryData) && $categoryData->count() > 0)
    new Chart(document.getElementById('categoryChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($categoryData->pluck('category')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($categoryData->pluck('total')->toArray()) !!},
                backgroundColor: ['#2F5D46', '#D8A24A', '#8B9A7A', '#B6A58E', '#5F7B64']
            }]
        }
    });
    @endif

    // Daily Trend Chart - X-AXIS shows ONLY 10, 20, 30
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyLabels) !!},
            datasets: [{
                label: 'Spending (B$)',
                data: {!! json_encode($dailyValues) !!},
                borderColor: '#2F5D46',
                backgroundColor: 'rgba(47,93,70,0.05)',
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#D8A24A',
                pointBorderColor: '#2F5D46',
                pointRadius: 3,
                pointHoverRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Amount (B$)',
                        color: '#2F5D46'
                    },
                    grid: {
                        color: 'rgba(47,93,70,0.1)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Day of Month',
                        color: '#2F5D46'
                    },
                    grid: {
                        display: false
                    },
                    ticks: {
                        autoSkip: true,
                        maxRotation: 0,
                        callback: function(val, index) {
                            // 🔴 FIXED: Only show 10, 20, 30
                            const day = Number(val);
                            if (day === 10 || day === 20 || day === 30) {
                                return day;
                            }
                            return '';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Day ' + context.label + ': B$' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            }
        }
    });

    // Monthly Comparison Chart
    @if(!empty($monthlyData))
    new Chart(document.getElementById('comparisonChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($monthlyData)) !!},
            datasets: [
                {
                    label: 'Income',
                    data: {!! json_encode(array_column($monthlyData, 'income') ?: [0, 0, 0]) !!},
                    backgroundColor: '#2F5D46'
                },
                {
                    label: 'Expense',
                    data: {!! json_encode(array_column($monthlyData, 'expense') ?: [0, 0, 0]) !!},
                    backgroundColor: '#D8A24A'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(47,93,70,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    @endif

    // Month selector
    const monthSelector = document.getElementById('monthSelector');
    if (monthSelector) {
        monthSelector.addEventListener('change', function() {
            if (this.value === 'current') {
                window.location.href = '/statistics';
            } else {
                const [year, month] = this.value.split('-');
                window.location.href = '/statistics?month=' + month + '&year=' + year;
            }
        });
    }
});
</script>
</body>
</html>