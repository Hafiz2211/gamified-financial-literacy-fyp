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
    // Helper to safely get numeric value
    function toNumber(val) {
        let n = parseFloat(val);
        return isNaN(n) ? 0 : n;
    }

    // ============================================================
    // 1. PIE CHART – only valid categories, percentages on large slices
    // ============================================================
    @if(!empty($categoryData) && $categoryData->count() > 0)
    (function() {
        let rawLabels = {!! json_encode($categoryData->pluck('category')->toArray()) !!};
        let rawValues = {!! json_encode($categoryData->pluck('total')->toArray()) !!};
        // Filter out zero or invalid values
        let labels = [];
        let values = [];
        for (let i = 0; i < rawLabels.length; i++) {
            let val = toNumber(rawValues[i]);
            if (val > 0) {
                labels.push(rawLabels[i]);
                values.push(val);
            }
        }
        if (labels.length === 0) {
            const categoryCanvas = document.getElementById('categoryChart');
            if (categoryCanvas) {
                categoryCanvas.style.display = 'none';
                categoryCanvas.parentElement.insertAdjacentHTML('beforeend', '<p class="text-center text-gray-500">No spending data for this period.</p>');
            }
            return;
        }
        let total = values.reduce((a, b) => a + b, 0);
        if (total === 0) total = 1;
        
        const safeLabelPlugin = {
            id: 'safeLabels',
            afterDatasetsDraw(chart) {
                const { ctx, data } = chart;
                const meta = chart.getDatasetMeta(0);
                if (!meta.data || meta.data.length === 0) return;
                const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                const radius = Math.min(chart.chartArea.width, chart.chartArea.height) / 2;
                
                ctx.save();
                ctx.font = 'bold 12px "Segoe UI", system-ui';
                ctx.fillStyle = '#FFFFFF';
                ctx.shadowBlur = 0;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                
                data.labels.forEach((label, i) => {
                    const value = data.datasets[0].data[i];
                    if (typeof value !== 'number' || !isFinite(value) || value === 0) return;
                    const percentage = ((value / total) * 100).toFixed(1);
                    if (percentage === 'NaN' || !isFinite(parseFloat(percentage))) return;
                    
                    const arc = meta.data[i];
                    if (!arc || typeof arc.startAngle !== 'number' || typeof arc.endAngle !== 'number') return;
                    const sliceAngle = (arc.endAngle - arc.startAngle) * 180 / Math.PI;
                    if (sliceAngle <= 30 || !isFinite(sliceAngle)) return;
                    
                    const angle = arc.startAngle + (arc.endAngle - arc.startAngle) / 2;
                    if (!isFinite(angle)) return;
                    
                    const x = centerX + Math.cos(angle) * radius * 0.7;
                    const y = centerY + Math.sin(angle) * radius * 0.7;
                    
                    ctx.fillText(percentage + '%', x, y - 8);
                    ctx.font = '10px "Segoe UI"';
                    ctx.fillText(label, x, y + 8);
                    ctx.font = 'bold 12px "Segoe UI"';
                });
                ctx.restore();
            }
        };
        
        new Chart(document.getElementById('categoryChart'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#2F5D46', '#D8A24A', '#8B9A7A', '#B6A58E', '#5F7B64']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: B$${value.toFixed(2)} (${percentage}%)`;
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            generateLabels: function(chart) {
                                const original = Chart.defaults.plugins.legend.labels.generateLabels(chart);
                                // Only include labels that exist in the filtered dataset
                                return original.filter(label => label.text !== undefined && label.text !== 'undefined');
                            }
                        }
                    }
                }
            },
            plugins: [safeLabelPlugin]
        });
    })();
    @else
    const categoryCanvas = document.getElementById('categoryChart');
    if (categoryCanvas) {
        categoryCanvas.style.display = 'none';
        categoryCanvas.parentElement.insertAdjacentHTML('beforeend', '<p class="text-center text-gray-500">No spending data for this period.</p>');
    }
    @endif

    // ============================================================
    // 2. DAILY TREND CHART (unchanged – shows only days 10,20,30)
    // ============================================================
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
                    title: { display: true, text: 'Amount (B$)', color: '#2F5D46' },
                    grid: { color: 'rgba(47,93,70,0.1)' }
                },
                x: {
                    title: { display: true, text: 'Day of Month', color: '#2F5D46' },
                    grid: { display: false },
                    ticks: {
                        autoSkip: true,
                        maxRotation: 0,
                        callback: function(val) {
                            const day = Number(val);
                            return (day === 10 || day === 20 || day === 30) ? day : '';
                        }
                    }
                }
            },
            plugins: {
                legend: { display: false },
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

    // ============================================================
    // 3. MONTHLY COMPARISON – SHOW SELECTED MONTH + NEXT TWO MONTHS
    // ============================================================
    const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    
    // Parse all monthly data from PHP
    let allMonthlyEntries = [];
    try {
        let dataObj = {!! json_encode($monthlyData) !!};
        if (typeof dataObj === 'object' && dataObj !== null) {
            for (let key in dataObj) {
                if (dataObj.hasOwnProperty(key)) {
                    let year = null;
                    let monthName = null;
                    // Check for "YYYY-MM" format
                    let match = key.match(/^(\d{4})-(\d{2})$/);
                    if (match) {
                        year = parseInt(match[1]);
                        let monthIdx = parseInt(match[2]) - 1;
                        monthName = monthNames[monthIdx];
                    } else {
                        // Look for a 4-digit year
                        let yearMatch = key.match(/\d{4}/);
                        if (yearMatch) year = parseInt(yearMatch[0]);
                        // Look for a month name
                        for (let i = 0; i < monthNames.length; i++) {
                            if (key.includes(monthNames[i])) {
                                monthName = monthNames[i];
                                break;
                            }
                        }
                        if (!monthName) {
                            let d = new Date(key);
                            if (!isNaN(d.getTime())) {
                                monthName = monthNames[d.getMonth()];
                                if (!year) year = d.getFullYear();
                            }
                        }
                    }
                    if (!monthName) monthName = "January";
                    if (!year) year = 2026;
                    let monthIndex = monthNames.indexOf(monthName);
                    let label = monthName + " " + year;
                    allMonthlyEntries.push({
                        label: label,
                        year: year,
                        monthIndex: monthIndex,
                        income: toNumber(dataObj[key].income),
                        expense: toNumber(dataObj[key].expense)
                    });
                }
            }
        }
    } catch(e) { console.warn(e); }
    
    // Sort entries chronologically
    allMonthlyEntries.sort((a,b) => {
        if (a.year !== b.year) return a.year - b.year;
        return a.monthIndex - b.monthIndex;
    });
    
    // Remove leading months with zero income & expense (optional)
    while (allMonthlyEntries.length > 0 && allMonthlyEntries[0].income === 0 && allMonthlyEntries[0].expense === 0) {
        allMonthlyEntries.shift();
    }
    
    const comparisonCanvas = document.getElementById('comparisonChart');
    if (allMonthlyEntries.length === 0) {
        if (comparisonCanvas) {
            comparisonCanvas.style.display = 'none';
            comparisonCanvas.parentElement.insertAdjacentHTML('beforeend', '<p class="text-center text-gray-500">No monthly data available.</p>');
        }
    } else {
        let currentChart = null;
        
        function updateBarChart(selectedValue) {
            if (!comparisonCanvas) return;
            // Determine selected month/year
            let targetYear, targetMonthIndex;
            if (selectedValue === 'current') {
                const now = new Date();
                targetYear = now.getFullYear();
                targetMonthIndex = now.getMonth();
            } else {
                let [year, month] = selectedValue.split('-');
                targetYear = parseInt(year);
                targetMonthIndex = parseInt(month) - 1;
            }
            
            // Find the closest month in our data (or exactly that month)
            let selectedIndex = -1;
            for (let i = 0; i < allMonthlyEntries.length; i++) {
                if (allMonthlyEntries[i].year === targetYear && allMonthlyEntries[i].monthIndex === targetMonthIndex) {
                    selectedIndex = i;
                    break;
                }
            }
            // If not found, find the closest by date difference
            if (selectedIndex === -1) {
                let bestDiff = Infinity;
                for (let i = 0; i < allMonthlyEntries.length; i++) {
                    let diff = (allMonthlyEntries[i].year - targetYear) * 12 + (allMonthlyEntries[i].monthIndex - targetMonthIndex);
                    if (Math.abs(diff) < bestDiff) {
                        bestDiff = Math.abs(diff);
                        selectedIndex = i;
                    }
                }
            }
            if (selectedIndex === -1) return;
            
            // Build the three months: selected month, next month, month after next
            // We need to generate months even if they don't exist in data (using calendar logic)
            let startYear = allMonthlyEntries[selectedIndex].year;
            let startMonthIndex = allMonthlyEntries[selectedIndex].monthIndex;
            let monthsToShow = [];
            let incomes = [];
            let expenses = [];
            
            for (let i = 0; i < 3; i++) {
                let year = startYear;
                let monthIdx = startMonthIndex + i;
                if (monthIdx >= 12) {
                    monthIdx -= 12;
                    year++;
                }
                let monthName = monthNames[monthIdx];
                let label = monthName + " " + year;
                // Find if this month exists in allMonthlyEntries
                let found = allMonthlyEntries.find(e => e.year === year && e.monthIndex === monthIdx);
                monthsToShow.push(label);
                incomes.push(found ? found.income : 0);
                expenses.push(found ? found.expense : 0);
            }
            
            // Bar label plugin
            const barLabelPlugin = {
                id: 'barLabels',
                afterDatasetsDraw(chart) {
                    const { ctx } = chart;
                    ctx.save();
                    ctx.font = 'bold 12px "Segoe UI", system-ui';
                    ctx.fillStyle = '#2F5D46';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';
                    chart.data.datasets.forEach((dataset, di) => {
                        const meta = chart.getDatasetMeta(di);
                        meta.data.forEach((bar, idx) => {
                            let val = dataset.data[idx];
                            if (val > 0) {
                                ctx.fillText('B$' + Math.round(val), bar.x, bar.y - 5);
                            }
                        });
                    });
                    ctx.restore();
                }
            };
            
            if (currentChart) currentChart.destroy();
            currentChart = new Chart(comparisonCanvas, {
                type: 'bar',
                data: {
                    labels: monthsToShow,
                    datasets: [
                        { label: 'Income', data: incomes, backgroundColor: '#2F5D46', borderRadius: 6 },
                        { label: 'Expense', data: expenses, backgroundColor: '#D8A24A', borderRadius: 6 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(47,93,70,0.1)' } },
                        x: { grid: { display: false } }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return `${ctx.dataset.label}: B$${ctx.raw.toFixed(2)}`;
                                }
                            }
                        }
                    }
                },
                plugins: [barLabelPlugin]
            });
        }
        
        // Get the current month selector value
        const monthSelector = document.getElementById('monthSelector');
        if (monthSelector) {
            updateBarChart(monthSelector.value);
        }
    }

    // ============================================================
    // 4. Month selector (original behaviour – reloads page)
    // ============================================================
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