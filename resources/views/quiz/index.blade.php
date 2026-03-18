<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz • BruSave</title>

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
    $GOLD = '#D8A24A';
    $BG = '#F6F1E6';
    $CARD = '#FFFBF2';
    $active = 'quiz';
    
    $nav = [
        ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard','icon'=>'🏠'],
        ['key'=>'learn','label'=>'Learn','href'=>'/learn','icon'=>'📖'],
        ['key'=>'quiz','label'=>'Quiz','href'=>'/quiz','icon'=>'❓'],
        ['key'=>'track','label'=>'Track Spending','href'=>'/track-spending','icon'=>'🧾'],
        ['key'=>'progress','label'=>'Room','href'=>'/progress','icon'=>'🏆'],
        ['key'=>'town','label'=>'Town','href'=>'/town','icon'=>'🏘️'],
    ];
@endphp

<div class="app-container">
    {{-- Sidebar with profile dropdown --}}
    @include('partials.sidebar', ['nav' => $nav, 'active' => $active, 'GREEN' => $GREEN, 'GOLD' => $GOLD])

    {{-- Main content --}}
    <div class="main-content">
        {{-- Profile dropdown in top right --}}
        <div style="display: flex; justify-content: flex-end; padding: 20px 24px 0;">
            @include('components.profile-dropdown', ['user' => auth()->user()])
        </div>
        
        <div style="max-width:1200px; margin:0 auto; padding:32px 24px;">
            {{-- Header --}}
            <section class="mb-8">
                <h1 class="text-3xl md:text-4xl font-extrabold" style="color:{{ $GREEN }};">Financial Quizzes</h1>
                <p class="mt-2" style="color: rgba(47,93,70,0.82);">
                    Test your knowledge and earn rewards. Complete each level to unlock the next.
                </p>
            </section>

            {{-- Quiz Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($quizzes as $quiz)
                    @php $status = $quizStatus[$quiz->id] ?? []; @endphp
                    
                    <div class="rounded-3xl border shadow-lg overflow-hidden transition-all hover:shadow-xl"
                         style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16); 
                                opacity: {{ isset($status['available']) && $status['available'] ? '1' : '0.85' }};">
                        
                        {{-- Quiz Header --}}
                        <div class="p-5 border-b" style="border-color: rgba(47,93,70,0.12); 
                                background: {{ $status['status'] == 'completed' ? 'rgba(47,93,70,0.05)' : 'rgba(216,162,74,0.05)' }};">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold px-3 py-1 rounded-full"
                                      style="background: {{ $status['status'] == 'completed' ? $GREEN : $GOLD }}20; 
                                             color: {{ $status['status'] == 'completed' ? $GREEN : $GOLD }};">
                                    Level {{ $quiz->order }}
                                </span>
                                @if($status['status'] == 'completed')
                                    <span class="text-sm" style="color: {{ $GREEN }};">✅ Completed</span>
                                @elseif($status['locked'] ?? false)
                                    <span class="text-sm" style="color: rgba(47,93,70,0.55);">🔒 Locked</span>
                                @else
                                    <span class="text-sm" style="color: {{ $GOLD }};">📝 Ready</span>
                                @endif
                            </div>
                            <h2 class="text-xl font-bold mt-3" style="color:{{ $GREEN }};">{{ $quiz->title }}</h2>
                            <p class="text-sm mt-1" style="color: rgba(47,93,70,0.75);">
                                {{ $quiz->description }}
                            </p>
                        </div>
                        
                        {{-- Quiz Details --}}
                        <div class="p-5 space-y-3">
                            <div class="flex items-center gap-2 text-sm" style="color: rgba(47,93,70,0.75);">
                                <span>📊</span>
                                <span>{{ $quiz->questions->count() }} questions</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm" style="color: rgba(47,93,70,0.75);">
                                <span>🎯</span>
                                <span>Pass: {{ $quiz->passing_score }}%</span>
                            </div>
                            
                            @if($status['status'] != 'completed' && isset($status['next_reward']))
                                <div class="flex items-center gap-2 text-sm" style="color: {{ $GOLD }};">
                                    <span>🏆</span>
                                    <span>Next attempt: {{ $status['next_reward']['xp'] }} XP / {{ $status['next_reward']['coins'] }} 🪙</span>
                                </div>
                            @endif
                            
                            @if(isset($status['reason']) && !$status['available'])
                                <div class="text-xs p-2 rounded-lg" style="background: rgba(47,93,70,0.08); color: rgba(47,93,70,0.75);">
                                    ⓘ {{ $status['reason'] }}
                                </div>
                            @endif
                            
                            {{-- Action Button --}}
                            @if($status['status'] == 'completed')
                                <div class="mt-2 space-y-2">
                                    <button disabled
                                            class="w-full px-4 py-3 rounded-xl font-semibold border cursor-not-allowed"
                                            style="background: rgba(47,93,70,0.1); color: rgba(47,93,70,0.55);">
                                        Completed ✅
                                    </button>
                                    
                                    {{-- View Best Attempt Button --}}
                                    @if(isset($status['best_attempt']))
                                        <a href="{{ route('quiz.results', ['quiz' => $quiz->id, 'attempt' => $status['best_attempt']->id]) }}"
                                           class="block w-full px-4 py-2 rounded-xl font-semibold text-center transition hover:opacity-90 text-sm"
                                           style="background: rgba(216,162,74,0.15); color: {{ $GOLD }}; border: 1px solid rgba(216,162,74,0.3);">
                                            📊 View Best Attempt ({{ $status['best_attempt']->score }}%)
                                        </a>
                                    @endif
                                </div>
                            @elseif($status['available'] ?? false)
                                <a href="{{ route('quiz.take', $quiz) }}"
                                   class="block w-full mt-2 px-4 py-3 rounded-xl font-semibold text-center transition hover:opacity-90"
                                   style="background: {{ $GREEN }}; color: {{ $GOLD }};">
                                    Start Quiz
                                </a>
                            @else
                                <button disabled
                                        class="w-full mt-2 px-4 py-3 rounded-xl font-semibold border cursor-not-allowed"
                                        style="background: rgba(47,93,70,0.05); color: rgba(47,93,70,0.45);">
                                    Locked
                                </button>
                            @endif
                        </div>
                        
                        {{-- Progress if attempted --}}
                        @if(isset($status['previous_attempts']) && $status['previous_attempts'] > 0)
                            <div class="px-5 pb-5">
                                <div class="text-xs mb-1" style="color: rgba(47,93,70,0.65);">
                                    Attempts: {{ $status['previous_attempts'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            {{-- Info Section --}}
            <section class="mt-12 p-6 rounded-3xl border" style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                <h3 class="text-lg font-bold" style="color:{{ $GREEN }};">How It Works</h3>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm" style="color: rgba(47,93,70,0.8);">
                    <div class="flex gap-2">
                        <span style="color: {{ $GOLD }};">1️⃣</span>
                        <span>Complete Level 1 to unlock Level 2, then Level 2 to unlock Level 3</span>
                    </div>
                    <div class="flex gap-2">
                        <span style="color: {{ $GOLD }};">2️⃣</span>
                        <span>Higher rewards for first attempts (90 XP) — decreases gradually to 60 XP</span>
                    </div>
                    <div class="flex gap-2">
                        <span style="color: {{ $GOLD }};">3️⃣</span>
                        <span>Reward only on PASS (70%+). Once passed, you can't earn more from that level</span>
                    </div>
                </div>
            </section>
            
            <footer class="text-center text-xs pt-12 pb-2" style="color: rgba(47,93,70,0.75);">
                © {{ date('Y') }} Bru<i>Save</i>
            </footer>
        </div>
    </div>
</div>
</body>
</html>