<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard • BruSave</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
@php
    $levelUpData = session('level_up');
    $usePut = false;
    
    if (!$levelUpData && session()->has('level_up_put')) {
        $levelUpData = session('level_up_put');
        $usePut = true;
    }
@endphp

@if($levelUpData)
    <div id="levelUpNotification" class="level-up-notification" style="position:fixed; top:20px; right:20px; z-index:9999;">
        <div style="background:#2F5D46; color:#D8A24A; padding:16px 24px; border-radius:16px; box-shadow:0 10px 25px rgba(0,0,0,0.2); border-left:4px solid #D8A24A;">
            <div style="display:flex; align-items:center; gap:12px;">
                <span style="font-size:28px;">🎉</span>
                <div>
                    <div style="font-weight:800; font-size:18px;">Level Up!</div>
                    <div style="font-size:14px; color:rgba(255,255,255,0.9);">
                        You reached Level {{ $levelUpData['new_level'] }}!
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.parentElement.remove(); 
                    @if($usePut)
                        fetch('/clear-notification', {
                            method: 'POST', 
                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                        });
                    @endif" 
                    style="background:none; border:none; color:rgba(255,255,255,0.7); cursor:pointer; font-size:18px; margin-left:8px;">✕</button>
            </div>
        </div>
    </div>
@endif

@php
    $user = auth()->user();
    $userName = $user->name ?? 'Hafiz';
    $GREEN = '#2F5D46';
    $GOLD  = '#D8A24A';
    $BG    = '#F6F1E6';
    $CARD  = '#FFFBF2';
    $active = 'dashboard';

    // Calculate current balance
    $totalIncome = $user->transactions()->where('type', 'income')->sum('amount');
    $totalExpense = $user->transactions()->where('type', 'expense')->sum('amount');
    $currentBalance = $totalIncome - $totalExpense;

    // Monthly expenses
    $monthlyExpense = $user->transactions()
        ->where('type', 'expense')
        ->whereMonth('date', now()->month)
        ->whereYear('date', now()->year)
        ->sum('amount');

    // 🔴 KEEP YOUR ORIGINAL THRESHOLDS (matches User.php)
    $currentLevel = $user->level ?? 1;
    $currentXP = $user->xp ?? 0;

    $levelThresholds = [
        1 => 0,
        2 => 300,
        3 => 700,
        4 => 1200,
        5 => 1800,
        6 => 2500,
        7 => 3300,
        8 => 4200,
        9 => 5200,
        10 => 6300,
    ];

    // 🔴 FIXED: Calculate XP correctly for your thresholds
    $currentThreshold = $levelThresholds[$currentLevel] ?? 0;
    $nextThreshold = $levelThresholds[$currentLevel + 1] ?? ($currentThreshold + 600);
    
    // XP earned in current level (never negative, never over limit)
    $xpInCurrentLevel = max(0, $currentXP - $currentThreshold);
    
    // Total XP needed for next level
    $xpNeededForNextLevel = $nextThreshold - $currentThreshold;
    
    // Progress percentage (capped at 100%)
    $xpProgress = $xpNeededForNextLevel > 0 ? min(100, ($xpInCurrentLevel / $xpNeededForNextLevel) * 100) : 0;
    
    // Display text
    $xpDisplay = $xpInCurrentLevel . '/' . $xpNeededForNextLevel . ' XP to Level ' . ($currentLevel + 1);

    // Title based on level
    if ($currentLevel <= 2) {
        $title = 'New Mayor';
    } elseif ($currentLevel <= 4) {
        $title = 'Rising Mayor';
    } elseif ($currentLevel <= 6) {
        $title = 'Great Mayor';
    } else {
        $title = 'Legendary Mayor';
    }

    // Recent activity
    $recentTransactions = $user->transactions()->latest()->take(5)->get();

    $stats = [
        ['label' => 'Current Balance', 'value' => 'B$' . number_format($currentBalance, 2), 'sub' => 'Updated today', 'icon' => '💰'],
        ['label' => 'Total Expenses',  'value' => 'B$' . number_format($monthlyExpense, 2), 'sub' => 'This month',    'icon' => '📉'],
        ['label' => 'Available coins', 'value' => $user->coins ?? 0,   'sub' => 'Keep learning!', 'icon' => '🪙'],
    ];

    $nav = [
        ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard','icon'=>'🏠'],
        ['key'=>'learn','label'=>'Learn','href'=>'/learn','icon'=>'📖'],
        ['key'=>'quiz','label'=>'Quiz','href'=>'/quiz','icon'=>'❓'],
        ['key'=>'track','label'=>'Track Spending','href'=>'/track-spending','icon'=>'🧾'],
        ['key'=>'progress','label'=>'Achievement','href'=>'/progress','icon'=>'🏆'],
        ['key'=>'town','label'=>'Town','href'=>'/town','icon'=>'🏘️'],
    ];
