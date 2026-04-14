<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BruSave') }} - @yield('title', 'Financial Literacy App')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --green: #2F5D46;
            --gold: #D8A24A;
            --bg: #F6F1E6;
            --card: #FFFBF2;
        }
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
    </style>
</head>

<body>
    @php
        $GREEN = '#2F5D46';
        $GOLD = '#D8A24A';
        
        $nav = [
            ['key' => 'dashboard', 'href' => route('dashboard'), 'icon' => '🏠', 'label' => 'Dashboard'],
            ['key' => 'learn', 'href' => route('learn'), 'icon' => '📖', 'label' => 'Learn'],
            ['key' => 'quiz', 'href' => route('quiz.index'), 'icon' => '❓', 'label' => 'Quiz'],
            ['key' => 'spending', 'href' => route('spending'), 'icon' => '🧾', 'label' => 'Track Spending'],
            ['key' => 'progress', 'href' => route('progress'), 'icon' => '🏆', 'label' => 'Achievement'],
            ['key' => 'town', 'href' => route('town'), 'icon' => '🏘️', 'label' => 'Town'],
        ];
        
        $active = request()->route()->getName();
        if (str_starts_with($active, 'quiz.')) $active = 'quiz';
        if ($active === 'town') $active = 'town';
        if ($active === 'spending') $active = 'spending';
        if ($active === 'progress') $active = 'progress';
    @endphp

    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            @include('partials.sidebar', ['nav' => $nav, 'active' => $active, 'GREEN' => $GREEN, 'GOLD' => $GOLD])
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Profile dropdown in top right -->
            <div style="display: flex; justify-content: flex-end; padding: 20px 24px 0;">
                @include('components.profile-dropdown', ['user' => auth()->user()])
            </div>
            
            <div style="max-width:1200px; margin:0 auto; padding:32px 24px;">
                @yield('content')
            </div>
            
            <footer class="text-center text-xs pb-8" style="color: rgba(47,93,70,0.75);">
                © {{ date('Y') }} Bru<i>Save</i>
            </footer>
        </div>
    </div>
    
    {{-- Background Music (plays on all pages, ON by default) --}}
    <audio id="bgMusic" loop preload="auto" style="display:none;">
        <source src="{{ asset('music/days-off-matrika-main-version-39449-02-56.mp3') }}" type="audio/mpeg">
    </audio>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bgMusic = document.getElementById('bgMusic');
            bgMusic.volume = 0.25; // 25% volume - subtle background
            
            // 🔴 DEFAULT TO ON for new users
            let musicEnabled = localStorage.getItem('musicEnabled');
            if (musicEnabled === null) {
                // First time user - default to ON
                musicEnabled = 'true';
                localStorage.setItem('musicEnabled', 'true');
            }
            const isEnabled = musicEnabled === 'true';
            
            if (isEnabled) {
                bgMusic.play().catch(e => {
                    console.log('Autoplay prevented - waiting for user interaction');
                    // Try to play on first user interaction
                    const playOnInteraction = () => {
                        if (localStorage.getItem('musicEnabled') === 'true' && bgMusic.paused) {
                            bgMusic.play();
                        }
                        document.removeEventListener('click', playOnInteraction);
                        document.removeEventListener('keydown', playOnInteraction);
                    };
                    document.addEventListener('click', playOnInteraction);
                    document.addEventListener('keydown', playOnInteraction);
                });
            }
            
            // Listen for toggle changes from settings page
            window.addEventListener('storage', function(e) {
                if (e.key === 'musicEnabled') {
                    const isEnabled = e.newValue === 'true';
                    if (isEnabled) {
                        bgMusic.play();
                    } else {
                        bgMusic.pause();
                    }
                }
            });
        });
    </script>
</body>
</html>