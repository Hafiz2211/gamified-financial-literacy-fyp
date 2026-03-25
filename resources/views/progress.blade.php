<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        
        /* Pixel-perfect rendering */
        .pixel img {
            image-rendering: pixelated;
            image-rendering: crisp-edges;
            user-select: none;
            -webkit-user-drag: none;
            display: block;
            width: 100%;
            height: auto;
        }
        
        /* Room container */
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
        
        /* Furniture items - only shown when owned */
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

        /* Tooltip - simplified */
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
        
        /* Furniture shop items */
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
        
        /* Grid guide */
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
    
    $nav = [
        ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard','icon'=>'🏠'],
        ['key'=>'learn','label'=>'Learn','href'=>'/learn','icon'=>'📖'],
        ['key'=>'quiz','label'=>'Quiz','href'=>'/quiz','icon'=>'❓'],
        ['key'=>'track','label'=>'Track Spending','href'=>'/track-spending','icon'=>'🧾'],
        ['key'=>'progress','label'=>'Room','href'=>'/progress','icon'=>'🏆'],
        ['key'=>'town','label'=>'Town','href'=>'/town','icon'=>'🏘️'],
    ];

    // Get REAL user data from database
    $userName = $user->name ?? 'Hafiz';
    $level = $user->level ?? 1;
    $coins = $user->coins ?? 0;
    $xp = $user->xp ?? 0;
    
    // 🔴 FIXED: XP calculation matching dashboard
    // Define XP thresholds
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

    // Get threshold for current level
    $currentLevelThreshold = $levelThresholds[$level];

    // Calculate XP needed for next level
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

    // Progress percentage
    $xpProgress = $xpNeededForNextLevel > 0 ? ($xpInCurrentLevel / $xpNeededForNextLevel) * 100 : 100;
    $xpProgress = min(100, max(0, $xpProgress));

    // Display text for the XP bar
    $xpDisplay = $xpInCurrentLevel . '/' . $xpNeededForNextLevel . ' XP to Level ' . $nextLevel;
    
    // Determine title based on level
    if ($level <= 2) {
        $title = 'Beginner Mayor';
    } elseif ($level <= 4) {
        $title = 'Rising Mayor';
    } elseif ($level <= 6) {
        $title = 'Great Mayor';
    } else {
        $title = 'Legendary Mayor';
    }
    
    // Get user stats from database
    $completedLessons = $user->lessons()->count() ?? 0;
    $totalLessons = 6;
    
    $passedQuizzes = $user->quizAttempts()
        ->where('passed', true)
        ->distinct('quiz_id')
        ->count('quiz_id') ?? 0;
    
    $spendingRecords = $user->transactions()->count() ?? 0;
    
    // Achievements with image paths
    $achievements = [
        'plant' => [
            'name' => 'Potted Plant',
            'image' => asset('images/room/plant.png'),
            'condition' => $completedLessons >= 1,
            'requirement' => 'Complete 1 Lesson',
            'price' => 50,
            'unlocked' => $completedLessons >= 1
        ],
        'chair' => [
            'name' => 'Study Chair',
            'image' => asset('images/room/chair.png'),
            'condition' => $spendingRecords >= 10,
            'requirement' => 'Add 10 Spending Records',
            'price' => 100,
            'unlocked' => $spendingRecords >= 10
        ],
        'desk' => [
            'name' => 'Study Desk',
            'image' => asset('images/room/desk.png'),
            'condition' => $passedQuizzes >= 1,
            'requirement' => 'Pass Quiz Level 1',
            'price' => 150,
            'unlocked' => $passedQuizzes >= 1
        ],
        'bed' => [
            'name' => 'Comfy Bed',
            'image' => asset('images/room/bed.png'),
            'condition' => $level >= 3,
            'requirement' => 'Reach Level 3',
            'price' => 250,
            'unlocked' => $level >= 3
        ],
        'wardrobe' => [
            'name' => 'Storage Wardrobe',
            'image' => asset('images/room/wardrobe.png'),
            'condition' => $passedQuizzes >= 3,
            'requirement' => 'Pass Quiz Level 3',
            'price' => 300,
            'unlocked' => $passedQuizzes >= 3
        ],
    ];
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
        
        <div style="max-width:1200px; margin:0 auto; padding:32px 24px;">
            
            {{-- TOP BAR --}}
            <section class="rounded-3xl border shadow-lg p-6"
                     style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-2xl font-extrabold" style="color:{{ $GREEN }};">
                            {{ $userName }}'s Room
                        </div>
                        <div class="text-sm font-semibold mt-1" style="color:{{ $GOLD }};">
                            <span id="titleDisplay">{{ $title }}</span>
                        </div>
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
            <section class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="rounded-2xl border p-4" style="background:{{ $CARD }};">
                    <div class="text-xs" style="color: rgba(47,93,70,0.65);">📚 Lessons</div>
                    <div class="text-xl font-bold" style="color:{{ $GREEN }};">{{ $completedLessons }}/{{ $totalLessons }}</div>
                </div>
                <div class="rounded-2xl border p-4" style="background:{{ $CARD }};">
                    <div class="text-xs" style="color: rgba(47,93,70,0.65);">📝 Quizzes Passed</div>
                    <div class="text-xl font-bold" style="color:{{ $GREEN }};">{{ $passedQuizzes }}/3</div>
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

            {{-- FURNITURE SHOP WITH IMAGES --}}
            <section class="mt-8 rounded-3xl border shadow-lg p-6"
                     style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold" style="color:{{ $GREEN }};">🛒 Furniture Shop</h2>
                    <div class="text-sm" style="color: rgba(47,93,70,0.7);">
                        Press <kbd style="background: #2F5D46; color: #D8A24A; padding: 2px 6px; border-radius: 4px;">G</kbd> for grid
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @foreach($achievements as $key => $item)
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

                <div class="room-container">
                    {{-- Background image --}}
                    <img
                        src="{{ asset('images/room/room.png') }}"
                        alt="Empty room"
                        class="absolute inset-0 w-full h-full object-cover pointer-events-none select-none"
                        onerror="this.style.display='none';"
                    >

                    <div id="gridGuide" class="grid-guide">
                        @for($i = 0; $i < 96; $i++)
                            <div class="grid-cell"></div>
                        @endfor
                    </div>

                    <div id="furnitureLayer" class="absolute inset-0"></div>
                </div>
            </section>

            <footer class="text-center text-xs pt-8 pb-2" style="color: rgba(47,93,70,0.75);">
                © {{ date('Y') }} Bru<i>Save</i> 
            </footer>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Get current user ID
    const userId = {{ $user->id }};
    
    // User data
    let coins = Number(@json($coins));
    const level = Number(@json($level));
    const achievements = @json($achievements);
    
    // Storage keys per user
    const STORAGE_OWNED = `brusave.room.owned.${userId}`;
    const STORAGE_POSITIONS = `brusave.room.positions.${userId}`;
    
    // Load owned furniture
    const loadOwned = () => {
        try {
            const raw = localStorage.getItem(STORAGE_OWNED);
            return raw ? new Set(JSON.parse(raw)) : new Set();
        } catch (e) {
            return new Set();
        }
    };
    
    // Load saved positions
    const loadPositions = () => {
        try {
            const raw = localStorage.getItem(STORAGE_POSITIONS);
            return raw ? JSON.parse(raw) : {};
        } catch (e) {
            return {};
        }
    };
    
    // Save data
    const saveOwned = (set) => {
        localStorage.setItem(STORAGE_OWNED, JSON.stringify([...set]));
    };
    
    const savePositions = (positions) => {
        localStorage.setItem(STORAGE_POSITIONS, JSON.stringify(positions));
    };
    
    // State
    let owned = loadOwned();
    let savedPositions = loadPositions();
    
    // DOM elements
    const coinDisplay = document.getElementById('coinDisplay');
    const coinDisplay2 = document.getElementById('coinDisplay2');
    const layer = document.getElementById('furnitureLayer');
    const gridGuide = document.getElementById('gridGuide');
    
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
    const items = [
        {
            id:'plant',
            name:'Potted Plant',
            src:"{{ asset('images/room/plant.png') }}",
            price:50,
            size: 'small',
            reqAchievement: 'plant',
            defaultPos: { left: 60, top: 350, width: 80 },   
            z: 10
        },
        {
            id:'chair',
            name:'Study Chair',
            src:"{{ asset('images/room/chair.png') }}",
            price:100,
            size: 'small',
            reqAchievement: 'chair',
            defaultPos: { left: 450, top: 280, width: 120 },
            z: 30
        },
        {
            id:'desk',
            name:'Study Desk',
            src:"{{ asset('images/room/desk.png') }}",
            price:150,
            size: 'medium',
            reqAchievement: 'desk',
            defaultPos: { left: 600, top: 150, width: 200 },
            z: 25
        },
        {
            id:'bed',
            name:'Comfy Bed',
            src:"{{ asset('images/room/bed.png') }}",
            price:250,
            size: 'medium',
            reqAchievement: 'bed',
            defaultPos: { left: 700, top: 300, width: 220 },
            z: 35
        },
        {
            id:'wardrobe',
            name:'Storage Wardrobe',
            src:"{{ asset('images/room/wardrobe.png') }}",
            price:300,
            size: 'large',
            reqAchievement: 'wardrobe',
            defaultPos: { left: 100, top: 80, width: 200 }, 
            z: 20
        },
    ];

    function createTooltip(text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip-tag';
        tooltip.textContent = text;
        return tooltip;
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
                savePositions(savedPositions);
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

        items.forEach(item => {
            if (!owned.has(item.id)) return;
            
            const pos = savedPositions[item.id] || item.defaultPos;
            
            const wrap = document.createElement('div');
            wrap.className = 'furniture-item owned';
            if (item.size) {
                wrap.classList.add(item.size);
            }
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
            instruction.textContent = 'Drag furniture anywhere to arrange your cozy room!';
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
            
            if (owned.has(id)) {
                alert(`✅ You already own the ${name}!`);
                return;
            }
            
            if (!unlocked) {
                alert(`❌ ${name} is locked! Complete the requirement first.`);
                return;
            }
            
            if (coins < price) {
                alert(`❌ Not enough coins! Need ${price} 🪙`);
                return;
            }
            
            if (confirm(`Buy ${name} for ${price} coins?`)) {
                // Calculate new coin total
                const newCoins = coins - price;
                
                try {
                    const response = await fetch('/update-coins', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            coins: newCoins
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Update local state
                        coins = newCoins;
                        owned.add(id);
                        
                        // Save to localStorage
                        saveOwned(owned);
                        
                        // Update displays
                        updateCoinDisplay();
                        render();
                        
                        alert(`✅ Purchased ${name}! Drag it anywhere to position it.`);
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

    // Initial render
    render();
});
</script>
</body>
</html>