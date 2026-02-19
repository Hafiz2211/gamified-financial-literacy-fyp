{{-- resources/views/track-spending.blade.php --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Spending • BruSave</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial,
                         "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji";
        }
        .bs-input:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(216, 162, 74, 0.20);
            border-color: rgba(216, 162, 74, 0.65) !important;
        }
        .bs-btn:focus { outline: none; box-shadow: 0 0 0 4px rgba(216,162,74,0.22); }

        /* Simple modal (slide up on mobile feel) */
        .modal-backdrop { background: rgba(10, 15, 12, 0.45); }
        .modal-panel {
            transform: translateY(16px);
            opacity: 0;
            transition: all .18s ease;
        }
        .modal-open .modal-panel {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>

<body class="min-h-screen text-slate-900" style="background:#F6F1E6;">
@php
    // Cozy tokens
    $GREEN = '#2F5D46';
    $GOLD  = '#D8A24A';
    $BG    = '#F6F1E6';
    $CARD  = '#FFFBF2';
@endphp

<div class="min-h-screen">
    {{-- Header (simple, like your Learn page) --}}
    <header class="border-b" style="background:{{ $GREEN }}; border-color: rgba(0,0,0,0.15);">
        <div class="mx-auto max-w-6xl px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/brusave-logo.png') }}"
                     alt="BruSave logo"
                     class="h-10 w-auto object-contain">
                <div>
                    <div class="text-2xl font-extrabold leading-tight" style="color:{{ $GOLD }};">
                        Bru<i>Save</i>
                    </div>
                    <div class="text-sm" style="color:{{ $GOLD }}; opacity:0.75;">
                        Build Wealth, Build Your Town
                    </div>
                </div>
            </div>

            <nav class="flex items-center">
                <a href="/dashboard"
                   class="px-5 py-3 rounded-xl font-semibold border transition hover:opacity-90"
                   style="border-color: rgba(255,255,255,0.25); color:#ffffff;">
                    Dashboard
                </a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 pb-16">

        {{-- Title --}}
        <section class="mt-10">
            <h1 class="text-3xl md:text-4xl font-extrabold" style="color:{{ $GREEN }};">
                Track Spending
            </h1>
            <p class="mt-2" style="color: rgba(47,93,70,0.82);">
                Add your daily income and expenses — we’ll keep a history and show simple totals.
            </p>
        </section>

        {{-- Stats cards --}}
        <section class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-3xl border shadow-lg p-5"
                 style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold tracking-wide uppercase"
                             style="color: rgba(47,93,70,0.65);">
                            Total Income
                        </div>
                        <div class="mt-1 text-2xl font-extrabold" style="color:{{ $GREEN }};">
                            <span id="incomeTotal">B$0.00</span>
                        </div>
                        <div class="mt-1 text-xs" style="color: rgba(47,93,70,0.65);">
                            This month
                        </div>
                    </div>
                    <div class="h-11 w-11 rounded-xl flex items-center justify-center text-xl border"
                         style="background: rgba(216,162,74,0.20); border-color: rgba(216,162,74,0.40);">
                        📈
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border shadow-lg p-5"
                 style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold tracking-wide uppercase"
                             style="color: rgba(47,93,70,0.65);">
                            Total Expenses
                        </div>
                        <div class="mt-1 text-2xl font-extrabold" style="color:{{ $GREEN }};">
                            <span id="expenseTotal">B$0.00</span>
                        </div>
                        <div class="mt-1 text-xs" style="color: rgba(47,93,70,0.65);">
                            This month
                        </div>
                    </div>
                    <div class="h-11 w-11 rounded-xl flex items-center justify-center text-xl border"
                         style="background: rgba(216,162,74,0.20); border-color: rgba(216,162,74,0.40);">
                        📉
                    </div>
                </div>
            </div>
        </section>

        {{-- Form card --}}
        <section class="mt-8 rounded-3xl border shadow-lg"
                 style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
            <div class="p-6 md:p-7">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-xl font-extrabold" style="color:{{ $GREEN }};">
                        Add a record
                    </h2>
                    <p class="text-sm" style="color: rgba(47,93,70,0.72);">
                        Saved automatically on this device (demo).
                    </p>
                </div>

                <form id="recordForm" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Type --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">
                            Type
                        </label>
                        <div class="mt-2 flex items-center gap-4">
                            <label class="inline-flex items-center gap-2 text-sm"
                                   style="color: rgba(47,93,70,0.85);">
                                <input type="radio" name="type" value="expense" checked>
                                Expense
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm"
                                   style="color: rgba(47,93,70,0.85);">
                                <input type="radio" name="type" value="income">
                                Income
                            </label>
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">
                            Amount
                        </label>
                        <div class="mt-2 flex items-center rounded-2xl border overflow-hidden"
                             style="border-color: rgba(47,93,70,0.18); background:#FFFFFF;">
                            <span class="px-4 py-3 text-sm font-semibold"
                                  style="color: rgba(47,93,70,0.72);">B$</span>
                            <input id="amount"
                                   type="number"
                                   min="0"
                                   step="0.01"
                                   inputmode="decimal"
                                   class="bs-input w-full px-4 py-3"
                                   placeholder="0.00"
                                   required
                                   style="background:#FFFFFF; border:0;">
                        </div>
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">
                            Date
                        </label>
                        <input id="date"
                               type="date"
                               class="bs-input mt-2 w-full rounded-2xl border px-4 py-3"
                               required
                               style="background:#FFFFFF; border-color: rgba(47,93,70,0.18);">
                    </div>

                    {{-- Category picker --}}
                    <div>
                        <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">
                            Category
                        </label>

                        <button type="button" id="openCategory"
                                class="bs-btn mt-2 w-full rounded-2xl border px-4 py-3 text-left font-semibold transition hover:opacity-95"
                                style="background:#FFFFFF; border-color: rgba(47,93,70,0.18); color: rgba(47,93,70,0.88);">
                            <span id="categoryLabel">Choose a category</span>
                            <span class="float-right" style="color: rgba(47,93,70,0.55);">▾</span>
                        </button>

                        <input type="hidden" id="category" required>
                    </div>

                    {{-- Other category manual --}}
                    <div id="otherWrap" class="hidden">
                        <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">
                            Other category
                        </label>
                        <input id="otherCategory"
                               type="text"
                               class="bs-input mt-2 w-full rounded-2xl border px-4 py-3"
                               placeholder="Type your category…"
                               style="background:#FFFFFF; border-color: rgba(47,93,70,0.18);">
                    </div>

                    {{-- Note --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">
                            Note (optional)
                        </label>
                        <textarea id="note"
                                  rows="3"
                                  class="bs-input mt-2 w-full rounded-2xl border px-4 py-3"
                                  placeholder="Example: Lunch with friends"
                                  style="background:#FFFFFF; border-color: rgba(47,93,70,0.18);"></textarea>
                    </div>

                    {{-- Photo --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold" style="color:{{ $GREEN }};">
                            Picture (optional)
                        </label>
                        <input id="photo"
                               type="file"
                               accept="image/*"
                               class="mt-2 w-full text-sm"
                               style="color: rgba(47,93,70,0.80);">
                        <p class="mt-1 text-xs" style="color: rgba(47,93,70,0.65);">
                            Demo: we store a small preview (not full photo) in localStorage.
                        </p>
                    </div>

                    {{-- Submit --}}
                    <div class="md:col-span-2 flex flex-wrap items-center gap-3 pt-1">
                        <button type="submit"
                                class="bs-btn px-6 py-3 rounded-2xl font-semibold transition hover:opacity-90"
                                style="background:{{ $GREEN }}; color:{{ $GOLD }};">
                            Add Record
                        </button>
                        <span class="text-sm" style="color: rgba(47,93,70,0.72);">
                            Your history appears below.
                        </span>
                    </div>
                </form>
            </div>
        </section>

        {{-- History --}}
        <section class="mt-8 rounded-3xl border shadow-lg overflow-hidden"
                 style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
            <div class="p-6 md:p-7">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-xl font-extrabold" style="color:{{ $GREEN }};">
                        History
                    </h2>
                    <button id="clearAll"
                            type="button"
                            class="bs-btn text-sm font-semibold underline underline-offset-4 transition hover:opacity-90"
                            style="color: rgba(47,93,70,0.78);">
                        Clear demo records
                    </button>
                </div>

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
                            <th class="text-left px-4 py-3 font-extrabold" style="color: rgba(47,93,70,0.85);">Pic</th>
                            <th class="text-right px-4 py-3 font-extrabold" style="color: rgba(47,93,70,0.85);">Action</th>
                        </tr>
                        </thead>

                        <tbody id="historyBody" class="divide-y" style="divide-color: rgba(47,93,70,0.10);">
                            {{-- Filled by JS --}}
                        </tbody>
                    </table>
                </div>

                <p class="mt-3 text-xs" style="color: rgba(47,93,70,0.65);">
                    Tip: later you can connect this to database + charts (teacher requirement).
                </p>
            </div>
        </section>

        <footer class="mt-10 text-center text-xs pb-8" style="color: rgba(47,93,70,0.75);">
            © {{ date('Y') }} Bru<i>Save</i>
        </footer>
    </main>
</div>

{{-- Category Modal --}}
<div id="categoryModal" class="fixed inset-0 hidden items-end sm:items-center justify-center p-4 z-50">
    <div class="absolute inset-0 modal-backdrop"></div>

    <div class="relative w-full max-w-md rounded-3xl border shadow-xl modal-panel"
         style="background:#FFFBF2; border-color: rgba(47,93,70,0.16);">
        <div class="p-5 border-b" style="border-color: rgba(47,93,70,0.12);">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-extrabold" style="color:#2F5D46;">Pick a category</div>
                    <div class="text-xs" style="color: rgba(47,93,70,0.70);">
                        Categories change based on Expense / Income.
                    </div>
                </div>
                <button id="closeCategory"
                        class="bs-btn px-3 py-2 rounded-xl border font-semibold"
                        style="border-color: rgba(47,93,70,0.18); color: rgba(47,93,70,0.80); background:#FFFFFF;">
                    ✕
                </button>
            </div>
        </div>

        <div class="p-5">
            <div id="categoryList" class="grid grid-cols-2 gap-3">
                {{-- Filled by JS --}}
            </div>

            <button id="categoryOther"
                    class="bs-btn mt-4 w-full px-4 py-3 rounded-2xl font-semibold border transition hover:opacity-95"
                    style="background:#FFFFFF; border-color: rgba(216,162,74,0.55); color:#2F5D46;">
                Other (type manually)
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Storage keys (demo)
    const STORAGE_KEY = 'brusave.track.records';

    // Categories
    const EXPENSE_CATS = ['Health','Leisure','Home','Cafe','Education','Gift','Groceries'];
    const INCOME_CATS  = ['Paycheck','Gift','Interest'];

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
    const incomeTotal = document.getElementById('incomeTotal');
    const expenseTotal = document.getElementById('expenseTotal');

    const openCategoryBtn = document.getElementById('openCategory');
    const modal = document.getElementById('categoryModal');
    const closeCategoryBtn = document.getElementById('closeCategory');
    const categoryList = document.getElementById('categoryList');
    const categoryOtherBtn = document.getElementById('categoryOther');

    const clearAllBtn = document.getElementById('clearAll');

    // Default date today
    const today = new Date();
    dateEl.valueAsDate = today;

    const getType = () => {
        const checked = document.querySelector('input[name="type"]:checked');
        return checked ? checked.value : 'expense';
    };

    const loadRecords = () => {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            const parsed = raw ? JSON.parse(raw) : [];
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            return [];
        }
    };

    const saveRecords = (records) => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(records));
    };

    const formatMoney = (n) => {
        const num = Number(n || 0);
        return 'B$' + num.toFixed(2);
    };

    const monthKey = (yyyy_mm_dd) => {
        // yyyy-mm-dd -> yyyy-mm
        return (yyyy_mm_dd || '').slice(0,7);
    };

    const updateTotals = () => {
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}`;

        const records = loadRecords();
        let inc = 0, exp = 0;

        records.forEach(r => {
            if (monthKey(r.date) !== currentMonth) return;
            const amt = Number(r.amount || 0);
            if (r.type === 'income') inc += amt;
            if (r.type === 'expense') exp += amt;
        });

        incomeTotal.textContent = formatMoney(inc);
        expenseTotal.textContent = formatMoney(exp);
    };

    const renderHistory = () => {
        const records = loadRecords().slice().sort((a,b) => (b.date || '').localeCompare(a.date || ''));

        if (records.length === 0) {
            historyBody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-sm"
                        style="color: rgba(47,93,70,0.70);">
                        No records yet — add your first expense or income above.
                    </td>
                </tr>
            `;
            return;
        }

        historyBody.innerHTML = records.map((r, idx) => {
            const typePillBg = r.type === 'income' ? 'rgba(47,93,70,0.10)' : 'rgba(216,162,74,0.18)';
            const typePillTx = r.type === 'income' ? '#2F5D46' : '#2F5D46';
            const amountColor = r.type === 'income' ? '#2F5D46' : 'rgba(180,60,60,0.95)';

            const pic = r.photoThumb
                ? `<img src="${r.photoThumb}" class="h-9 w-9 rounded-xl object-cover border"
                        style="border-color: rgba(47,93,70,0.18);" alt="pic">`
                : `<span style="color: rgba(47,93,70,0.45);">—</span>`;

            return `
                <tr>
                    <td class="px-4 py-3" style="color: rgba(47,93,70,0.85);">${r.date || ''}</td>

                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                              style="background:${typePillBg}; color:${typePillTx}; border: 1px solid rgba(47,93,70,0.12);">
                            ${r.type === 'income' ? 'Income' : 'Expense'}
                        </span>
                    </td>

                    <td class="px-4 py-3" style="color: rgba(47,93,70,0.85);">${r.category || ''}</td>

                    <td class="px-4 py-3 text-right font-extrabold" style="color:${amountColor};">
                        ${r.type === 'income' ? '+' : '-'}${formatMoney(r.amount).replace('B$','B$')}
                    </td>

                    <td class="px-4 py-3" style="color: rgba(47,93,70,0.78);">
                        ${r.note ? r.note.replace(/</g,'&lt;') : '—'}
                    </td>

                    <td class="px-4 py-3">${pic}</td>

                    <td class="px-4 py-3 text-right">
                        <button data-delete="${r.id}"
                                class="bs-btn text-xs font-semibold underline underline-offset-4 transition hover:opacity-90"
                                style="color: rgba(180,60,60,0.95);">
                            Delete
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        // Delete handlers
        historyBody.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-delete');
                const records = loadRecords().filter(r => String(r.id) !== String(id));
                saveRecords(records);
                renderHistory();
                updateTotals();
            });
        });
    };

    // Category modal rendering
    const openCategoryModal = () => {
        const type = getType();
        const list = type === 'income' ? INCOME_CATS : EXPENSE_CATS;

        categoryList.innerHTML = list.map(cat => `
            <button type="button"
                    class="bs-btn w-full px-4 py-3 rounded-2xl font-semibold border transition hover:opacity-95"
                    style="background:#FFFFFF; border-color: rgba(47,93,70,0.18); color:#2F5D46;"
                    data-cat="${cat}">
                ${cat}
            </button>
        `).join('');

        categoryList.querySelectorAll('[data-cat]').forEach(b => {
            b.addEventListener('click', () => {
                const cat = b.getAttribute('data-cat');
                categoryHidden.value = cat;
                categoryLabel.textContent = cat;
                otherWrap.classList.add('hidden');
                otherEl.value = '';
                closeCategoryModal();
            });
        });

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            modal.classList.add('modal-open');
        });
    };

    const closeCategoryModal = () => {
        modal.classList.remove('modal-open');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 180);
    };

    openCategoryBtn.addEventListener('click', openCategoryModal);
    closeCategoryBtn.addEventListener('click', closeCategoryModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal.querySelector('.modal-backdrop')) closeCategoryModal();
    });

    categoryOtherBtn.addEventListener('click', () => {
        categoryHidden.value = 'Other';
        categoryLabel.textContent = 'Other';
        otherWrap.classList.remove('hidden');
        closeCategoryModal();
        otherEl.focus();
    });

    // When switching type, reset category UI (because lists differ)
    document.querySelectorAll('input[name="type"]').forEach(r => {
        r.addEventListener('change', () => {
            categoryHidden.value = '';
            categoryLabel.textContent = 'Choose a category';
            otherWrap.classList.add('hidden');
            otherEl.value = '';
        });
    });

    // Add record
    const readPhotoThumb = (file) => {
        return new Promise((resolve) => {
            if (!file) return resolve(null);

            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = () => resolve(null);
            reader.readAsDataURL(file);
        });
    };

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

        if (!date) {
            alert('Please choose a date.');
            return;
        }

        if (!(amount > 0)) {
            alert('Please enter a valid amount greater than 0.');
            return;
        }

        const note = (noteEl.value || '').trim();
        const file = photoEl.files && photoEl.files[0] ? photoEl.files[0] : null;
        const photoThumb = await readPhotoThumb(file);

        const records = loadRecords();
        records.push({
            id: String(Date.now()),
            type,
            amount,
            category,
            date,
            note,
            photoThumb
        });

        saveRecords(records);

        // Reset form (keep date today)
        amountEl.value = '';
        noteEl.value = '';
        photoEl.value = '';
        categoryHidden.value = '';
        categoryLabel.textContent = 'Choose a category';
        otherWrap.classList.add('hidden');
        otherEl.value = '';

        renderHistory();
        updateTotals();

        alert('✅ Record added!');
    });

    // Clear demo records
    clearAllBtn.addEventListener('click', () => {
        if (!confirm('Clear all demo records stored on this device?')) return;
        localStorage.removeItem(STORAGE_KEY);
        renderHistory();
        updateTotals();
    });

    // Init
    renderHistory();
    updateTotals();
});
</script>

</body>
</html>