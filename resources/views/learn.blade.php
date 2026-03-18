<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Learn • BruSave</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
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

<body class="text-slate-900" style="background:#F6F1E6; margin:0; height:100vh; overflow:hidden;">
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
                <button onclick="this.parentElement.parentElement.parentElement.remove(); 
                    fetch('/clear-notification', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });" 
                    style="background:none; border:none; color:rgba(255,255,255,0.7); cursor:pointer; font-size:18px; margin-left:8px;">✕</button>
            </div>
        </div>
    </div>
    
    {{-- Auto clear after 5 seconds --}}
    <script>
        setTimeout(function() {
            fetch('/clear-notification', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
        }, 5000);
    </script>
@endif

@php
    $user = auth()->user();
    $GREEN = '#2F5D46';
    $GOLD  = '#D8A24A';
    $CARD  = '#FFFBF2';
    $active = 'learn';
    
    $nav = [
        ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard','icon'=>'🏠'],
        ['key'=>'learn','label'=>'Learn','href'=>'/learn','icon'=>'📖'],
        ['key'=>'quiz','label'=>'Quiz','href'=>'/quiz','icon'=>'❓'],
        ['key'=>'track','label'=>'Track Spending','href'=>'/track-spending','icon'=>'🧾'],
        ['key'=>'progress','label'=>'Room','href'=>'/progress','icon'=>'🏆'],
        ['key'=>'town','label'=>'Town','href'=>'/town','icon'=>'🏘️'],
    ];

    // Get completed lessons with error handling
    $completedLessonIds = [];
    $completedLessons = [];
    
    try {
        if ($user && method_exists($user, 'lessons')) {
            $completedLessonIds = $user->lessons()->pluck('lesson_id')->toArray();
        }
    } catch (\Exception $e) {
        \Log::error('Error fetching lessons: ' . $e->getMessage());
    }
    
    // Map database IDs to string IDs
    $lessonIdMap = [
        1 => 'needs-vs-wants',
        2 => 'budgeting-simple',
        3 => 'income-vs-expense',
        4 => 'saving-goals',
        5 => 'tracking-spending',
        6 => 'emergency-fund'
    ];
    
    // Convert numeric IDs to string IDs for completed lessons
    foreach ($completedLessonIds as $numericId) {
        if (isset($lessonIdMap[$numericId])) {
            $completedLessons[] = $lessonIdMap[$numericId];
        }
    }

    $lessons = [
        [
            'id' => 'needs-vs-wants',
            'title' => 'Needs vs Wants',
            'summary' => 'Making Spending Decisions in Daily Student Life.',
            'points' => [
                'A university student receives a monthly allowance of BND 200 from their parents. At the beginning of the month, they plan to use the money for meals, transportation to campus, mobile data for academic use, and printing assignments.',
                'However, during the week, the student notices that many of their classmates frequently purchase coffee or specialty drinks before lectures. Some also upgrade their phone accessories or buy branded sportswear for training sessions.',
                'One afternoon, the student considers buying a BND 6 drink after class. The purchase feels small and harmless. However, if the same decision is made daily after lectures, the weekly spending becomes BND 30 and approximately BND 120 per month.',
                'Although the drink provides enjoyment, it does not directly contribute to survival, academic performance, or work-related responsibilities. It is therefore categorized as a want rather than a need.',
                'If the student delays the purchase decision by one day using the 24-hour rule, they may later realize that the money could instead be used for transportation or saving towards badminton tournament registration fees next month.',
                'This situation demonstrates how distinguishing between needs and wants helps students make rational financial decisions instead of emotionally influenced ones.'
            ],
            'tip' => 'Pause before you purchase and ask if it helps your studies or just your mood.',
            'xp_reward' => 50,
            'coin_reward' => 50
        ],
        [
            'id' => 'budgeting-simple',
            'title' => 'Budgeting',
            'summary' => 'Allocating Allowance Before Spending.',
            'points' => [
                'A student receives BND 250 at the start of each month. Instead of spending freely until the allowance runs out, they decide to create a budget plan.',
                'They allocate:',
                'BND 150 for essential expenses (meals and transport)',
                'BND 50 for savings',
                'BND 50 for leisure activities',
                'Immediately after receiving the allowance, the student transfers BND 50 into a separate savings account.',
                'Later in the month, when invited to dine out several times, the student refers to the leisure category in their budget before making a decision. This prevents overspending and ensures that essential expenses remain covered.',
                'This example demonstrates how budgeting allows students to proactively manage financial resources rather than reacting to spending impulses.'
            ],
            'tip' => 'Decide where your money goes before the month begins.',
            'xp_reward' => 50,
            'coin_reward' => 50
        ],
        [
            'id' => 'income-vs-expense',
            'title' => 'Income vs Expense',
            'summary' => 'Managing Monthly Allowance Sustainably.',
            'points' => [
                'A part-time student earns BND 300 per month from tutoring and also receives BND 150 in allowance. This results in a total monthly income of BND 450.',
                'Throughout the month, the student spends:',
                'BND 180 on food',
                'BND 70 on transportation',
                'BND 60 on mobile data',
                'BND 90 on dining out with friends',
                'BND 80 on online shopping',
                'By the end of the month, the total expenses amount to BND 480.',
                'Although the student had income at the start of the month, their expenses exceeded earnings by BND 30. If this pattern continues for several months, savings will gradually decrease and financial stress may occur.',
                'This scenario illustrates that financial stability depends not only on receiving money but on ensuring that expenses remain within sustainable limits over time.'
            ],
            'tip' => 'Make sure your spending stays within what you earn each month.',
            'xp_reward' => 50,
            'coin_reward' => 50
        ],
        [
            'id' => 'saving-goals',
            'title' => 'Saving Goals',
            'summary' => 'Planning for Upcoming Academic Needs.',
            'points' => [
                'A student’s laptop battery requires replacement, which is expected to cost BND 120 within three months.',
                'Instead of postponing the issue until the device fails completely, the student sets a saving goal to accumulate the required amount within twelve weeks.',
                'By dividing BND 120 by twelve weeks, the student determines that saving BND 10 per week will achieve the target within the deadline.',
                'Each week, the student transfers BND 10 into a designated savings account. By the end of the third month, the required amount is available without the need to borrow money from family or friends.',
                'This scenario illustrates how setting specific saving goals with clear deadlines promotes consistent financial discipline.'
            ],
            'tip' => 'Set a clear target and save a fixed amount regularly.',
            'xp_reward' => 50,
            'coin_reward' => 50
        ],
        [
            'id' => 'tracking-spending',
            'title' => 'Tracking Spending',
            'summary' => 'Identifying Hidden Spending Patterns.',
            'points' => [
                'A student believes they only spend around BND 50 per month on snacks and drinks.',
                'To verify this assumption, they begin recording daily purchases in a mobile notes application for four weeks. Each time they buy bottled drinks, snacks between lectures, or small convenience items, they record the amount spent.',
                'At the end of the month, the recorded data shows that total spending on snacks and drinks was actually BND 135.',
                'The difference between perceived and actual spending highlights how relying on memory can result in inaccurate financial judgments. By tracking spending behaviour using simple recording methods, students can identify unnecessary expenditure and make adjustments for the following month.'
            ],
            'tip' => 'Small daily expenses add up. Record them to stay in control.',
            'xp_reward' => 50,
            'coin_reward' => 50
        ],
        [
            'id' => 'emergency-fund',
            'title' => 'Emergency Fund',
            'summary' => 'Preparing for Unexpected Situations.',
            'points' => [
                'While commuting to campus, a student accidentally drops their smartphone, causing screen damage that requires immediate repair costing BND 90.',
                'Without emergency savings, the student may need to borrow money from friends or delay the repair, which could disrupt communication and academic coordination.',
                'However, if the student had previously built an emergency fund by saving BND 5 per week over several months, they would have sufficient funds to cover the repair cost immediately.',
                'This preparation reduces financial stress and prevents reliance on external assistance during urgent situations.'
            ],
            'tip' => 'Prepare for unexpected costs by saving a little consistently.',
            'xp_reward' => 50,
            'coin_reward' => 50
        ],
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
            {{-- Intro --}}
            <section style="max-width:768px;">
                <h1 style="font-size:36px; font-weight:800; color:{{ $GREEN }}; margin-bottom:12px;">
                    Learn Financial Basics
                </h1>
                <p style="color:rgba(47,93,70,0.85); margin-top:8px;">
                    Short, practical lessons designed to be relatable in Brunei. Read at your own pace — quizzes will test what you learn.
                </p>
            </section>

            {{-- Progress Bar --}}
            <section style="margin-top:32px; padding:24px; border-radius:24px; border:1px solid rgba(47,93,70,0.16); background:{{ $CARD }}; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
                <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
                    <div>
                        <h2 style="font-size:18px; font-weight:700; color:{{ $GREEN }};">Your lesson progress</h2>
                        <p style="font-size:14px; margin-top:4px; color:rgba(47,93,70,0.75);">
                            Complete lessons to earn <span style="color:{{ $GOLD }};">50 XP</span> and <span style="color:{{ $GOLD }};">50 coins</span> each!
                        </p>
                    </div>

                    <div style="font-size:14px; font-weight:600; color:rgba(47,93,70,0.85);">
                        <span id="progressText">{{ count($completedLessons) }}/{{ count($lessons) }} lessons completed</span>
                    </div>
                </div>

                <div style="margin-top:16px; height:12px; border-radius:9999px; overflow:hidden; background:rgba(47,93,70,0.22);">
                    <div id="progressBar"
                         style="height:100%; border-radius:9999px; width:{{ count($lessons) > 0 ? (count($completedLessons) / count($lessons)) * 100 : 0 }}%; background:{{ $GOLD }};"></div>
                </div>
            </section>

            {{-- Lessons --}}
            <section style="margin-top:40px; display:grid; gap:24px;">
                @foreach ($lessons as $lesson)
                    @php $isCompleted = in_array($lesson['id'], $completedLessons); @endphp
                    <article style="border-radius:24px; border:1px solid rgba(47,93,70,0.16); background:{{ $CARD }}; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); transition:all 0.2s;">
                        <div style="padding:24px;">
                            <h2 style="font-size:24px; font-weight:600; color:{{ $GREEN }};">
                                {{ $lesson['title'] }}
                            </h2>
                            <p style="margin-top:4px; color:rgba(47,93,70,0.85);">
                                {{ $lesson['summary'] }}
                            </p>

                            <details style="margin-top:16px; border-radius:12px; border:1px solid rgba(47,93,70,0.16); padding:16px; background:rgba(47,93,70,0.09);"
                                     data-lesson-id="{{ $lesson['id'] }}">
                                <summary style="cursor:pointer; font-weight:600; display:flex; align-items:center; justify-content:space-between; color:{{ $GREEN }};">
                                    <span>Read lesson</span>
                                    <span style="font-size:18px; color:rgba(47,93,70,0.55);">▾</span>
                                </summary>

                                <ul style="margin-top:16px; list-style-type:disc; padding-left:20px; display:flex; flex-direction:column; gap:8px; color:rgba(20,30,25,0.92);">
                                    @foreach ($lesson['points'] as $point)
                                        <li>{{ $point }}</li>
                                    @endforeach
                                </ul>

                                <div style="margin-top:16px; padding:16px; border-radius:12px; border:1px solid rgba(216,162,74,0.60); background:rgba(216,162,74,0.25);">
                                    <strong style="color:{{ $GOLD }};">Tip:</strong>
                                    <span style="color:rgba(20,30,25,0.92);">{{ $lesson['tip'] }}</span>
                                </div>

                                {{-- Finish reading --}}
                                <div style="margin-top:16px; display:flex; flex-wrap:wrap; align-items:center; gap:12px;">
                                    @if(!$isCompleted)
                                        <button
                                            type="button"
                                            class="complete-lesson-btn"
                                            data-lesson-id="{{ $lesson['id'] }}"
                                            data-xp="{{ $lesson['xp_reward'] }}"
                                            data-coins="{{ $lesson['coin_reward'] }}"
                                            style="padding:8px 16px; border-radius:12px; font-weight:600; background:{{ $GREEN }}; color:{{ $GOLD }}; border:none; cursor:pointer;">
                                            Finish & Earn 50 XP / 50 🪙
                                        </button>
                                    @else
                                        <button
                                            disabled
                                            style="padding:8px 16px; border-radius:12px; font-weight:600; background:rgba(47,93,70,0.85); color:#F6F1E6; border:none; opacity:0.6; cursor:not-allowed;">
                                            Completed ✅
                                        </button>
                                    @endif
                                </div>
                            </details>
                        </div>
                    </article>
                @endforeach
            </section>

            {{-- CTA --}}
            <section style="margin-top:40px; padding:24px; border-radius:24px; border:1px solid rgba(47,93,70,0.16); background:{{ $CARD }}; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
                <h3 style="font-size:18px; font-weight:700; color:{{ $GREEN }};">Next step</h3>
                <p style="margin-top:8px; color:rgba(47,93,70,0.85);">
                    When you're ready, try the quiz to test your understanding. Quiz levels unlock as your level increases.
                </p>

                <a href="/quiz"
                   style="display:inline-block; margin-top:16px; padding:12px 24px; border-radius:12px; font-weight:600; background:{{ $GREEN }}; color:{{ $GOLD }}; text-decoration:none;">
                    Go to Quiz
                </a>
            </section>

            <footer style="text-align:center; font-size:12px; padding-bottom:32px; color:rgba(47,93,70,0.75);">
                © {{ date('Y') }} Bru<i>Save</i>
            </footer>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const detailsEls = Array.from(document.querySelectorAll('details'));
    const completeButtons = document.querySelectorAll('.complete-lesson-btn');
    const progressText = document.getElementById('progressText');
    const progressBar = document.getElementById('progressBar');
    const totalLessons = {{ count($lessons) }};

    // Function to show level up notification
    function showLevelUpNotification(newLevel) {
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
                            You reached Level ${newLevel}!
                        </div>
                    </div>
                    <button onclick="this.parentElement.parentElement.parentElement.remove();" style="background:none; border:none; color:rgba(255,255,255,0.7); cursor:pointer; font-size:18px; margin-left:8px;">✕</button>
                </div>
            </div>
        `;
        document.body.appendChild(notif);
        
        // Auto remove after 5 seconds
        setTimeout(() => notif.remove(), 5000);
    }

    // Update progress display
    const updateProgress = async () => {
        try {
            const response = await fetch('/user/lesson-progress');
            const data = await response.json();
            
            const completed = data.completed_count;
            progressText.textContent = `${completed}/${totalLessons} lessons completed`;
            const pct = totalLessons > 0 ? (completed / totalLessons) * 100 : 0;
            progressBar.style.width = `${pct}%`;
        } catch (error) {
            console.error('Error updating progress:', error);
        }
    };

    // Handle lesson completion
    completeButtons.forEach(btn => {
        btn.addEventListener('click', async () => {
            const lessonId = btn.dataset.lessonId;
            const xpReward = btn.dataset.xp;
            const coinReward = btn.dataset.coins;

            try {
                const response = await fetch('/lessons/complete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ lesson_id: lessonId })
                });

                const result = await response.json();

                if (response.ok) {
                    btn.textContent = 'Completed ✅';
                    btn.disabled = true;
                    btn.style.background = 'rgba(47,93,70,0.85)';
                    btn.style.color = '#F6F1E6';

                    alert(`✅ Lesson completed!\n+${xpReward} XP\n+${coinReward} coins`);

                    // 🔥 Show level up notification if it happened
                    if (result.level_up) {
                        showLevelUpNotification(result.new_level);
                    }

                    await updateProgress();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (result.message === 'Lesson already completed') {
                        btn.textContent = 'Completed ✅';
                        btn.disabled = true;
                        btn.style.background = 'rgba(47,93,70,0.85)';
                        btn.style.color = '#F6F1E6';
                        await updateProgress();
                        alert('You already completed this lesson!');
                    } else {
                        alert('Error: ' + (result.message || 'Failed to complete lesson'));
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error completing lesson');
            }
        });
    });

    // One lesson open at a time
    detailsEls.forEach(d => {
        d.addEventListener('toggle', () => {
            if (d.open) {
                detailsEls.forEach(other => {
                    if (other !== d) other.open = false;
                });
            }
        });
    });
});
</script>
</body>
</html>