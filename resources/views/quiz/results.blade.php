<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz Results • BruSave</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        ['key'=>'progress','label'=>'Progress / Rewards','href'=>'/progress','icon'=>'🏆'],
        ['key'=>'town','label'=>'Town','href'=>'/town','icon'=>'🏘️'],
    ];
@endphp

<div style="display:flex; height:100vh; width:100vw; overflow:hidden;">
    {{-- Sidebar --}}
    <div style="width:270px; height:100vh; background:#2F5D46; flex-shrink:0;">
        <!-- Copy sidebar from other files -->
        <div style="padding:20px; border-bottom:1px solid rgba(255,255,255,0.12);">
            <div style="color:{{ $GOLD }}; font-size:20px; font-weight:800;">BruSave</div>
        </div>
        <nav style="padding:16px;">
            @foreach($nav as $item)
                <a href="{{ $item['href'] }}" style="display:block; padding:12px; color:white;">{{ $item['label'] }}</a>
            @endforeach
        </nav>
    </div>

    {{-- Main Content --}}
    <div style="flex:1; overflow-y:auto; padding:32px; max-width:900px; margin:0 auto;">
        <a href="{{ route('quiz.index') }}" style="color:{{ $GREEN }};">← Back to Quizzes</a>
        
        <h1 style="color:{{ $GREEN }}; font-size:24px; margin:20px 0;">{{ $quiz->title }} - Results</h1>
        
        {{-- Score Card --}}
        <div style="background:{{ $CARD }}; border-radius:16px; padding:24px; margin-bottom:24px;">
            <div style="font-size:48px; font-weight:bold; color:{{ $attempt->passed ? $GREEN : '#b43c3c' }};">
                {{ $attempt->score }}%
            </div>
            <div style="color:rgba(47,93,70,0.7);">
                {{ $attempt->passed ? '✅ Passed' : '❌ Failed' }} • 
                Attempt #{{ $attempt->attempt_number }} • 
                {{ $attempt->xp_earned }} XP / {{ $attempt->coins_earned }} 🪙
            </div>
        </div>

        {{-- Questions Review --}}
        @foreach($quiz->questions as $index => $question)
            @php
                $userAnswer = $userAnswers[$question->id] ?? null;
                $isCorrect = $userAnswer && $userAnswer->is_correct;
                $selectedOption = $userAnswer ? $userAnswer->selected_option : 'Not answered';
                $correctOption = $question->correct_option;
            @endphp
            
            <div style="background:{{ $CARD }}; border-radius:16px; padding:20px; margin-bottom:16px; 
                        border-left:4px solid {{ $isCorrect ? $GREEN : '#b43c3c' }};">
                
                <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                    <span style="font-weight:bold; color:{{ $GREEN }};">Question {{ $index+1 }}</span>
                    <span style="color:{{ $isCorrect ? $GREEN : '#b43c3c' }};">
                        {{ $isCorrect ? '✅ Correct' : '❌ Incorrect' }}
                    </span>
                </div>
                
                <p style="margin-bottom:16px;">{{ $question->question_text }}</p>
                
                <div style="background:rgba(47,93,70,0.05); padding:12px; border-radius:8px;">
                    <div style="margin-bottom:8px;">
                        <span style="font-weight:bold;">Your answer:</span> 
                        <span style="color:{{ $isCorrect ? $GREEN : '#b43c3c' }};">
                            {{ $selectedOption }}. {{ $question->{'option_' . strtolower($selectedOption)} ?? 'Not answered' }}
                        </span>
                    </div>
                    
                    @if(!$isCorrect)
                        <div>
                            <span style="font-weight:bold;">Correct answer:</span> 
                            <span style="color:{{ $GREEN }};">
                                {{ $correctOption }}. {{ $question->{'option_' . strtolower($correctOption)} }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Action Buttons --}}
        <div style="display:flex; gap:16px; margin-top:24px;">
            <a href="{{ route('quiz.take', $quiz) }}" 
               style="background:{{ $GREEN }}; color:{{ $GOLD }}; padding:12px 24px; border-radius:12px; text-decoration:none;">
                🔄 Try Again
            </a>
            <a href="{{ route('quiz.index') }}" 
               style="border:1px solid rgba(47,93,70,0.2); color:{{ $GREEN }}; padding:12px 24px; border-radius:12px; text-decoration:none;">
                📚 All Quizzes
            </a>
        </div>
    </div>
</div>
</body>
</html>
