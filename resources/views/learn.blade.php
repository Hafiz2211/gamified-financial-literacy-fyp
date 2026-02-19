{{-- resources/views/learn.blade.php --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Learn • BruSave</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col text-slate-900" style="background:#F6F1E6;">
    {{-- Cozy Mayor tokens (ONLY colors changed)
        Primary green:   #2F5D46
        Accent gold:     #D8A24A
        Warm cream bg:   #F6F1E6
        Cozy card cream: #FFFBF2
    --}}

    {{-- Header --}}
    <header class="border-b" style="background:#2F5D46; border-color: rgba(0,0,0,0.15);">
    <div class="mx-auto max-w-6xl px-6 py-3 flex items-center justify-between">

        <div class="flex items-center gap-3">

            {{-- Logo --}}
            <img
                src="{{ asset('images/brusave-logo.png') }}"
                alt="BruSave logo"
                class="h-10 w-auto object-contain"
            >

            <div>
                <div class="text-2xl font-extrabold leading-tight"
                     style="color:#D8A24A;">
                    Bru<i>Save</i>
                </div>
                <div class="text-sm"
                     style="color:#D8A24A; opacity:0.75;">
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

    <main class="flex-1 mx-auto max-w-6xl px-6 pb-16">
        {{-- Intro --}}
        <section class="mt-10 max-w-3xl">
            <h1 class="text-3xl md:text-4xl font-extrabold" style="color:#2F5D46;">
                Learn Financial Basics
            </h1>
            <p class="mt-3" style="color: rgba(47,93,70,0.85);">
                Short, practical lessons designed to be relatable in Brunei. Read at your own pace — quizzes will test what you learn.
            </p>
        </section>

        @php
            $lessons = [
                [
                    'id' => 'needs-vs-wants',
                    'title' => 'Needs vs Wants',
                    'summary' => 'Separate essentials from optional spending.',
                    'points' => [
                        'WIP',
                        'WIP',
                        'WIP'
                    ],
                    'tip' => 'WIP'
                ],
                [
                    'id' => 'budgeting-simple',
                    'title' => 'Budgeting (Simple Method)',
                    'summary' => 'Make a plan so your money doesn’t disappear.',
                    'points' => [
                        'WIP',
                        'WIP',
                        'WIP'
                    ],
                    'tip' => 'WIP'
                ],
                [
                    'id' => 'income-vs-expense',
                    'title' => 'Income vs Expense',
                    'summary' => 'Understand money in and money out.',
                    'points' => [
                        'WIP',
                        'WIP',
                        'WIP'
                    ],
                    'tip' => 'WIP'
                ],
                [
                    'id' => 'saving-goals',
                    'title' => 'Saving Goals',
                    'summary' => 'Save with a clear target and deadline.',
                    'points' => [
                        'WIP',
                        'WIP',
                        'WIP'
                    ],
                    'tip' => 'WIP'
                ],
                [
                    'id' => 'emergency-fund',
                    'title' => 'Emergency Fund',
                    'summary' => 'Be ready for surprises without panic.',
                    'points' => [
                        'WIP',
                        'WIP',
                        'WIP'
                    ],
                    'tip' => 'WIP'
                ],
            ];

            // Demo progress storage key (per-page)
            $progressKey = 'brusave.learn.completed';
        @endphp

        {{-- STATUS / PROGRESS BAR --}}
        <section class="mt-8 rounded-3xl p-6 border shadow-lg"
                 style="border-color: rgba(47,93,70,0.16); background:#FFFBF2;">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold" style="color:#2F5D46;">Your lesson progress</h2>
                    <p class="text-sm mt-1" style="color: rgba(47,93,70,0.75);">
                        Keep going — progress saves automatically on this device.
                    </p>
                </div>

                <div class="text-sm font-semibold"
                     style="color: rgba(47,93,70,0.85);">
                    <span id="progressText">0/0 lessons completed</span>
                </div>
            </div>

            <div class="mt-4 h-3 rounded-full overflow-hidden"
                 style="background: rgba(47,93,70,0.22);">
                <div id="progressBar"
                     class="h-full rounded-full"
                     style="width: 0%; background:#D8A24A;"></div>
            </div>
        </section>

        {{-- Lessons --}}
        <section class="mt-10 grid gap-6">
            @foreach ($lessons as $lesson)
                <article class="rounded-3xl border shadow-lg transition duration-200 hover:-translate-y-1 hover:shadow-xl"
                         style="border-color: rgba(47,93,70,0.16); background:#FFFBF2;">
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold" style="color:#2F5D46;">
                            {{ $lesson['title'] }}
                        </h2>
                        <p class="mt-1" style="color: rgba(47,93,70,0.85);">
                            {{ $lesson['summary'] }}
                        </p>

                        <details class="mt-4 rounded-xl border p-4 lesson-details"
                                 data-lesson-id="{{ $lesson['id'] }}"
                                 style="border-color: rgba(47,93,70,0.16); background: rgba(47,93,70,0.09);">
                            <summary class="cursor-pointer font-semibold flex items-center justify-between select-none"
                                     style="color:#2F5D46;">
                                <span>Read lesson</span>
                                <span aria-hidden="true" class="text-lg" style="color: rgba(47,93,70,0.55);">▾</span>
                            </summary>

                            <ul class="mt-4 list-disc pl-5 space-y-2" style="color: rgba(20,30,25,0.92);">
                                @foreach ($lesson['points'] as $point)
                                    <li>{{ $point }}</li>
                                @endforeach
                            </ul>

                            <div class="mt-4 rounded-xl p-4 border"
                                 style="border-color: rgba(216,162,74,0.60); background: rgba(216,162,74,0.25);">
                                <strong style="color:#D8A24A;">Tip:</strong>
                                <span style="color: rgba(20,30,25,0.92);">{{ $lesson['tip'] }}</span>
                            </div>

                            {{-- Finish reading --}}
                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                <button
                                    type="button"
                                    class="px-4 py-2 rounded-xl font-semibold transition hover:opacity-90 finish-btn"
                                    data-lesson-id="{{ $lesson['id'] }}"
                                    style="background:#2F5D46; color:#D8A24A;"
                                >
                                    Finish reading
                                </button>

                                <span class="text-sm" style="color: rgba(47,93,70,0.75);">
                                    This will give XP & coins later.
                                </span>
                            </div>
                        </details>
                    </div>
                </article>
            @endforeach
        </section>

        {{-- CTA --}}
        <section class="mt-10 rounded-3xl p-6 border shadow-lg transition duration-200 hover:shadow-xl"
                 style="border-color: rgba(47,93,70,0.16); background:#FFFBF2;">
            <h3 class="text-lg font-bold" style="color:#2F5D46;">Next step</h3>
            <p class="mt-2" style="color: rgba(47,93,70,0.85);">
                When you’re ready, try the quiz to test your understanding. Quiz levels unlock as your level increases.
            </p>

            <a href="/quiz"
               class="inline-block mt-4 px-6 py-3 rounded-xl font-semibold transition hover:opacity-90"
               style="background:#2F5D46; color:#D8A24A;">
                Go to Quiz
            </a>
        </section>
    </main>

    <footer class="text-center text-xs pb-8" style="color: rgba(47,93,70,0.75);">
        © {{ date('Y') }} Bru<i>Save</i>
    </footer>

    {{-- Scripts: progress + one-open-only + persistent completion (device/localStorage) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const STORAGE_KEY = @json($progressKey);

            const detailsEls = Array.from(document.querySelectorAll('.lesson-details'));
            const finishBtns = Array.from(document.querySelectorAll('.finish-btn'));

            const progressText = document.getElementById('progressText');
            const progressBar  = document.getElementById('progressBar');

            // Load completed set
            const loadCompleted = () => {
                try {
                    const raw = localStorage.getItem(STORAGE_KEY);
                    const parsed = raw ? JSON.parse(raw) : [];
                    return new Set(Array.isArray(parsed) ? parsed : []);
                } catch {
                    return new Set();
                }
            };

            const saveCompleted = (set) => {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(set)));
            };

            const setButtonCompletedUI = (btn, completed) => {
                if (!btn) return;

                if (completed) {
                    btn.textContent = 'Completed ✅';
                    btn.disabled = true;
                    btn.classList.add('opacity-80', 'cursor-not-allowed');
                    btn.style.background = 'rgba(47,93,70,0.85)';
                    btn.style.color = '#F6F1E6';
                } else {
                    btn.textContent = 'Finish reading';
                    btn.disabled = false;
                    btn.classList.remove('opacity-80', 'cursor-not-allowed');
                    btn.style.background = '#2F5D46';
                    btn.style.color = '#D8A24A';
                }
            };

            const updateProgressUI = (completedSet) => {
                const total = detailsEls.length;
                const done = completedSet.size;

                progressText.textContent = `${done}/${total} lessons completed`;
                const pct = total === 0 ? 0 : Math.round((done / total) * 100);
                progressBar.style.width = `${pct}%`;
            };

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

            // Init from storage
            const completed = loadCompleted();
            finishBtns.forEach(btn => {
                const id = btn.dataset.lessonId;
                setButtonCompletedUI(btn, completed.has(id));
            });
            updateProgressUI(completed);

            // Finish reading click
            finishBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.lessonId;

                    const completedSet = loadCompleted();
                    if (!completedSet.has(id)) {
                        completedSet.add(id);
                        saveCompleted(completedSet);
                    }

                    setButtonCompletedUI(btn, true);
                    updateProgressUI(loadCompleted());

                    alert('✅ Finished reading! Progress saved.');
                });
            });
        });
    </script>
</body>
</html>