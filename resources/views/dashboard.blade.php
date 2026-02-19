{{-- resources/views/dashboard.blade.php --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard • BruSave</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Necessary: ensure emojis render on Windows/Edge too --}}
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial,
                         "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji";
        }
    </style>
</head>

<body class="min-h-screen text-slate-900" style="background:#F6F1E6;">
@php
    // Demo data (swap with DB later)
    $userName = auth()->check() ? (auth()->user()->name ?? 'Hafiz') : 'Hafiz';

    $stats = [
<<<<<<< HEAD
        ['label' => 'Current Balance', 'value' => 'B$0', 'sub' => 'Updated today', 'icon' => ''],
=======
        ['label' => 'Current Balance', 'value' => 'B$0', 'sub' => 'Updated today', 'icon' => '💰'],
>>>>>>> origin/home-page
        ['label' => 'Total Expenses',  'value' => 'B$0', 'sub' => 'This month',    'icon' => '📉'],
        ['label' => 'Available coins', 'value' => '0',   'sub' => 'Keep learning!', 'icon' => '🪙'],
    ];

    $level = ['label'=>'Current Level','value'=>'Level 0','progress'=>0,'sub'=>'0% to next level'];

    $recent = [
        ['title'=>'Completed Module: Budgeting Basics','time'=>'2 hours ago','delta'=>'+50 pts','type'=>'plus'],
        ['title'=>'Expenses Added: Lunch','time'=>'5 hours ago','delta'=>'-B$12.50','type'=>'minus'],
        ['title'=>'Quiz Completed: Savings 101','time'=>'Yesterday','delta'=>'+30 pts','type'=>'plus'],
    ];

    $active = 'home';
@endphp

<div class="min-h-screen flex">
    {{-- Sidebar (ALWAYS visible) --}}
    <aside class="w-[270px] h-screen flex flex-col border-r"
           style="
                background:#2F5D46;
                border-color: rgba(47,93,70,0.22);
           ">
        <div class="p-5 border-b"
             style="border-color: rgba(255,255,255,0.12);">
            <div class="flex items-center gap-3">
                <img
                    src="{{ asset('images/brusave-logo.png') }}"
                    alt="BruSave logo"
                    class="h-8 w-auto object-contain"
                >
                <div>
                    <div class="text-xl font-extrabold leading-tight" style="color:#D8A24A;">
                        Bru<i>Save</i>
                    </div>
                    <div class="text-xs" style="color: rgba(216,162,74,0.78);">
                        Build Wealth, Build Your Town
                    </div>
                </div>
            </div>
        </div>

        @php
            $nav = [
                ['key'=>'home','label'=>'Home','href'=>'/dashboard','icon'=>'🏠'],
                ['key'=>'learn','label'=>'Learn','href'=>'/learn','icon'=>'📖'],
                ['key'=>'quiz','label'=>'Quiz','href'=>'/quiz','icon'=>'❓'],
                ['key'=>'track','label'=>'Track Spending','href'=>'/track-spending','icon'=>'🧾'],
                ['key'=>'progress','label'=>'Progress / Rewards','href'=>'/progress','icon'=>'🏆'],
                ['key'=>'town','label'=>'Town','href'=>'/town','icon'=>'🏘️'],
            ];
        @endphp

        {{-- nav scrolls if needed; logout stays visible --}}
        <nav class="p-4 space-y-2 flex-1 overflow-y-auto">
            @foreach ($nav as $item)
                @php $isActive = $active === $item['key']; @endphp

                <a href="{{ $item['href'] }}"
                   class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border transition hover:opacity-95"
                   style="
                        border-color: {{ $isActive ? 'rgba(216,162,74,0.60)' : 'rgba(255,255,255,0.16)' }};
                        background:  {{ $isActive ? 'rgba(216,162,74,0.14)' : 'rgba(255,255,255,0.04)' }};
                        color:       {{ $isActive ? '#D8A24A' : 'rgba(255,255,255,0.92)' }};
                   ">
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
                        style="
                            border-color: rgba(255,255,255,0.16);
                            color: rgba(255,255,255,0.92);
                            background: rgba(255,255,255,0.04);
                        ">
                    <span>🚪</span> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <main class="flex-1 h-screen flex flex-col overflow-y-auto">
        <div class="flex flex-col flex-1 mx-auto max-w-6xl px-6 py-8">

            {{-- Welcome header --}}
            <section class="rounded-2xl border shadow-lg p-6 md:p-7"
                     style="border-color: rgba(47,93,70,0.16); background:#FFFBF2;">
                <h1 class="text-2xl md:text-3xl font-extrabold" style="color:#2F5D46;">
                    Welcome back, {{ $userName }}
                </h1>
                <p class="mt-2" style="color: rgba(47,93,70,0.78);">
                    Track your spending and continue learning to earn more points.
                </p>

                {{-- Stat cards --}}
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($stats as $card)
                        <div class="rounded-2xl border p-5 shadow-sm"
                             style="border-color: rgba(47,93,70,0.16); background:#FFFBF2;">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-xs font-semibold tracking-wide uppercase"
                                         style="color: rgba(47,93,70,0.65);">
                                        {{ $card['label'] }}
                                    </div>
                                    <div class="mt-1 text-2xl font-extrabold" style="color:#2F5D46;">
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

                    {{-- Level --}}
                    <div class="rounded-2xl border p-5 shadow-sm"
                         style="border-color: rgba(47,93,70,0.16); background:#FFFBF2;">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="text-xs font-semibold tracking-wide uppercase"
                                     style="color: rgba(47,93,70,0.65);">
                                    {{ $level['label'] }}
                                </div>
                                <div class="mt-1 text-2xl font-extrabold" style="color:#2F5D46;">
                                    {{ $level['value'] }}
                                </div>

                                <div class="mt-3">
                                    <div class="h-2.5 rounded-full overflow-hidden"
                                         style="background: rgba(47,93,70,0.18);">
                                        <div class="h-full rounded-full"
                                             style="width: {{ $level['progress'] }}%; background:#D8A24A;"></div>
                                    </div>
                                    <div class="mt-2 text-xs" style="color: rgba(47,93,70,0.65);">
                                        {{ $level['sub'] }}
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

                {{-- Recent activity --}}
                <div class="mt-6">
                    <div class="rounded-2xl border p-5 shadow-sm"
                         style="border-color: rgba(47,93,70,0.16); background:#FFFBF2;">
                        <h2 class="text-lg font-extrabold" style="color:#D8A24A;">Recent Activity</h2>

                        <div class="mt-4 divide-y" style="divide-color: rgba(47,93,70,0.12);">
                            @foreach ($recent as $r)
                                <div class="py-4 flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="font-semibold truncate" style="color: rgba(20,30,25,0.92);">
                                            {{ $r['title'] }}
                                        </div>
                                        <div class="text-xs mt-1" style="color: rgba(47,93,70,0.65);">
                                            {{ $r['time'] }}
                                        </div>
                                    </div>

                                    <div class="shrink-0 text-sm font-extrabold"
                                         style="color: {{ $r['type']==='plus' ? '#D8A24A' : 'rgba(180,60,60,0.95)' }};">
                                        {{ $r['delta'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <footer class="mt-auto text-center text-xs pt-8 pb-2" style="color: rgba(47,93,70,0.75);">
                © {{ date('Y') }} Bru<i>Save</i>
            </footer>
        </div>
    </main>
</div>
</body>
</html>