@endphp

<div class="app-container">
    @include('partials.sidebar', ['nav' => $nav, 'active' => $active, 'GREEN' => $GREEN, 'GOLD' => $GOLD])

    <div class="main-content">
        <div style="display: flex; justify-content: flex-end; padding: 20px 24px 0;">
            @include('components.profile-dropdown', ['user' => auth()->user()])
        </div>
        
        <div style="max-width:1200px; margin:0 auto; padding:32px 24px;">
            <section class="rounded-2xl border shadow-lg p-6 md:p-7"
                     style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                <h1 class="text-2xl md:text-3xl font-extrabold" style="color:{{ $GREEN }};">
                    Welcome back, Mayor {{ $userName }}
                </h1>
                <p class="mt-2" style="color: rgba(47,93,70,0.78);">
                    Track your spending and earn coins and XP to unlock and purchase decorations and buildings.
                </p>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($stats as $card)
                        <div class="rounded-2xl border p-5 shadow-sm"
                             style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-xs font-semibold tracking-wide uppercase"
                                         style="color: rgba(47,93,70,0.65);">
                                        {{ $card['label'] }}
                                    </div>
                                    <div class="mt-1 text-2xl font-extrabold" style="color:{{ $GREEN }};">
                                        {{ $card['value'] }}
                                    </div>
                                    <div class="mt-1 text-xs" style="color: rgba(47,93,70,0.65);">
                                        {{ $card['sub'] }}
                                    </div>
                                </div>
                                <div class="h-11 w-11 rounded-xl flex items-center justify-center text-xl border"
                                     style="background: rgba(216,162,74,0.20); border-color: rgba(216,162,74,0.40);">
                                    {{ $card['icon'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="rounded-2xl border p-5 shadow-sm"
                         style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="text-xs font-semibold tracking-wide uppercase"
                                     style="color: rgba(47,93,70,0.65);">
                                    Current Level
                                </div>
                                <div class="mt-1 text-2xl font-extrabold" style="color:{{ $GREEN }};">
                                    Level {{ $currentLevel }}
                                </div>
                                <div class="mt-1 text-sm font-medium" style="color: {{ $GOLD }};">
                                    {{ $title }}
                                </div>
                                <div class="mt-3">
                                    <div class="h-2.5 rounded-full overflow-hidden"
                                         style="background: rgba(47,93,70,0.18);">
                                        <div class="h-full rounded-full"
                                             style="width: {{ $xpProgress }}%; background:{{ $GOLD }};"></div>
                                    </div>
                                    <div class="mt-2 text-xs" style="color: rgba(47,93,70,0.65);">
                                        {{ $xpDisplay }}
                                    </div>
                                </div>
                            </div>
                            <div class="h-11 w-11 rounded-xl flex items-center justify-center text-xl border"
                                 style="background: rgba(216,162,74,0.20); border-color: rgba(216,162,74,0.40);">
                                ⭐
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="rounded-2xl border p-5 shadow-sm"
                         style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                        <h2 class="text-lg font-extrabold" style="color:{{ $GOLD }};">Recent Activity</h2>

                        <div class="mt-4 divide-y" style="divide-color: rgba(47,93,70,0.12);">
                            @forelse($recentTransactions as $transaction)
                                @php
                                    $isIncome = $transaction->type === 'income';
                                    $activityTitle = $isIncome ? 'Income: ' . $transaction->category : 'Expense: ' . $transaction->category;
                                    $time = $transaction->created_at->diffForHumans();
                                    $delta = $isIncome ? '+' : '-';
                                    $delta .= 'B$' . number_format($transaction->amount, 2);
                                @endphp
                                <div class="py-4 flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="font-semibold truncate" style="color: rgba(20,30,25,0.92);">
                                            {{ $activityTitle }}
                                        </div>
                                        <div class="text-xs mt-1" style="color: rgba(47,93,70,0.65);">
                                            {{ $time }}
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-sm font-extrabold"
                                         style="color: {{ $isIncome ? $GOLD : 'rgba(180,60,60,0.95)' }};">
                                        {{ $delta }}
                                    </div>
                                </div>
                            @empty
                                <div class="py-4 text-center" style="color: rgba(47,93,70,0.65);">
                                    No recent activity. Start tracking your spending!
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <footer class="text-center text-xs pt-8 pb-2" style="color: rgba(47,93,70,0.75);">
                © {{ date('Y') }} Bru<i>Save</i>
            </footer>
        </div>
    </div>
</div>
    @include('partials.music')
</body>
</html>