<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Spending • BruSave</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial,
                         "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji";
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }
        .app-container {
            display: flex;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }
        .sidebar {
            width: 270px;
            height: 100vh;
            flex-shrink: 0;
            background: #2F5D46;
            border-right: 1px solid rgba(47,93,70,0.22);
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            height: 100vh;
            overflow-y: auto;
            background: #F6F1E6;
        }
        .bs-input:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(216, 162, 74, 0.20);
            border-color: rgba(216, 162, 74, 0.65) !important;
        }
        .bs-btn:focus { 
            outline: none; 
            box-shadow: 0 0 0 4px rgba(216,162,74,0.22); 
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .level-up-notification {
            animation: slideIn 0.3s ease-out;
        }
    </style>
</head>

<body class="text-slate-900" style="background:#F6F1E6;">
{{-- Level Up Notification --}}
@if(session('level_up'))
    <div id="levelUpNotification" class="level-up-notification" style="position:fixed; top:20px; right:20px; z-index:9999;">
        <div style="background:#2F5D46; color:#D8A24A; padding:16px 24px; border-radius:16px; box-shadow:0 10px 25px rgba(0,0,0,0.2); border-left:4px solid #D8A24A;">
            <div style="display:flex; align-items:center; gap:12px;">
                <span style="font-size:28px;">🎉</span>
                <div>
                    <div style="font-weight:800; font-size:18px;">Level Up!</div>
                    <div style="font-size:14px; color:rgba(255,255,255,0.9);">
                        You reached Level {{ session('level_up')['new_level'] }}!
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.parentElement.remove();" style="background:none; border:none; color:rgba(255,255,255,0.7); cursor:pointer; font-size:18px; margin-left:8px;">✕</button>
            </div>
        </div>
    </div>
    @php session()->forget('level_up'); @endphp
@endif

@php
    $user = auth()->user();
    $GREEN = '#2F5D46';
    $GOLD  = '#D8A24A';
    $BG    = '#F6F1E6';
    $CARD  = '#FFFBF2';
    $active = 'track';

    $nav = [
        ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard','icon'=>'🏠'],
        ['key'=>'learn','label'=>'Learn','href'=>'/learn','icon'=>'📖'],
        ['key'=>'quiz','label'=>'Quiz','href'=>'/quiz','icon'=>'❓'],
        ['key'=>'track','label'=>'Track Spending','href'=>'/track-spending','icon'=>'🧾'],
        ['key'=>'progress','label'=>'Room','href'=>'/progress','icon'=>'🏆'],
        ['key'=>'town','label'=>'Town','href'=>'/town','icon'=>'🏘️'],
    ];

    // Calculate monthly totals from DATABASE
    $monthlyIncome = $user->transactions()
        ->where('type', 'income')
        ->whereMonth('date', now()->month)
        ->whereYear('date', now()->year)
        ->sum('amount');

    $monthlyExpense = $user->transactions()
        ->where('type', 'expense')
        ->whereMonth('date', now()->month)
        ->whereYear('date', now()->year)
        ->sum('amount');
@endphp

<div class="app-container">
    {{-- Sidebar --}}
    <div class="sidebar">
        <div class="p-5 border-b" style="border-color: rgba(255,255,255,0.12);">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/brusave-logo.png') }}" alt="BruSave logo" class="h-8 w-auto object-contain">
                <div>
                    <div class="text-xl font-extrabold leading-tight" style="color:{{ $GOLD }};">Bru<i>Save</i></div>
                    <div class="text-xs" style="color: rgba(216,162,74,0.78);">Build Wealth, Build Your Town</div>
                </div>
            </div>
        </div>

        <nav class="p-4 space-y-2 flex-1 overflow-y-auto">
            @foreach ($nav as $item)
                @php $isActive = $active === $item['key']; @endphp
                <a href="{{ $item['href'] }}"
                   class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border transition hover:opacity-95"
                   style="border-color: {{ $isActive ? 'rgba(216,162,74,0.60)' : 'rgba(255,255,255,0.16)' }};
                          background:  {{ $isActive ? 'rgba(216,162,74,0.14)' : 'rgba(255,255,255,0.04)' }};
                          color:       {{ $isActive ? $GOLD : 'rgba(255,255,255,0.92)' }};">
                    <span class="text-lg">{{ $item['icon'] }}</span>
                    <span class="font-semibold">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl border font-semibold transition hover:opacity-95"
                        style="border-color: rgba(255,255,255,0.16); color: rgba(255,255,255,0.92); background: rgba(255,255,255,0.04);">
                    <span>🚪</span> Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Main content --}}
    <div class="main-content">
        <div style="max-width:1200px; margin:0 auto; padding:32px 24px;">
            {{-- Title --}}
            <section class="mt-10">
                <h1 class="text-3xl md:text-4xl font-extrabold" style="color:{{ $GREEN }};">Track Spending</h1>
                <p class="mt-2" style="color: rgba(47,93,70,0.82);">
                    Add your daily income and expenses
                </p>
            </section>

            {{-- Stats cards from DATABASE --}}
            <section class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-3xl border shadow-lg p-5" style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xs font-semibold tracking-wide uppercase" style="color: rgba(47,93,70,0.65);">Total Income</div>
                            <div class="mt-1 text-2xl font-extrabold total-income-value" style="color:{{ $GREEN }};">B${{ number_format($monthlyIncome, 2) }}</div>
                            <div class="mt-1 text-xs" style="color: rgba(47,93,70,0.65);">This month</div>
                        </div>
                        <div class="h-11 w-11 rounded-xl flex items-center justify-center text-xl border"
                             style="background: rgba(216,162,74,0.20); border-color: rgba(216,162,74,0.40);">📈</div>
                    </div>
                </div>

                <div class="rounded-3xl border shadow-lg p-5" style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xs font-semibold tracking-wide uppercase" style="color: rgba(47,93,70,0.65);">Total Expenses</div>
                            <div class="mt-1 text-2xl font-extrabold total-expense-value" style="color:{{ $GREEN }};">B${{ number_format($monthlyExpense, 2) }}</div>
                            <div class="mt-1 text-xs" style="color: rgba(47,93,70,0.65);">This month</div>
                        </div>
                        <div class="h-11 w-11 rounded-xl flex items-center justify-center text-xl border"
                             style="background: rgba(216,162,74,0.20); border-color: rgba(216,162,74,0.40);">📉</div>
                    </div>
                </div>
            </section>

            {{-- Form card with PHOTO UPLOAD --}}
            <section class="mt-8 rounded-3xl border shadow-lg" style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                <div class="p-6 md:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-xl font-extrabold" style="color:{{ $GREEN }};">Add a record</h2>
                        <p class="text-sm" style="color: rgba(47,93,70,0.72);">Saved to your account.</p>
                    </div>

                    <form id="recordForm" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5" enctype="multipart/form-data">
                        @csrf
                        {{-- Type --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">Type</label>
                            <div class="mt-2 flex items-center gap-4">
                                <label class="inline-flex items-center gap-2 text-sm" style="color: rgba(47,93,70,0.85);">
                                    <input type="radio" name="type" value="expense" checked> Expense
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm" style="color: rgba(47,93,70,0.85);">
                                    <input type="radio" name="type" value="income"> Income
                                </label>
                            </div>
                        </div>

                        {{-- Amount --}}
                        <div>
                            <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">Amount</label>
                            <div class="mt-2 flex items-center rounded-2xl border overflow-hidden"
                                 style="border-color: rgba(47,93,70,0.18); background:#FFFFFF;">
                                <span class="px-4 py-3 text-sm font-semibold" style="color: rgba(47,93,70,0.72);">B$</span>
                                <input id="amount" name="amount" type="number" min="0" step="0.01" inputmode="decimal"
                                       class="bs-input w-full px-4 py-3" placeholder="0.00" required
                                       style="background:#FFFFFF; border:0;">
                            </div>
                        </div>

                        {{-- Date --}}
                        <div>
                            <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">Date</label>
                            <input id="date" name="date" type="date" class="bs-input mt-2 w-full rounded-2xl border px-4 py-3" required
                                   style="background:#FFFFFF; border-color: rgba(47,93,70,0.18);">
                        </div>

                        {{-- Category picker --}}
                        <div>
                            <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">Category</label>
                            <button type="button" id="openCategory"
                                    class="bs-btn mt-2 w-full rounded-2xl border px-4 py-3 text-left font-semibold transition hover:opacity-95"
                                    style="background:#FFFFFF; border-color: rgba(47,93,70,0.18); color: rgba(47,93,70,0.88);">
                                <span id="categoryLabel">Choose a category</span>
                                <span class="float-right" style="color: rgba(47,93,70,0.55);">▾</span>
                            </button>
                            <input type="hidden" id="category" name="category" required>
                        </div>

                        {{-- Other category manual --}}
                        <div id="otherWrap" class="hidden">
                            <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">Other category</label>
                            <input id="otherCategory" type="text" class="bs-input mt-2 w-full rounded-2xl border px-4 py-3"
                                   placeholder="Type your category…"
                                   style="background:#FFFFFF; border-color: rgba(47,93,70,0.18);">
                        </div>

                        {{-- Note --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">Note (optional)</label>
                            <textarea id="note" name="note" rows="3" class="bs-input mt-2 w-full rounded-2xl border px-4 py-3"
                                      placeholder="Example: Lunch with friends"
                                      style="background:#FFFFFF; border-color: rgba(47,93,70,0.18);"></textarea>
                        </div>

                        {{-- PHOTO UPLOAD --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">Picture (optional)</label>
                            <input id="photo" name="photo" type="file" accept="image/*"
                                   class="mt-2 w-full text-sm"
                                   style="color: rgba(47,93,70,0.80);">
                            <p class="mt-1 text-xs" style="color: rgba(47,93,70,0.65);">
                                Upload a photo of your receipt or item.
                            </p>
                        </div>

                        {{-- STATISTICS BUTTON --}}
                        <div class="md:col-span-2 flex justify-end">
                            <a href="{{ route('statistics') }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition hover:opacity-90"
                               style="background: rgba(47,93,70,0.10); color:{{ $GREEN }};">
                                <span>📊</span> View Spending Statistics
                            </a>
                        </div>

                        {{-- SUBMIT BUTTON --}}
                        <div class="md:col-span-2 flex flex-wrap items-center gap-3 pt-1">
                            <button type="submit" class="bs-btn px-6 py-3 rounded-2xl font-semibold transition hover:opacity-90"
                                    style="background:{{ $GREEN }}; color:{{ $GOLD }};">
                                Add Record
                            </button>
                            <span class="text-sm" style="color: rgba(47,93,70,0.72);">Earn XP and coins!</span>
                        </div>
                    </form>
                </div>
            </section>

            {{-- History from DATABASE with PHOTO column --}}
            <section class="mt-8 rounded-3xl border shadow-lg overflow-hidden"
                     style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                <div class="p-6 md:p-7">
                    <h2 class="text-xl font-extrabold" style="color:{{ $GREEN }};">History</h2>

                    <div class="mt-5 overflow-x-auto rounded-2xl border"
                         style="border-color: rgba(47,93,70,0.14); background:#FFFFFF;">
                        <table class="min-w-full text-sm">
                            <thead>
                            <tr style="background: rgba(47,93,70,0.06);">
                                <th class="text-left px-4 py-3 font-extrabold" style="color: rgba(47,93,70,0.85);">Date</th>
                                <th class="text-left px-4 py-3 font-extrabold" style="color: rgba(47,93,70,0.85);">Type</th>
                                <th class="text-left px-4 py-3 font-extrabold" style="color: rgba(47,93,70,0.85);">Category</th>
                                <th class="text-right px-4 py-3 font-extrabold" style="color: rgba(47,93,70,0.85);">Amount</th>
                                <th class="text-left px-4 py-3 font-extrabold" style="color: rgba(47,93,70,0.85);">Note</th>
                                <th class="text-left px-4 py-3 font-extrabold" style="color: rgba(47,93,70,0.85);">Photo</th>
                                <th class="text-right px-4 py-3 font-extrabold" style="color: rgba(47,93,70,0.85);">XP/Coins</th>
                                <th class="text-right px-4 py-3 font-extrabold" style="color: rgba(47,93,70,0.85);">Action</th>
                            </tr>
                            </thead>
                            <tbody id="historyBody" class="divide-y" style="divide-color: rgba(47,93,70,0.10);">
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-sm"
                                        style="color: rgba(47,93,70,0.70);">
                                        Loading transactions...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <footer class="text-center text-xs pb-8" style="color: rgba(47,93,70,0.75);">
            © {{ date('Y') }} Bru<i>Save</i>
        </footer>
    </div>
</div>

{{-- Category Modal --}}
<div id="categoryModal" class="fixed inset-0 hidden items-center justify-center p-4" style="z-index: 1000; background: rgba(10,15,12,0.45;">
    <div class="relative w-full max-w-md rounded-3xl border shadow-xl"
         style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
        <div class="p-5 border-b" style="border-color: rgba(47,93,70,0.12);">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-extrabold" style="color:{{ $GREEN }};">Pick a category</div>
                    <div class="text-xs" style="color: rgba(47,93,70,0.70);">Categories change based on Expense / Income.</div>
                </div>
                <button id="closeCategory"
                        class="bs-btn px-3 py-2 rounded-xl border font-semibold"
                        style="border-color: rgba(47,93,70,0.18); color: rgba(47,93,70,0.80); background:#FFFFFF;">
                    ✕
                </button>
            </div>
        </div>
        <div class="p-5">
            <div id="categoryList" class="grid grid-cols-2 gap-3"></div>
            <button id="categoryOther"
                    class="bs-btn mt-4 w-full px-4 py-3 rounded-2xl font-semibold border transition hover:opacity-95"
                    style="background:#FFFFFF; border-color: rgba(216,162,74,0.55); color:{{ $GREEN }};">
                Other (type manually)
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // API endpoints
    const API = {
        transactions: '/transactions',
        stats: '/transactions/stats'
    };

    // Categories
    const EXPENSE_CATS = ['Health','Education','Transport','Shopping','Bills'];
    const INCOME_CATS  = ['Salary','Allowance','Bonus'];

    // Elements
    const form = document.getElementById('recordForm');
    const amountEl = document.getElementById('amount');
    const dateEl = document.getElementById('date');
    const noteEl = document.getElementById('note');
    const photoEl = document.getElementById('photo');
    const categoryHidden = document.getElementById('category');
    const categoryLabel  = document.getElementById('categoryLabel');
    const otherWrap = document.getElementById('otherWrap');
    const otherEl   = document.getElementById('otherCategory');
    const historyBody = document.getElementById('historyBody');
    const openCategoryBtn = document.getElementById('openCategory');
    const modal = document.getElementById('categoryModal');
    const closeCategoryBtn = document.getElementById('closeCategory');
    const categoryList = document.getElementById('categoryList');
    const categoryOtherBtn = document.getElementById('categoryOther');
    const incomeValue = document.querySelector('.total-income-value');
    const expenseValue = document.querySelector('.total-expense-value');

    // Set today's date
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    dateEl.value = `${year}-${month}-${day}`;

    const getType = () => {
        const checked = document.querySelector('input[name="type"]:checked');
        return checked ? checked.value : 'expense';
    };

    // Format money
    const formatMoney = (n) => 'B$' + Number(n || 0).toFixed(2);

    // Update stats cards
    const updateStats = async () => {
        try {
            const response = await fetch(API.stats);
            const stats = await response.json();
            
            if (incomeValue) {
                incomeValue.textContent = 'B$' + Number(stats.monthlyIncome || 0).toFixed(2);
            }
            if (expenseValue) {
                expenseValue.textContent = 'B$' + Number(stats.monthlyExpense || 0).toFixed(2);
            }
        } catch (error) {
            console.error('Error updating stats:', error);
        }
    };

    // Fetch and display transactions
    const loadTransactions = async () => {
        try {
            const response = await fetch(API.transactions);
            const transactions = await response.json();

            if (!transactions || transactions.length === 0) {
                historyBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-sm"
                            style="color: rgba(47,93,70,0.70);">
                            No records yet — add your first expense or income above.
                        </td>
                    </tr>
                `;
                return;
            }

            let html = '';
            transactions.forEach(t => {
                const typePillBg = t.type === 'income' ? 'rgba(47,93,70,0.10)' : 'rgba(216,162,74,0.18)';
                const amountColor = t.type === 'income' ? '#2F5D46' : 'rgba(180,60,60,0.95)';
                const rewardText = `${t.xp_earned} XP / ${t.coins_earned} 🪙`;
                
                // Photo display
                const photoDisplay = t.photo_url 
                    ? `<img src="${t.photo_url}" class="h-9 w-9 rounded-xl object-cover border cursor-pointer" 
                            style="border-color: rgba(47,93,70,0.18);" 
                            onclick="window.open('${t.photo_url}', '_blank')"
                            alt="receipt">` 
                    : '<span style="color: rgba(47,93,70,0.45);">—</span>';

                html += `
                    <tr>
                        <td class="px-4 py-3" style="color: rgba(47,93,70,0.85);">${t.date}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                                  style="background:${typePillBg}; color:#2F5D46; border: 1px solid rgba(47,93,70,0.12);">
                                ${t.type === 'income' ? 'Income' : 'Expense'}
                            </span>
                        </td>
                        <td class="px-4 py-3" style="color: rgba(47,93,70,0.85);">${t.category}</td>
                        <td class="px-4 py-3 text-right font-extrabold" style="color:${amountColor};">
                            ${t.type === 'income' ? '+' : '-'}${formatMoney(t.amount)}
                        </td>
                        <td class="px-4 py-3" style="color: rgba(47,93,70,0.78);">${t.note || '—'}</td>
                        <td class="px-4 py-3">${photoDisplay}</td>
                        <td class="px-4 py-3 text-right" style="color: #2F5D46;">
                            ${rewardText}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button onclick="deleteTransaction(${t.id})"
                                    class="bs-btn text-xs font-semibold underline underline-offset-4 transition hover:opacity-90"
                                    style="color: rgba(180,60,60,0.95);">
                                Delete
                            </button>
                        </td>
                    </tr>
                `;
            });
            historyBody.innerHTML = html;
        } catch (error) {
            console.error('Error loading transactions:', error);
            historyBody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-sm" style="color: rgba(180,60,60,0.95);">
                        Error loading transactions. Please refresh.
                    </td>
                </tr>
            `;
        }
    };

    // Delete transaction
    window.deleteTransaction = async (id) => {
        if (!confirm('Delete this record?')) return;

        try {
            const response = await fetch(`${API.transactions}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                await loadTransactions();
                await updateStats();
            } else {
                alert('Failed to delete transaction');
            }
        } catch (error) {
            console.error('Error deleting transaction:', error);
            alert('Error deleting transaction');
        }
    };

    // Form submit with PHOTO UPLOAD
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const type = getType();
        const amount = Number(amountEl.value || 0);
        const date = dateEl.value;

        let category = categoryHidden.value;
        if (!category) {
            alert('Please choose a category.');
            return;
        }

        if (category === 'Other') {
            const manual = (otherEl.value || '').trim();
            if (!manual) {
                alert('Please type your other category.');
                return;
            }
            category = manual;
        }

        if (!date || !(amount > 0)) {
            alert('Please enter a valid date and amount.');
            return;
        }

        // Create FormData to handle file upload
        const formData = new FormData();
        formData.append('type', type);
        formData.append('amount', amount);
        formData.append('category', category);
        formData.append('date', date);
        formData.append('note', noteEl.value || '');
        
        // Add photo if selected
        if (photoEl.files && photoEl.files[0]) {
            formData.append('photo', photoEl.files[0]);
        }

        try {
            const response = await fetch(API.transactions, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                // Reset form
                amountEl.value = '';
                noteEl.value = '';
                photoEl.value = '';
                categoryHidden.value = '';
                categoryLabel.textContent = 'Choose a category';
                otherWrap.classList.add('hidden');
                otherEl.value = '';

                // Refresh transactions list and update stats
                await loadTransactions();
                await updateStats();

                // Show success message with rewards
                alert(result.message || '✅ Record added!');
            } else {
                alert('Error: ' + (result.message || 'Failed to add record'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error adding record');
        }
    });

    // Category modal functions
    const openCategoryModal = () => {
        const type = getType();
        const list = type === 'income' ? INCOME_CATS : EXPENSE_CATS;

        let html = '';
        list.forEach(cat => {
            html += `
                <button type="button"
                        class="category-option w-full px-4 py-3 rounded-2xl font-semibold border transition hover:opacity-95"
                        style="background:#FFFFFF; border-color: rgba(47,93,70,0.18); color:#2F5D46; cursor:pointer;">
                    ${cat}
                </button>
            `;
        });
        categoryList.innerHTML = html;

        // Add event listeners to category buttons
        document.querySelectorAll('.category-option').forEach(btn => {
            btn.addEventListener('click', function() {
                const cat = this.textContent;
                categoryHidden.value = cat;
                categoryLabel.textContent = cat;
                otherWrap.classList.add('hidden');
                otherEl.value = '';
                modal.style.display = 'none';
            });
        });

        // Show modal
        modal.style.display = 'flex';
    };

    const closeCategoryModal = () => {
        modal.style.display = 'none';
    };

    openCategoryBtn.addEventListener('click', openCategoryModal);
    closeCategoryBtn.addEventListener('click', closeCategoryModal);
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeCategoryModal();
        }
    });

    categoryOtherBtn.addEventListener('click', () => {
        categoryHidden.value = 'Other';
        categoryLabel.textContent = 'Other';
        otherWrap.classList.remove('hidden');
        modal.style.display = 'none';
        otherEl.focus();
    });

    document.querySelectorAll('input[name="type"]').forEach(r => {
        r.addEventListener('change', () => {
            categoryHidden.value = '';
            categoryLabel.textContent = 'Choose a category';
            otherWrap.classList.add('hidden');
            otherEl.value = '';
        });
    });

    // Initial load
    loadTransactions();
    updateStats();

    // Refresh stats every 30 seconds
    setInterval(updateStats, 30000);
});
</script>
</body>
</html>