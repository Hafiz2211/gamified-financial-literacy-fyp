<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz Results • BruSave</title>

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
        .correct-answer {
            border-left: 4px solid #2F5D46;
            background: rgba(47,93,70,0.05);
        }
        .wrong-answer {
            border-left: 4px solid #b43c3c;
            background: rgba(180,60,60,0.05);
        }
        .correct-badge {
            background: #2F5D46;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .wrong-badge {
            background: #b43c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .answer-letter {
            display: inline-block;
            width: 24px;
            height: 24px;
            line-height: 24px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            margin-right: 8px;
        }
        .answer-letter.correct {
            background: #2F5D46;
            color: white;
        }
        .answer-letter.wrong {
            background: #b43c3c;
            color: white;
        }
        .answer-letter.neutral {
            background: rgba(47,93,70,0.2);
            color: #2F5D46;
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
    
    // Get user answers from database
    $userAnswers = DB::table('user_quiz_answers')
        ->where('quiz_attempt_id', $attempt->id)
        ->get()
        ->keyBy('question_id');
@endphp

<div class="app-container">
    {{-- Sidebar --}}
    @include('partials.sidebar', ['nav' => $nav, 'active' => $active, 'GREEN' => $GREEN, 'GOLD' => $GOLD])

    {{-- Main content --}}
    <div class="main-content">
        {{-- Profile dropdown --}}
        <div style="display: flex; justify-content: flex-end; padding: 20px 24px 0;">
            @include('components.profile-dropdown', ['user' => auth()->user()])
        </div>
        
        <div style="max-width:900px; margin:0 auto; padding:32px 24px;">
            
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <a href="{{ route('quiz.index') }}" class="text-sm hover:underline" style="color: {{ $GREEN }};">
                        ← Back to Quizzes
                    </a>
                    <h1 class="text-2xl font-bold mt-2" style="color: {{ $GREEN }};">{{ $quiz->title }} - Results</h1>
                    <p class="text-sm" style="color: rgba(47,93,70,0.7);">Attempt #{{ $attempt->attempt_number }}</p>
                </div>
                
                {{-- Score Card --}}
                <div class="text-right p-4 rounded-xl" style="background: {{ $CARD }};">
                    <div class="text-3xl font-bold" style="color: {{ $attempt->passed ? $GREEN : '#b43c3c' }};">
                        {{ $attempt->score }}%
                    </div>
                    <div class="text-sm font-semibold mt-1" style="color: {{ $attempt->passed ? $GREEN : '#b43c3c' }};">
                        {{ $attempt->passed ? '✅ PASSED' : '❌ FAILED' }}
                    </div>
                    @if($attempt->passed)
                        <div class="text-xs mt-2" style="color: {{ $GOLD }};">
                            +{{ $attempt->xp_earned }} XP • +{{ $attempt->coins_earned }} 🪙
                        </div>
                    @endif
                </div>
            </div>

            {{-- Results Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="rounded-xl p-4" style="background: {{ $CARD }};">
                    <div class="text-sm" style="color: rgba(47,93,70,0.65);">Correct Answers</div>
                    <div class="text-2xl font-bold" style="color: {{ $GREEN }};">
                        {{ $attempt->score * $quiz->questions->count() / 100 }} / {{ $quiz->questions->count() }}
                    </div>
                </div>
                <div class="rounded-xl p-4" style="background: {{ $CARD }};">
                    <div class="text-sm" style="color: rgba(47,93,70,0.65);">Date Completed</div>
                    <div class="text-lg font-bold" style="color: {{ $GREEN }};">
                        {{ $attempt->completed_at->format('d M Y') }}
                    </div>
                </div>
                <div class="rounded-xl p-4" style="background: {{ $CARD }};">
                    <div class="text-sm" style="color: rgba(47,93,70,0.65);">Time</div>
                    <div class="text-lg font-bold" style="color: {{ $GREEN }};">
                        {{ $attempt->completed_at->format('H:i') }}
                    </div>
                </div>
            </div>

            {{-- Message based on pass/fail --}}
            @if(!$attempt->passed)
                <div class="mb-6 p-4 rounded-xl" style="background: rgba(180,60,60,0.1); border: 1px solid #b43c3c;">
                    <div class="flex items-center gap-3">
                        <span style="font-size: 24px;">📝</span>
                        <div>
                            <p class="font-bold" style="color: #b43c3c;">Keep Learning!</p>
                            <p class="text-sm" style="color: rgba(47,93,70,0.8);">
                                You can see which questions you got wrong below. Review the lessons and try again to unlock the correct answers!
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Questions Review --}}
            <h2 class="text-xl font-bold mb-4" style="color: {{ $GREEN }};">Question Review</h2>
            
            <div class="space-y-4">
                @foreach($quiz->questions as $index => $question)
                    @php
                        $userAnswer = $userAnswers[$question->id] ?? null;
                        $isCorrect = $userAnswer && $userAnswer->is_correct;
                        $selectedOption = $userAnswer ? $userAnswer->selected_option : null;
                        $correctOption = $question->correct_option;
                    @endphp
                    
                    <div class="rounded-xl border p-5 {{ $isCorrect ? 'correct-answer' : 'wrong-answer' }}" 
                         style="background: {{ $CARD }};">
                        
                        {{-- Question Header --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-sm font-bold"
                                      style="background: {{ $GREEN }}; color: white;">
                                    {{ $index + 1 }}
                                </span>
                                <span class="font-semibold" style="color: {{ $GREEN }};">{{ $question->question_text }}</span>
                            </div>
                            <span class="{{ $isCorrect ? 'correct-badge' : 'wrong-badge' }}">
                                {{ $isCorrect ? '✓ Correct' : '✗ Incorrect' }}
                            </span>
                        </div>

                        {{-- Your Answer --}}
                        @if($selectedOption)
                            <div class="mb-3">
                                <div class="text-xs font-semibold mb-2" style="color: rgba(47,93,70,0.65);">YOUR ANSWER:</div>
                                <div class="flex items-center">
                                    <span class="answer-letter {{ $isCorrect ? 'correct' : 'wrong' }}">
                                        {{ $selectedOption }}
                                    </span>
                                    <span style="color: rgba(47,93,70,0.85);">
                                        {{ $question->{'option_' . strtolower($selectedOption)} }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        {{-- 🔴 DIFFERENT BEHAVIOR BASED ON PASS/FAIL --}}
                        @if(!$isCorrect)
                            @if($attempt->passed)
                                {{-- PASSED: Show correct answer --}}
                                <div class="mt-3 pt-3 border-t" style="border-color: rgba(47,93,70,0.12);">
                                    <div class="text-xs font-semibold mb-2" style="color: {{ $GREEN }};">CORRECT ANSWER:</div>
                                    <div class="flex items-center">
                                        <span class="answer-letter correct">
                                            {{ $correctOption }}
                                        </span>
                                        <span style="color: rgba(47,93,70,0.85);">
                                            {{ $question->{'option_' . strtolower($correctOption)} }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                {{-- FAILED: Show hint to try again --}}
                                <div class="mt-3 pt-3 border-t text-center" style="border-color: rgba(47,93,70,0.12);">
                                    <p class="text-sm italic" style="color: {{ $GOLD }};">
                                        🔒 Review the lesson and try again to reveal the correct answer
                                    </p>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Action Buttons --}}
            <div class="mt-8 flex gap-4">
                @if(!$attempt->passed)
                    {{-- FAILED: Show Try Again button --}}
                    <a href="{{ route('quiz.take', $quiz) }}" 
                       class="px-6 py-3 rounded-xl font-semibold transition hover:opacity-90"
                       style="background: {{ $GREEN }}; color: {{ $GOLD }};">
                        🔄 Try Again
                    </a>
                @endif
                
                <a href="{{ route('quiz.index') }}" 
                   class="px-6 py-3 rounded-xl font-semibold border transition hover:opacity-90"
                   style="border-color: rgba(47,93,70,0.2); color: {{ $GREEN }};">
                    📚 All Quizzes
                </a>
                
                @if($attempt->passed && $quiz->order < 3)
                    @php
                        $nextQuiz = App\Models\Quiz::where('order', $quiz->order + 1)->first();
                    @endphp
                    @if($nextQuiz)
                        <a href="{{ route('quiz.take', $nextQuiz) }}" 
                           class="px-6 py-3 rounded-xl font-semibold transition hover:opacity-90"
                           style="background: {{ $GREEN }}; color: {{ $GOLD }};">
                            Next Level →
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
    @include('partials.music')
</body>
</html>