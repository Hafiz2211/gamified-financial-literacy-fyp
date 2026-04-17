<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
    <title>My Room • BruSave</title>

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
        
        .pixel img {
            image-rendering: pixelated;
            image-rendering: crisp-edges;
            user-select: none;
            -webkit-user-drag: none;
            display: block;
            width: 100%;
            height: auto;
        }
        
        .room-container {
            position: relative;
            width: 980px;
            height: 520px;
            margin: 0 auto;
            background: #2F5D46;
            overflow: hidden;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .furniture-item {
            position: absolute;
            display: none;
            cursor: move;
            user-select: none;
            transition: filter 0.2s ease, transform 0.1s ease;
            z-index: 10;
        }
        
        .furniture-item.owned {
            display: block;
        }
        
        .furniture-item.dragging {
            opacity: 0.8;
            transform: scale(1.05);
            z-index: 1000;
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));
        }
        
        .furniture-item img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            pointer-events: none;
            image-rendering: pixelated;
            max-width: 150px;  
            max-height: 150px; 
        }

        .furniture-item.small img {
            max-width: 100px;
            max-height: 100px;
        }
        .furniture-item.medium img {
            max-width: 150px;
            max-height: 150px;
        }
        .furniture-item.large img {
            max-width: 250px;
            max-height: 250px;
        }

        /* 🧚 Wisp Pet Styles */
        .wisp-pet {
            position: absolute;
            cursor: pointer;
            transition: left 0.3s ease-out, top 0.3s ease-out;
            z-index: 200;
            filter: drop-shadow(0 0 8px rgba(216,162,74,0.6));
            pointer-events: auto;
        }
        
        .wisp-pet img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            pointer-events: none;
        }
        
        .wisp-pet.following {
            transition: left 0.1s ease-out, top 0.1s ease-out;
        }
        
        @keyframes floatWisp {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        .wisp-pet {
            animation: floatWisp 3s ease-in-out infinite;
        }

        .tooltip-tag {
            position: absolute;
            left: 50%;
            top: -25px;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.8);
            color: white;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 16px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 1001;
            border: 1px solid #D8A24A;
        }
        
        .shop-item {
            transition: all 0.2s ease;
            cursor: pointer;
            border-radius: 16px;
            padding: 16px;
        }
        
        .shop-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(216,162,74,0.3);
        }
        
        .shop-item.locked {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .shop-item.locked:hover {
            transform: none;
            box-shadow: none;
        }
        
        .shop-item.owned {
            opacity: 0.9;
            cursor: default;
            background: rgba(47,93,70,0.1) !important;
            border-color: #2F5D46 !important;
        }
        
        .shop-item.owned:hover {
            transform: none;
            box-shadow: none;
        }
        
        .shop-preview {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        
        .shop-preview img {
            max-height: 70px;
            width: auto;
            object-fit: contain;
            image-rendering: pixelated;
            transition: filter 0.2s ease;
        }
        
        .shop-item.locked .shop-preview img {
            filter: grayscale(100%) opacity(0.5);
        }
        
        .grid-guide {
            position: absolute;
            inset: 0;
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            grid-template-rows: repeat(8, 1fr);
            pointer-events: none;
            z-index: 5;
            opacity: 0.3;
            display: none;
        }
        
        .grid-cell {
            border: 1px dashed #D8A24A;
            background: rgba(216,162,74,0.05);
        }
        
        .grid-guide.visible {
            display: grid;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .level-up-notification {
            animation: slideIn 0.3s ease-out;
        }
        
        .section-header {
            font-size: 14px;
            font-weight: 700;
            margin: 16px 0 12px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid rgba(216,162,74,0.3);
        }
        
        /* Mobile Warning - only shows on phones */
        .desktop-only-banner {
            display: none;
        }
        
        /* ========== MOBILE RESPONSIVE CSS ========== */
        @media (max-width: 768px) {
            .desktop-only-banner {
                display: block;
            }
            
            .room-container {
                width: 100%;
                height: auto;
                aspect-ratio: 980 / 520;
                transform: scale(0.95);
            }
            
            .furniture-item img {
                max-width: 45px !important;
                max-height: 45px !important;
            }
            
            .furniture-item.small img {
                max-width: 30px !important;
                max-height: 30px !important;
            }
            
            .furniture-item.medium img {
                max-width: 40px !important;
                max-height: 40px !important;
            }
            
            .furniture-item.large img {
                max-width: 55px !important;
                max-height: 55px !important;
            }
            
            .wisp-pet {
                width: 50px !important;
                height: 50px !important;
            }
            
            .shop-preview img {
                max-height: 35px;
            }
            
            .grid-cols-1.md\:grid-cols-5 {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 8px !important;
            }
            
            .shop-item {
                padding: 8px;
            }
            
            .shop-item .font-bold.text-sm {
                font-size: 11px;
            }
            
            .shop-item .text-xs {
                font-size: 9px;
            }
            
            .shop-item .text-lg.font-bold {
                font-size: 14px;
            }
            
            .text-2xl.font-extrabold {
                font-size: 1.25rem;
            }
            
            .flex.gap-6 {
                gap: 12px;
            }
            
            .p-6 {
                padding: 1rem;
            }
            
            .section-header {
                font-size: 11px;
            }
            
            .text-sm {
                font-size: 10px;
            }
            
            .rounded-3xl {
                border-radius: 16px;
            }
        }
        
        @media (max-width: 480px) {
            .room-container {
                transform: scale(0.9);
            }
            
            .furniture-item img {
                max-width: 35px !important;
                max-height: 35px !important;
            }
            
            .wisp-pet {
                width: 40px !important;
                height: 40px !important;
            }
            
            .grid-cols-1.md\:grid-cols-5 {
                grid-template-columns: repeat(2, 1fr) !important;
            }
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
    $GOLD = '#D8A24A';
    $BG = '#F6F1E6';
    $CARD = '#FFFBF2';
    $active = 'progress';
    $isPremium = $user->isPremium();
    
    $nav = [
        ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard','icon'=>'🏠'],
        ['key'=>'learn','label'=>'Learn','href'=>'/learn','icon'=>'📖'],
        ['key'=>'quiz','label'=>'Quiz','href'=>'/quiz','icon'=>'❓'],
        ['key'=>'track','label'=>'Track Spending','href'=>'/track-spending','icon'=>'🧾'],
        ['key'=>'progress','label'=>'Achievement','href'=>'/progress','icon'=>'🏆'],
        ['key'=>'town','label'=>'Town','href'=>'/town','icon'=>'🏘️'],
    ];

    // Get REAL user data from database
    $userName = $user->name ?? 'Hafiz';
    $level = $user->level ?? 1;
    $coins = $user->coins ?? 0;
    $xp = $user->xp ?? 0;
    
    // XP thresholds
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

    // Calculate XP progress
    $currentLevelThreshold = $levelThresholds[$level];
    if ($level < 10) {
        $nextLevelThreshold = $levelThresholds[$level + 1];
        $xpNeededForNextLevel = $nextLevelThreshold - $currentLevelThreshold;
        $xpInCurrentLevel = $xp - $currentLevelThreshold;
        $nextLevel = $level + 1;
    } else {
        $xpNeededForNextLevel = 0;
        $xpInCurrentLevel = $xp - $currentLevelThreshold;
        $nextLevel = $level;
    }
    $xpProgress = $xpNeededForNextLevel > 0 ? ($xpInCurrentLevel / $xpNeededForNextLevel) * 100 : 100;
    $xpProgress = min(100, max(0, $xpProgress));
    $xpDisplay = $xpInCurrentLevel . '/' . $xpNeededForNextLevel . ' XP to Level ' . $nextLevel;
    
    // Title based on level
    if ($level <= 2) {
        $title = 'New Mayor';
    } elseif ($level <= 4) {
        $title = 'Rising Mayor';
    } elseif ($level <= 6) {
        $title = 'Great Mayor';
    } else {
        $title = 'Legendary Mayor';
    }
    
    // Get user stats from database with correct limits
    $totalLessons = \App\Models\Lesson::count();
    $completedLessons = $user->lessons()->count() ?? 0;
    
    // Premium lessons completed - auto detect from database
    $premiumLessonIds = \App\Models\Lesson::where('is_premium', true)->pluck('id')->toArray();
    $completedPremiumLessons = $user->lessons()->whereIn('lesson_id', $premiumLessonIds)->count() ?? 0;
    
    $totalQuizzes = \App\Models\Quiz::count();
    $passedQuizzes = $user->quizAttempts()
        ->where('passed', true)
        ->distinct('quiz_id')
        ->count('quiz_id') ?? 0;
    
    // Premium quizzes passed - auto detect from database
    $premiumQuizIds = \App\Models\Quiz::where('is_premium', true)->pluck('id')->toArray();
    $passedPremiumQuizzes = $user->quizAttempts()
        ->where('passed', true)
        ->whereIn('quiz_id', $premiumQuizIds)
        ->distinct('quiz_id')
        ->count('quiz_id') ?? 0;
    
    $spendingRecords = $user->transactions()->count() ?? 0;
    
    // 🎯 FURNITURE SYSTEM
    
    // 🟢 FREE (Starter Room)
    $freeFurniture = [
        'plant' => [
            'name' => 'Potted Plant',
            'image' => asset('images/room/plant.png'),
            'condition' => $completedLessons >= 1,
            'requirement' => 'Complete 1 lesson',
            'price' => 40,
            'unlocked' => $completedLessons >= 1,
            'category' => 'free',
            'size' => 'small',
            'defaultPos' => ['left' => 60, 'top' => 350, 'width' => 80],
            'z' => 10
        ],
        'chair' => [
            'name' => 'Study Chair',
            'image' => asset('images/room/chair.png'),
            'condition' => $spendingRecords >= 5,
            'requirement' => 'Add 5 spending records',
            'price' => 60,
            'unlocked' => $spendingRecords >= 5,
            'category' => 'free',
            'size' => 'small',
            'defaultPos' => ['left' => 450, 'top' => 280, 'width' => 120],
            'z' => 30
        ],
        'picture' => [
            'name' => 'Wall Picture',
            'image' => asset('images/room/picture.png'),
            'condition' => $level >= 2,
            'requirement' => 'Reach Level 2',
            'price' => 80,
            'unlocked' => $level >= 2,
            'category' => 'free',
            'size' => 'small',
            'defaultPos' => ['left' => 800, 'top' => 80, 'width' => 60],
            'z' => 15
        ],
        'desk' => [
            'name' => 'Study Desk',
            'image' => asset('images/room/desk.png'),
            'condition' => $passedQuizzes >= 1,
            'requirement' => 'Pass 1 quiz',
            'price' => 120,
            'unlocked' => $passedQuizzes >= 1,
            'category' => 'free',
            'size' => 'medium',
            'defaultPos' => ['left' => 600, 'top' => 150, 'width' => 200],
            'z' => 25
        ],
        'bed' => [
            'name' => 'Comfy Bed',
            'image' => asset('images/room/bed.png'),
            'condition' => $level >= 3,
            'requirement' => 'Reach Level 3',
            'price' => 180,
            'unlocked' => $level >= 3,
            'category' => 'free',
            'size' => 'medium',
            'defaultPos' => ['left' => 700, 'top' => 300, 'width' => 220],
            'z' => 35
        ],
    ];
    
    // 🔴 MERGED PREMIUM FURNITURE (All 8 premium items in ONE section)
    $premiumFurnitureMerged = [
        'window' => [
            'name' => 'Window',
            'image' => asset('images/room/window.png'),
            'condition' => $isPremium,
            'requirement' => 'Premium member',
            'price' => 200,
            'unlocked' => $isPremium,
            'category' => 'premium',
            'size' => 'medium',
            'defaultPos' => ['left' => 300, 'top' => 50, 'width' => 150],
            'z' => 5
        ],
        'clock' => [
            'name' => 'Wall Clock',
            'image' => asset('images/room/clock.png'),
            'condition' => $isPremium,
            'requirement' => 'Premium member',
            'price' => 120,
            'unlocked' => $isPremium,
            'category' => 'premium',
            'size' => 'small',
            'defaultPos' => ['left' => 500, 'top' => 60, 'width' => 60],
            'z' => 15
        ],
        'bookcase' => [
            'name' => 'Bookcase',
            'image' => asset('images/room/bookcase.png'),
            'condition' => $completedPremiumLessons >= 2 && $isPremium,
            'requirement' => 'Complete 2 premium lessons',
            'price' => 220,
            'unlocked' => $completedPremiumLessons >= 2 && $isPremium,
            'category' => 'premium',
            'size' => 'large',
            'defaultPos' => ['left' => 50, 'top' => 150, 'width' => 120],
            'z' => 20
        ],
        'laptop' => [
            'name' => 'Laptop',
            'image' => asset('images/room/laptop.png'),
            'condition' => $passedPremiumQuizzes >= 1 && $isPremium,
            'requirement' => 'Pass 1 premium quiz',
            'price' => 250,
            'unlocked' => $passedPremiumQuizzes >= 1 && $isPremium,
            'category' => 'premium',
            'size' => 'small',
            'defaultPos' => ['left' => 650, 'top' => 220, 'width' => 80],
            'z' => 40
        ],
        'carpet' => [
            'name' => 'Carpet',
            'image' => asset('images/room/carpet.png'),
            'condition' => $spendingRecords >= 30 && $isPremium,
            'requirement' => 'Track 30 spending records + Premium',
            'price' => 150,
            'unlocked' => $spendingRecords >= 30 && $isPremium,
            'category' => 'premium',
            'size' => 'large',
            'defaultPos' => ['left' => 300, 'top' => 350, 'width' => 300],
            'z' => 1
        ],
        'lamp' => [
            'name' => 'Stand Lamp',
            'image' => asset('images/room/bedroom lamp.png'),
            'condition' => $level >= 5 && $isPremium,
            'requirement' => 'Reach Level 5 + Premium',
            'price' => 180,
            'unlocked' => $level >= 5 && $isPremium,
            'category' => 'premium',
            'size' => 'small',
            'defaultPos' => ['left' => 850, 'top' => 250, 'width' => 50],
            'z' => 20
        ],
        'drawer' => [
            'name' => 'Drawer',
            'image' => asset('images/room/drawer.png'),
            'condition' => $passedPremiumQuizzes >= 3 && $isPremium,
            'requirement' => 'Pass ALL premium quizzes',
            'price' => 160,
            'unlocked' => $passedPremiumQuizzes >= 3 && $isPremium,
            'category' => 'premium',
            'size' => 'medium',
            'defaultPos' => ['left' => 200, 'top' => 380, 'width' => 100],
            'z' => 20
        ],
        'wardrobe' => [
            'name' => 'Wardrobe',
            'image' => asset('images/room/wardrobe.png'),
            'condition' => $completedPremiumLessons >= 6 && $isPremium,
            'requirement' => 'Complete ALL premium lessons',
            'price' => 280,
            'unlocked' => $completedPremiumLessons >= 6 && $isPremium,
            'category' => 'premium',
            'size' => 'large',
            'defaultPos' => ['left' => 100, 'top' => 80, 'width' => 200],
            'z' => 20
        ],
    ];
    
    // 🧚 WISP PET (Special item)
    $wispPet = [
        'wisp' => [
            'name' => 'Blue Wisp',
            'image' => asset('images/room/wisp.gif'),
            'condition' => $level >= 3,
            'requirement' => 'Reach Level 3',
            'price' => 200,
            'unlocked' => $level >= 3,
            'category' => 'pet',
            'size' => 'small',
            'defaultPos' => ['left' => 460, 'top' => 240, 'width' => 60],
            'z' => 200,
            'isPet' => true
        ],
    ];
    
    // Merge all furniture
    $allFurniture = array_merge($freeFurniture, $premiumFurnitureMerged, $wispPet);
@endphp

<div class="app-container">
    @include('partials.sidebar', ['nav' => $nav, 'active' => $active, 'GREEN' => $GREEN, 'GOLD' => $GOLD])

    <div class="main-content">
        <div style="display: flex; justify-content: flex-end; padding: 20px 24px 0;">
            @include('components.profile-dropdown', ['user' => auth()->user()])
        </div>
        
        <div style="max-width:1200px; margin:0 auto; padding:32px 24px;">
            
            {{-- TOP BAR --}}
            <section class="rounded-3xl border shadow-lg p-6"
                     style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-2xl font-extrabold" style="color:{{ $GREEN }};">
                            {{ $userName }}'s Home
                        </div>
                         <p class="mt-2 text-sm" style="color: rgba(47,93,70,0.8);">
                            Unlock furniture by completing lessons, quizzes and tracking your spending to showcase your achievements.
                         </p>
                    </div>

                    <div class="flex gap-6">
                        <div class="text-right">
                            <div class="text-sm font-semibold" style="color: rgba(47,93,70,0.65);">Level</div>
                            <div class="text-2xl font-bold" style="color:{{ $GREEN }};" id="levelDisplay">{{ $level }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold" style="color: rgba(47,93,70,0.65);">Coins</div>
                            <div class="text-2xl font-bold" style="color:{{ $GOLD }};" id="coinDisplay">{{ $coins }}</div>
                        </div>
                    </div>
                </div>

                {{-- XP Bar --}}
                <div class="mt-4">
                    <div class="flex justify-between text-xs mb-1" style="color: rgba(47,93,70,0.65);">
                        <span>Level {{ $level }}</span>
                        <span>{{ $xpDisplay }}</span>
                    </div>
                    <div class="h-2.5 rounded-full overflow-hidden" style="background: rgba(47,93,70,0.18);">
                        <div class="h-full rounded-full" style="width: {{ $xpProgress }}%; background:{{ $GOLD }};"></div>
                    </div>
                </div>
            </section>

            {{-- QUICK STATS --}}
            <section class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div class="rounded-2xl border p-4" style="background:{{ $CARD }};">
                    <div class="text-xs" style="color: rgba(47,93,70,0.65);">📚 Lessons</div>
                    <div class="text-xl font-bold" style="color:{{ $GREEN }};">{{ $completedLessons }}/{{ $totalLessons }}</div>
                    @if($completedPremiumLessons > 0)
                        <div class="text-xs" style="color:{{ $GOLD }};">({{ $completedPremiumLessons }}/6 premium)</div>
                    @endif
                </div>
                <div class="rounded-2xl border p-4" style="background:{{ $CARD }};">
                    <div class="text-xs" style="color: rgba(47,93,70,0.65);">📝 Quizzes Passed</div>
                    <div class="text-xl font-bold" style="color:{{ $GREEN }};">{{ $passedQuizzes }}/{{ $totalQuizzes }}</div>
                    @if($passedPremiumQuizzes > 0)
                        <div class="text-xs" style="color:{{ $GOLD }};">({{ $passedPremiumQuizzes }}/3 premium)</div>
                    @endif
                </div>
                <div class="rounded-2xl border p-4" style="background:{{ $CARD }};">
                    <div class="text-xs" style="color: rgba(47,93,70,0.65);">💰 Spending Records</div>
                    <div class="text-xl font-bold" style="color:{{ $GREEN }};">{{ $spendingRecords }}</div>
                </div>
                <div class="rounded-2xl border p-4" style="background:{{ $CARD }};">
                    <div class="text-xs" style="color: rgba(47,93,70,0.65);">🏆 Title</div>
                    <div class="text-sm font-bold truncate" style="color:{{ $GOLD }};">{{ $title }}</div>
                </div>
            </section>

            {{-- 🔴 MOBILE WARNING BANNER (Only visible on phones) --}}
            <div class="desktop-only-banner mb-3 p-3 rounded-xl text-center" style="background: rgba(216,162,74,0.12); border: 1px solid {{ $GOLD }};">
                <p style="color: {{ $GOLD }}; font-size: 12px; margin: 0;">
                    💡 <strong>Tip:</strong> For the best room decorating experience, use a <strong>laptop or desktop</strong>. 
                    Drag and drop works best with a mouse. Mobile version will be implemented in a future update.
                </p>
            </div>

            {{-- 🔴 COMING SOON BANNER (Above Furniture Shop) --}}
            <div class="p-3 rounded-xl text-center" style="background: rgba(47,93,70,0.08); border: 1px dashed {{ $GOLD }}; margin-bottom: 25px;">
                <p style="color: {{ $GREEN }}; font-size: 13px; margin: 0;">
                    🏠 <strong>Coming Soon!</strong> Kitchen, Pets and Living Room decorations will be available in a future update. Stay tuned! ✨
                </p>
            </div>

            {{-- FURNITURE SHOP WITH SECTIONS --}}
            <section class="rounded-3xl border shadow-lg p-6" style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16); margin-top: 20px;">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold" style="color:{{ $GREEN }};">🛒 Furniture Shop</h2>
                    <div class="text-sm" style="color: rgba(47,93,70,0.7);">
                        Press <kbd style="background: #2F5D46; color: #D8A24A; padding: 2px 6px; border-radius: 4px;">G</kbd> for grid
                    </div>
                </div>
                
                {{-- 🟢 FREE SECTION --}}
                <div class="section-header" style="color:{{ $GREEN }};">🟢 Free Version Furniture</div>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                    @foreach($freeFurniture as $key => $item)
                        <div class="shop-item border text-center" 
                             data-id="{{ $key }}"
                             data-price="{{ $item['price'] }}"
                             data-name="{{ $item['name'] }}"
                             data-unlocked="{{ $item['condition'] ? 'true' : 'false' }}"
                             style="border-color: {{ $item['condition'] ? 'rgba(216,162,74,0.4)' : 'rgba(47,93,70,0.16)' }};
                                    background: {{ $item['condition'] ? 'rgba(216,162,74,0.05)' : 'rgba(47,93,70,0.02)' }};">
                            
                            <div class="shop-preview">
                                <img src="{{ $item['image'] }}" 
                                     alt="{{ $item['name'] }}"
                                     style="{{ !$item['condition'] ? 'filter: grayscale(100%) opacity(0.5);' : '' }}">
                            </div>
                            
                            <div class="font-bold text-sm" style="color:{{ $GREEN }};">{{ $item['name'] }}</div>
                            <div class="text-xs mt-1" style="color: rgba(47,93,70,0.65);">{{ $item['requirement'] }}</div>
                            <div class="text-lg font-bold mt-2" style="color:{{ $GOLD }};">{{ $item['price'] }} 🪙</div>
                            <div class="text-xs mt-2 requirement-text">
                                @if(!$item['condition'])
                                    <span style="color: #b43c3c;">🔒 Locked</span>
                                @elseif($coins < $item['price'])
                                    <span style="color: {{ $GOLD }};">💰 Need coins</span>
                                @else
                                    <span style="color: {{ $GREEN }};">✅ Buy</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- 🟡 PREMIUM FURNITURE (ALL 8 items together) --}}
                <div class="section-header" style="color:{{ $GOLD }};">🟡 Premium Version Furniture</div>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                    @foreach($premiumFurnitureMerged as $key => $item)
                        @php 
                            $isUnlocked = $item['unlocked'];
                            $requirementText = $item['requirement'];
                        @endphp
                        <div class="shop-item border text-center" 
                             data-id="{{ $key }}"
                             data-price="{{ $item['price'] }}"
                             data-name="{{ $item['name'] }}"
                             data-unlocked="{{ $isUnlocked ? 'true' : 'false' }}"
                             style="border-color: {{ $isUnlocked ? 'rgba(216,162,74,0.4)' : 'rgba(47,93,70,0.16)' }};
                                    background: {{ $isUnlocked ? 'rgba(216,162,74,0.05)' : 'rgba(47,93,70,0.02)' }};">
                            
                            <div class="shop-preview">
                                <img src="{{ $item['image'] }}" 
                                     alt="{{ $item['name'] }}"
                                     style="{{ !$isUnlocked ? 'filter: grayscale(100%) opacity(0.5);' : '' }}">
                            </div>
                            
                            <div class="font-bold text-sm" style="color:{{ $GREEN }};">{{ $item['name'] }}</div>
                            <div class="text-xs mt-1" style="color: rgba(47,93,70,0.65);">{{ $requirementText }}</div>
                            <div class="text-lg font-bold mt-2" style="color:{{ $GOLD }};">{{ $item['price'] }} 🪙</div>
                            <div class="text-xs mt-2 requirement-text">
                                @if(!$isUnlocked)
                                    <span style="color: #b43c3c;">🔒 Locked</span>
                                @elseif($coins < $item['price'])
                                    <span style="color: {{ $GOLD }};">💰 Need coins</span>
                                @else
                                    <span style="color: {{ $GREEN }};">✅ Buy</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- 🧚 PET SECTION --}}
                <div class="section-header mt-4" style="color:{{ $GREEN }};">🐾 Pets</div>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @foreach($wispPet as $key => $item)
                        <div class="shop-item border text-center" 
                             data-id="{{ $key }}"
                             data-price="{{ $item['price'] }}"
                             data-name="{{ $item['name'] }}"
                             data-unlocked="{{ $item['condition'] ? 'true' : 'false' }}"
                             data-is-pet="true"
                             style="border-color: {{ $item['condition'] ? 'rgba(216,162,74,0.4)' : 'rgba(47,93,70,0.16)' }};
                                    background: {{ $item['condition'] ? 'rgba(216,162,74,0.05)' : 'rgba(47,93,70,0.02)' }};">
                            
                            <div class="shop-preview">
                                <img src="{{ $item['image'] }}" 
                                     alt="{{ $item['name'] }}"
                                     style="{{ !$item['condition'] ? 'filter: grayscale(100%) opacity(0.5);' : '' }}">
                            </div>
                            
                            <div class="font-bold text-sm" style="color:{{ $GREEN }};">{{ $item['name'] }}</div>
                            <div class="text-xs mt-1" style="color: rgba(47,93,70,0.65);">{{ $item['requirement'] }}</div>
                            <div class="text-lg font-bold mt-2" style="color:{{ $GOLD }};">{{ $item['price'] }} 🪙</div>
                            <div class="text-xs mt-2 requirement-text">
                                @if(!$item['condition'])
                                    <span style="color: #b43c3c;">🔒 Level 3 Required</span>
                                @elseif($coins < $item['price'])
                                    <span style="color: {{ $GOLD }};">💰 Need coins</span>
                                @else
                                    <span style="color: {{ $GREEN }};">✅ Adopt</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- ROOM CANVAS --}}
            <section class="mt-8 rounded-3xl border shadow-lg overflow-hidden"
                     style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">

                <div class="p-5 border-b flex justify-between items-center" style="border-color: rgba(47,93,70,0.12);">
                    <div>
                        <div class="font-extrabold text-lg" style="color:{{ $GREEN }};">Your Room</div>
                        <div class="text-sm" style="color: rgba(47,93,70,0.75);">
                            <span id="dragInstruction">Your room is empty! Buy furniture from the shop above.</span>
                        </div>
                    </div>
                    <div class="text-sm px-3 py-1 rounded-full" style="background: rgba(216,162,74,0.15); color:{{ $GOLD }};">
                        <span id="coinDisplay2">{{ $coins }}</span> 🪙
                    </div>
                </div>

                <div class="room-container" id="roomContainer">
                    <img src="{{ asset('images/room/room.png') }}" alt="Empty room" class="absolute inset-0 w-full h-full object-cover pointer-events-none select-none" onerror="this.style.display='none';">
                    <div id="gridGuide" class="grid-guide">
                        @for($i = 0; $i < 96; $i++)
                            <div class="grid-cell"></div>
                        @endfor
                    </div>
                    <div id="furnitureLayer" class="absolute inset-0"></div>
                    <div id="wispLayer" class="absolute inset-0 pointer-events-none"></div>
                </div>
            </section>

            <footer class="text-center text-xs pt-8 pb-2" style="color: rgba(47,93,70,0.75);">
                © {{ date('Y') }} Bru<i>Save</i> &nbsp;|&nbsp;
                Pixel World assets by <a href="https://bitglow.itch.io/pixel-world-complete-pack-pixel-art-assets" target="_blank" style="color: {{ $GOLD }}; text-decoration: none;">bitglow (itch.io)</a> &nbsp;|&nbsp;
                Glowing Ball sprite by <a href="https://lvgames.itch.io/free-glowing-ball-sprite-pixel-fx-rpg-maker-ready" target="_blank" style="color: {{ $GOLD }}; text-decoration: none;">LVGames</a>
            </footer>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const userId = {{ $user->id }};
    let coins = Number(@json($coins));
    const isPremium = @json($isPremium);
    
    // Load from DATABASE
    let owned = new Set();
    let savedPositions = {};
    let wispElement = null;
    let wispFollowing = false;
    let wispMoveInterval = null;
    
    // Load from database via API
    async function loadFromDatabase() {
        try {
            const response = await fetch('/furniture/load');
            const data = await response.json();
            owned = new Set(data.owned || []);
            // FIX: Ensure positions is an object, not array
            if (data.positions && Array.isArray(data.positions)) {
                savedPositions = {};
            } else {
                savedPositions = data.positions || {};
            }
            render();
            updateShopItems();
            renderWisp();
        } catch (error) {
            console.error('Error loading furniture:', error);
            loadFromLocalStorage();
        }
    }
    
    // Fallback to localStorage
    function loadFromLocalStorage() {
        const STORAGE_OWNED = `brusave.room.owned.${userId}`;
        const STORAGE_POSITIONS = `brusave.room.positions.${userId}`;
        try {
            const raw = localStorage.getItem(STORAGE_OWNED);
            owned = raw ? new Set(JSON.parse(raw)) : new Set();
            const rawPos = localStorage.getItem(STORAGE_POSITIONS);
            savedPositions = rawPos ? JSON.parse(rawPos) : {};
            render();
            updateShopItems();
            renderWisp();
        } catch (e) {
            owned = new Set();
            savedPositions = {};
        }
    }
    
    // Save to database
    async function saveToDatabase() {
        try {
            const response = await fetch('/furniture/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    owned: [...owned],
                    positions: savedPositions
                })
            });
            const result = await response.json();
            if (result.success) {
                console.log('✅ Positions saved to database');
            } else {
                console.error('Save failed:', result);
                saveToLocalStorage();
            }
        } catch (error) {
            console.error('Error saving to database:', error);
            saveToLocalStorage();
        }
    }
    
    function saveToLocalStorage() {
        const STORAGE_OWNED = `brusave.room.owned.${userId}`;
        const STORAGE_POSITIONS = `brusave.room.positions.${userId}`;
        localStorage.setItem(STORAGE_OWNED, JSON.stringify([...owned]));
        localStorage.setItem(STORAGE_POSITIONS, JSON.stringify(savedPositions));
    }
    
    // Save positions when leaving the page
    window.addEventListener('beforeunload', () => {
        if (Object.keys(savedPositions).length > 0 || owned.size > 0) {
            const data = JSON.stringify({
                owned: [...owned],
                positions: savedPositions
            });
            navigator.sendBeacon('/furniture/save', data);
        }
    });
    
    // DOM elements
    const coinDisplay = document.getElementById('coinDisplay');
    const coinDisplay2 = document.getElementById('coinDisplay2');
    const layer = document.getElementById('furnitureLayer');
    const wispLayer = document.getElementById('wispLayer');
    const gridGuide = document.getElementById('gridGuide');
    const roomContainer = document.getElementById('roomContainer');
    
    // Update shop items to show owned status
    const updateShopItems = () => {
        document.querySelectorAll('.shop-item').forEach(item => {
            const id = item.dataset.id;
            if (owned.has(id)) {
                item.classList.add('owned');
                const reqText = item.querySelector('.requirement-text');
                if (reqText) {
                    reqText.innerHTML = '<span style="color: #2F5D46;">✅ Owned</span>';
                }
            } else {
                item.classList.remove('owned');
            }
        });
    };
    
    // Update coin displays
    const updateCoinDisplay = () => {
        coinDisplay.textContent = coins;
        coinDisplay2.textContent = coins;
    };
    updateCoinDisplay();
    
    // Furniture definitions with default positions
    const items = @json($allFurniture);
    const itemsList = Object.entries(items).map(([id, data]) => ({
        id: id,
        name: data.name,
        src: data.image,
        price: data.price,
        size: data.size,
        defaultPos: data.defaultPos,
        z: data.z,
        isPet: data.isPet || false
    }));
    
    // 🧚 Wisp movement functions
    function startWispRandomMovement() {
        if (wispMoveInterval) clearInterval(wispMoveInterval);
        
        wispMoveInterval = setInterval(() => {
            if (!wispElement || wispFollowing) return;
            if (!owned.has('wisp')) return;
            
            const container = roomContainer;
            const maxX = container.clientWidth - wispElement.offsetWidth - 20;
            const maxY = container.clientHeight - wispElement.offsetHeight - 20;
            
            const randomX = Math.max(20, Math.min(maxX, Math.random() * maxX));
            const randomY = Math.max(20, Math.min(maxY, Math.random() * maxY));
            
            wispElement.style.left = randomX + 'px';
            wispElement.style.top = randomY + 'px';
            
            // Save position
            const pos = {
                left: randomX,
                top: randomY,
                width: parseInt(wispElement.style.width)
            };
            savedPositions['wisp'] = pos;
            saveToDatabase();
        }, 8000);
    }
    
    function startWispFollowing() {
        if (!wispElement || !owned.has('wisp')) return;
        
        wispFollowing = true;
        wispElement.classList.add('following');
        
        const onMouseMove = (e) => {
            if (!wispFollowing) return;
            const containerRect = roomContainer.getBoundingClientRect();
            const mouseX = e.clientX - containerRect.left;
            const mouseY = e.clientY - containerRect.top;
            
            const maxX = roomContainer.clientWidth - wispElement.offsetWidth - 10;
            const maxY = roomContainer.clientHeight - wispElement.offsetHeight - 10;
            
            let targetX = Math.max(10, Math.min(maxX, mouseX - 25));
            let targetY = Math.max(10, Math.min(maxY, mouseY - 25));
            
            wispElement.style.left = targetX + 'px';
            wispElement.style.top = targetY + 'px';
        };
        
        const onMouseLeave = () => {
            if (wispFollowing) {
                wispFollowing = false;
                wispElement.classList.remove('following');
                document.removeEventListener('mousemove', onMouseMove);
                roomContainer.removeEventListener('mouseleave', onMouseLeave);
                startWispRandomMovement();
                
                // Save final position
                const pos = {
                    left: parseInt(wispElement.style.left),
                    top: parseInt(wispElement.style.top),
                    width: parseInt(wispElement.style.width)
                };
                savedPositions['wisp'] = pos;
                saveToDatabase();
            }
        };
        
        document.addEventListener('mousemove', onMouseMove);
        roomContainer.addEventListener('mouseleave', onMouseLeave);
        
        if (wispMoveInterval) clearInterval(wispMoveInterval);
    }
    
    function renderWisp() {
        if (!wispLayer) return;
        
        wispLayer.innerHTML = '';
        
        if (!owned.has('wisp')) return;
        
        const pos = savedPositions['wisp'] || { left: 460, top: 240, width: 60 };
        
        const wispDiv = document.createElement('div');
        wispDiv.className = 'wisp-pet';
        wispDiv.style.left = pos.left + 'px';
        wispDiv.style.top = pos.top + 'px';
        wispDiv.style.width = pos.width + 'px';
        wispDiv.style.height = pos.width + 'px';
        
        const img = document.createElement('img');
        img.src = "{{ asset('images/room/wisp.gif') }}";
        img.alt = "Blue Wisp";
        
        wispDiv.appendChild(img);
        wispLayer.appendChild(wispDiv);
        
        wispElement = wispDiv;
        
        // Add click to follow
        wispDiv.addEventListener('click', (e) => {
            e.stopPropagation();
            if (wispFollowing) {
                wispFollowing = false;
                wispElement.classList.remove('following');
                document.removeEventListener('mousemove', () => {});
                startWispRandomMovement();
            } else {
                startWispFollowing();
            }
        });
        
        startWispRandomMovement();
    }
    
    // Drag and drop
    function makeDraggable(element, itemId) {
        let isDragging = false;
        let startX, startY, startLeft, startTop;
        
        const onMouseMove = (e) => {
            if (!isDragging) return;
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            const newLeft = Math.max(0, Math.min(980 - element.offsetWidth, startLeft + dx));
            const newTop = Math.max(0, Math.min(520 - element.offsetHeight, startTop + dy));
            element.style.left = newLeft + 'px';
            element.style.top = newTop + 'px';
        };
        
        const onMouseUp = () => {
            if (isDragging) {
                isDragging = false;
                element.classList.remove('dragging');
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
                const pos = {
                    left: parseInt(element.style.left),
                    top: parseInt(element.style.top),
                    width: parseInt(element.style.width)
                };
                savedPositions[itemId] = pos;
                saveToDatabase();
            }
        };
        
        element.addEventListener('mousedown', (e) => {
            if (!owned.has(itemId)) return;
            e.preventDefault();
            isDragging = true;
            element.classList.add('dragging');
            startX = e.clientX;
            startY = e.clientY;
            startLeft = parseInt(element.style.left) || 0;
            startTop = parseInt(element.style.top) || 0;
            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        });
    }
    
    // Render furniture
    function render() {
        layer.innerHTML = '';
        
        console.log('Rendering with positions:', savedPositions);
        
        itemsList.forEach(item => {
            if (!owned.has(item.id)) return;
            if (item.isPet) return; // Skip wisp, handled separately
            
            let pos = savedPositions[item.id];
            if (!pos || typeof pos !== 'object') {
                pos = item.defaultPos;
            }
            if (!pos.left) pos.left = item.defaultPos.left;
            if (!pos.top) pos.top = item.defaultPos.top;
            if (!pos.width) pos.width = item.defaultPos.width;
            
            console.log('Rendering', item.id, 'at', pos);
            
            const wrap = document.createElement('div');
            wrap.className = 'furniture-item owned';
            if (item.size) wrap.classList.add(item.size);
            wrap.style.left = pos.left + 'px';
            wrap.style.top = pos.top + 'px';
            wrap.style.width = pos.width + 'px';
            wrap.style.height = 'auto';
            wrap.style.zIndex = String(100 + (item.z ?? 20));
            wrap.setAttribute('data-id', item.id);
            
            const img = document.createElement('img');
            img.src = item.src;
            img.alt = item.name;
            img.draggable = false;
            
            wrap.appendChild(img);
            makeDraggable(wrap, item.id);
            layer.appendChild(wrap);
        });
        
        const instruction = document.getElementById('dragInstruction');
        if (owned.size === 0) {
            instruction.textContent = 'Your room is empty! Buy furniture from the shop above.';
        } else {
            instruction.textContent = 'Drag furniture anywhere to arrange your cozy room! Click on your pet to make it follow your cursor.';
        }
        updateShopItems();
    }
    
    // Handle shop purchases
    document.querySelectorAll('.shop-item').forEach(shopItem => {
        shopItem.addEventListener('click', async () => {
            const id = shopItem.dataset.id;
            const price = parseInt(shopItem.dataset.price);
            const name = shopItem.dataset.name;
            const unlocked = shopItem.dataset.unlocked === 'true';
            const isPet = shopItem.dataset.isPet === 'true';
            
            if (owned.has(id)) {
                alert(`✅ You already own the ${name}!`);
                return;
            }
            
            if (!unlocked) {
                if (id === 'wisp') {
                    alert(`🔒 ${name} requires Level 3!`);
                } else {
                    alert(`🔒 ${name} requires Premium subscription or completing achievements!`);
                }
                return;
            }
            
            if (coins < price) {
                alert(`❌ Not enough coins! Need ${price} 🪙`);
                return;
            }
            
            if (confirm(`Buy ${name} for ${price} coins?`)) {
                const newCoins = coins - price;
                try {
                    const response = await fetch('/update-coins', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ coins: newCoins })
                    });
                    const result = await response.json();
                    if (result.success) {
                        coins = newCoins;
                        owned.add(id);
                        await saveToDatabase();
                        updateCoinDisplay();
                        render();
                        if (isPet) {
                            renderWisp();
                        }
                        alert(`✅ Purchased ${name}! ${isPet ? 'Your new companion will appear in the room!' : 'Drag it anywhere to position it.'}`);
                    } else {
                        alert('❌ Failed to update coins. Please try again.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('❌ Error processing purchase');
                }
            }
        });
    });
    
    // Toggle grid guide
    document.addEventListener('keydown', (e) => {
        if (e.key === 'g' || e.key === 'G') {
            gridGuide.classList.toggle('visible');
        }
    });
    
    // Load from database on start
    loadFromDatabase();
});
</script>
    @include('partials.music')
</body>
</html>