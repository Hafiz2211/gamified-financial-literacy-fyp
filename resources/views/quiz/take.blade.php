<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $quiz->title }} • BruSave Quiz</title>

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
        
        .quiz-option {
            transition: all 0.2s ease;
            border: 2px solid transparent;
            position: relative;
        }
        
        .quiz-option:hover {
            background: rgba(216,162,74,0.15);
            border-color: rgba(216,162,74,0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(47,93,70,0.1);
        }
        
        .quiz-option.selected {
            background: rgba(216,162,74,0.25) !important;
            border-color: #D8A24A !important;
            border-width: 2px !important;
            box-shadow: 0 8px 16px rgba(216,162,74,0.3);
            transform: scale(1.02);
        }
        
        .quiz-option.selected .w-6.h-6 {
            background: #D8A24A !important;
            color: white !important;
            transform: scale(1.1);
        }
        
        .quiz-option .checkmark {
            opacity: 0;
            transition: opacity 0.2s;
            color: #D8A24A;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .quiz-option.selected .checkmark {
            opacity: 1;
        }
        
        @keyframes gentlePulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.03); }
            100% { transform: scale(1.02); }
        }
        
        .quiz-option.selected {
            animation: gentlePulse 0.3s ease-out;
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
        ['key'=>'progress','label'=>'Achievement','href'=>'/progress','icon'=>'🏆'],
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
        
        <div style="max-width:900px; margin:0 auto; padding:32px 24px;">
            {{-- Quiz Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <a href="{{ route('quiz.index') }}" class="text-sm hover:underline" style="color: {{ $GREEN }};">
                        ← Back to Quizzes
                    </a>
                    <h1 class="text-2xl font-bold mt-2" style="color: {{ $GREEN }};">{{ $quiz->title }}</h1>
                    <p class="text-sm" style="color: rgba(47,93,70,0.7);">{{ $quiz->description }}</p>
                </div>
                <div class="text-right">
                    <div class="text-sm font-semibold px-4 py-2 rounded-full" 
                         style="background: {{ $GOLD }}20; color: {{ $GOLD }};">
                        Attempt #{{ $attemptNumber }}
                    </div>
                    <div class="text-xs mt-2" style="color: rgba(47,93,70,0.65);">
                        Pass: {{ $quiz->passing_score }}% • {{ $quiz->questions->count() }} questions
                    </div>
                </div>
            </div>

            {{-- Previous Attempts Warning --}}
            @if($previousAttempts->isNotEmpty())
                <div class="mb-6 p-4 rounded-xl" style="background: rgba(216,162,74,0.1); border: 1px solid rgba(216,162,74,0.3);">
                    <div class="flex items-center gap-2 text-sm" style="color: {{ $GOLD }};">
                        <span>📋</span>
                        <span>Previous attempts: {{ $previousAttempts->count() }}</span>
                    </div>
                    <div class="text-xs mt-1" style="color: rgba(47,93,70,0.7);">
                        Best score: {{ $previousAttempts->max('score') }}% • 
                        Reward for this attempt: {{ $status['next_reward']['xp'] }} XP / {{ $status['next_reward']['coins'] }} 🪙 (if passed)
                    </div>
                </div>
            @endif

            {{-- Quiz Form --}}
            <form id="quizForm" class="space-y-6">
                @csrf
                
                @foreach($quiz->questions as $index => $question)
                    <div class="rounded-3xl border p-6" style="background: {{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-sm font-bold"
                                  style="background: {{ $GREEN }}; color: white;">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1">
                                <p class="font-semibold mb-4" style="color: {{ $GREEN }};">{{ $question->question_text }}</p>
                                
                                <div class="space-y-3">
                                    @foreach(['A', 'B', 'C', 'D'] as $option)
                                        @php
                                            $optionText = $question->{'option_' . strtolower($option)};
                                        @endphp
                                        <label class="quiz-option block p-4 rounded-xl border cursor-pointer"
                                               style="border-color: rgba(47,93,70,0.16); background: white;">
                                            <input type="radio" 
                                                   name="answers[{{ $question->id }}]" 
                                                   value="{{ $option }}" 
                                                   class="hidden"
                                                   required>
                                            <div class="flex items-center gap-3">
                                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-sm font-medium"
                                                      style="background: rgba(47,93,70,0.1); color: {{ $GREEN }};">
                                                    {{ $option }}
                                                </span>
                                                <span style="color: rgba(47,93,70,0.85);">{{ $optionText }}</span>
                                                <span class="checkmark ml-auto">✓</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Submit Button --}}
                <div class="sticky bottom-0 py-4" style="background: linear-gradient(to top, {{ $BG }}, transparent);">
                    <div class="flex items-center justify-between">
                        <div class="text-sm" style="color: rgba(47,93,70,0.7);">
                            <span id="answeredCount">0</span>/{{ $quiz->questions->count() }} answered
                        </div>
                        <button type="submit" 
                                id="submitQuiz"
                                class="px-8 py-3 rounded-xl font-semibold transition hover:opacity-90"
                                style="background: {{ $GREEN }}; color: {{ $GOLD }};">
                            Submit Quiz
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Results Modal --}}
<div id="resultsModal" class="fixed inset-0 hidden items-center justify-center p-4" style="z-index: 1000; background: rgba(10,15,12,0.45);">
    <div class="relative w-full max-w-md rounded-3xl border shadow-xl" style="background: {{ $CARD }};">
        <div class="p-6 text-center">
            <div id="resultIcon" class="text-6xl mb-4">🎉</div>
            <h2 id="resultTitle" class="text-2xl font-bold" style="color: {{ $GREEN }};">Quiz Complete!</h2>
            <p id="resultMessage" class="mt-2" style="color: rgba(47,93,70,0.8);"></p>
            
            <div class="mt-6 p-4 rounded-xl" style="background: rgba(47,93,70,0.05);">
                <div class="flex justify-between mb-2">
                    <span style="color: rgba(47,93,70,0.7);">Score:</span>
                    <span id="resultScore" class="font-bold" style="color: {{ $GREEN }};">0%</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span style="color: rgba(47,93,70,0.7);">Correct:</span>
                    <span id="resultCorrect" class="font-bold" style="color: {{ $GREEN }};">0/0</span>
                </div>
                <div id="resultReward" class="flex justify-between pt-2 border-t" style="border-color: rgba(47,93,70,0.1);">
                    <span style="color: rgba(47,93,70,0.7);">Reward:</span>
                    <span id="rewardAmount" class="font-bold" style="color: {{ $GOLD }};">0 XP / 0 🪙</span>
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <a href="{{ route('quiz.index') }}" 
                   class="flex-1 px-4 py-2 rounded-xl font-semibold border transition hover:opacity-90"
                   style="border-color: rgba(47,93,70,0.2); color: {{ $GREEN }};">
                    Quizzes
                </a>
                
                <span id="resultButtonContainer" class="flex-1">
                    <button onclick="location.reload()" 
                            id="retryBtn"
                            class="w-full px-4 py-2 rounded-xl font-semibold transition hover:opacity-90"
                            style="background: {{ $GREEN }}; color: {{ $GOLD }}; display: none;">
                        Try Again
                    </button>
                    <a href="#" 
                       id="viewDetailsBtn"
                       class="w-full px-4 py-2 rounded-xl font-semibold transition hover:opacity-90 text-center"
                       style="background: {{ $GREEN }}; color: {{ $GOLD }}; display: none;">
                        📊 View Details
                    </a>
                </span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('quizForm');
    const submitBtn = document.getElementById('submitQuiz');
    const modal = document.getElementById('resultsModal');
    const answeredCount = document.getElementById('answeredCount');
    const retryBtn = document.getElementById('retryBtn');
    const viewDetailsBtn = document.getElementById('viewDetailsBtn');
    
    retryBtn.style.display = 'none';
    viewDetailsBtn.style.display = 'none';
    
    const radios = document.querySelectorAll('input[type="radio"]');
    const updateAnsweredCount = () => {
        const checked = document.querySelectorAll('input[type="radio"]:checked').length;
        answeredCount.textContent = checked;
    };
    
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            const questionDiv = this.closest('.rounded-3xl');
            
            questionDiv.querySelectorAll('.quiz-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            const parentLabel = this.closest('.quiz-option');
            parentLabel.classList.add('selected');
            
            updateAnsweredCount();
            
            parentLabel.style.transform = 'scale(0.98)';
            setTimeout(() => {
                parentLabel.style.transform = '';
            }, 100);
        });
    });
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const totalQuestions = {{ $quiz->questions->count() }};
        const answered = document.querySelectorAll('input[type="radio"]:checked').length;
        
        if (answered < totalQuestions) {
            alert(`Please answer all questions (${answered}/${totalQuestions})`);
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch('{{ route("quiz.submit", $quiz) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showResults(result);
            } else {
                alert(result.message || 'Error submitting quiz');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Quiz';
            }
            
        } catch (error) {
            console.error('Error:', error);
            alert('Error submitting quiz');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Quiz';
        }
    });
    
    function showResults(result) {
        document.getElementById('resultIcon').textContent = result.passed ? '🎉' : '📝';
        document.getElementById('resultTitle').textContent = result.passed ? 'Congratulations!' : 'Keep Trying!';
        document.getElementById('resultMessage').textContent = result.message;
        document.getElementById('resultScore').textContent = result.score + '%';
        document.getElementById('resultCorrect').textContent = result.correct + '/' + result.total;
        
        const rewardDiv = document.getElementById('resultReward');
        
        viewDetailsBtn.style.display = 'block';
        viewDetailsBtn.href = '/quiz/' + {{ $quiz->id }} + '/results/' + result.attempt_id;
        
        if (result.passed && result.reward_claimed) {
            document.getElementById('rewardAmount').textContent = result.xp_earned + ' XP / ' + result.coins_earned + ' 🪙';
            rewardDiv.style.display = 'flex';
            retryBtn.style.display = 'none';
        } else {
            rewardDiv.style.display = 'none';
            retryBtn.style.display = 'block';
        }
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        if (result.level_up) {
            setTimeout(() => {
                const notif = document.createElement('div');
                notif.className = 'level-up-notification';
                notif.style.cssText = 'position:fixed; top:20px; right:20px; z-index:9999;';
                notif.innerHTML = `
                    <div style="background:#2F5D46; color:#D8A24A; padding:16px 24px; border-radius:16px; box-shadow:0 10px 25px rgba(0,0,0,0.2); border-left:4px solid #D8A24A;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <span style="font-size:28px;">🎉</span>
                            <div>
                                <div style="font-weight:800; font-size:18px;">Level Up!</div>
                                <div style="font-size:14px; color:rgba(255,255,255,0.9);">
                                    You reached Level ${result.level_up.new_level}!
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(notif);
                setTimeout(() => notif.remove(), 5000);
            }, 500);
        }
        
        if (result.next_unlocked) {
            setTimeout(() => {
                alert(`🎉 ${result.next_unlocked} is now unlocked!`);
            }, 1000);
        }
    }
});
</script>
    @include('partials.music')
</body>
</html>