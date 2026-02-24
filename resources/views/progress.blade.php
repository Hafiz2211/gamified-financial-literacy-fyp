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
            display: none; /* Hidden by default */
            cursor: move;
            user-select: none;
            transition: filter 0.2s ease, transform 0.1s ease;
            z-index: 10;
        }
        
        .furniture-item.owned {
            display: block; /* Only show when owned */
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
        }
        
        /* Tooltip */
        .tooltip-tag {
            position: absolute;
            left: 50%;
            top: -30px;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.85);
            color: white;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 1001;
            border: 1px solid #D8A24A;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .tooltip-tag::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid rgba(0,0,0,0.85);
        }
        
        /* Furniture shop items */
        .shop-item {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .shop-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(216,162,74,0.3);
        }
        
        .shop-item.locked {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .shop-item.locked:hover {
            transform: none;
            box-shadow: none;
        }
        
        .shop-item.owned {
            opacity: 0.8;
            cursor: default;
            background: rgba(47,93,70,0.1) !important;
        }
        
        .shop-item.owned:hover {
            transform: none;
            box-shadow: none;
        }
        
        /* Grid guide (toggle with 'G' key) */
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
    
    // Calculate next level XP
    $nextLevelXP = 200 + ($level * 100);
    $xpProgress = $nextLevelXP > 0 ? min(100, ($xp / $nextLevelXP) * 100) : 0;
    
    // Determine title based on level
    if ($level <= 2) {
        $title = 'Beginner Mayor';
    } elseif ($level <= 4) {
        $title = 'Intermediate Mayor';
    } elseif ($level <= 6) {
        $title = 'Advanced Mayor';
    } else {
        $title = 'Elite Mayor';
    }
    
    // Get user stats from database
    $completedLessons = $user->lessons()->count() ?? 0;
    $totalLessons = 6;
    
    $passedQuizzes = $user->quizAttempts()
        ->where('passed', true)
        ->distinct('quiz_id')
        ->count('quiz_id') ?? 0;
    
    $spendingRecords = $user->transactions()->count() ?? 0;
    
    // Check achievements for furniture unlocking
    $achievements = [
        'plant' => [
            'name' => 'Cozy Plant',
            'icon' => '🌱',
            'condition' => $completedLessons >= 1,
            'requirement' => 'Complete 1 Lesson',
            'price' => 50,
            'unlocked' => $completedLessons >= 1
        ],
        'chair' => [
            'name' => 'Study Chair',
            'icon' => '🪑',
            'condition' => $spendingRecords >= 10,
            'requirement' => 'Add 10 Spending Records',
            'price' => 100,
            'unlocked' => $spendingRecords >= 10
        ],
        'desk' => [
            'name' => 'Study Desk',
            'icon' => '🪵',
            'condition' => $passedQuizzes >= 1,
            'requirement' => 'Pass Quiz Level 1',
            'price' => 150,
            'unlocked' => $passedQuizzes >= 1
        ],
        'bed' => [
            'name' => 'Comfy Bed',
            'icon' => '🛏️',
            'condition' => $level >= 3,
            'requirement' => 'Reach Level 3',
            'price' => 250,
            'unlocked' => $level >= 3
        ],
        'wardrobe' => [
            'name' => 'Storage Wardrobe',
            'icon' => '🚪',
            'condition' => $passedQuizzes >= 3,
            'requirement' => 'Pass Quiz Level 3',
            'price' => 400,
            'unlocked' => $passedQuizzes >= 3
        ],
    ];
@endphp

<div class="app-container">
    {{-- Sidebar --}}
    <div class="sidebar">
        <div class="p-5 border-b" style="border-color: rgba(255,255,255,0.12);">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/brusave-logo.png') }}" alt="BruSave logo" class="h-8 w-auto object-contain">
                <div>
                    <div class="text-xl font-extrabold leading-tight" style="color:{{ $GOLD }};">Bru<i>Save</i></div>
                    <div class="text-xs" style="color: rgba(216,162,74,0.78);">Build Wealth, Build Your Town</div>
                </div>
            </div>
        </div>

        <nav class="p-4 space-y-2 flex-1 overflow-y-auto">
            @foreach ($nav as $item)
                @php $isActive = $active === $item['key']; @endphp
                <a href="{{ $item['href'] }}"
                   class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border transition hover:opacity-95"
                   style="border-color: {{ $isActive ? 'rgba(216,162,74,0.60)' : 'rgba(255,255,255,0.16)' }};
                          background:  {{ $isActive ? 'rgba(216,162,74,0.14)' : 'rgba(255,255,255,0.04)' }};
                          color:       {{ $isActive ? $GOLD : 'rgba(255,255,255,0.92)' }};">
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
                        style="border-color: rgba(255,255,255,0.16); color: rgba(255,255,255,0.92); background: rgba(255,255,255,0.04);">
                    <span>🚪</span> Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Main content --}}
    <div class="main-content">
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
                        <span>{{ $xp }}/{{ $nextLevelXP }} XP to Level {{ $level + 1 }}</span>
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

            {{-- FURNITURE SHOP --}}
            <section class="mt-8 rounded-3xl border shadow-lg p-6"
                     style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold" style="color:{{ $GREEN }};">🛒 Furniture Shop</h2>
                    <div class="text-sm" style="color: rgba(47,93,70,0.7);">
                        Press <kbd style="background: #2F5D46; color: #D8A24A; padding: 2px 6px; border-radius: 4px;">G</kbd> for grid
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @foreach($achievements as $key => $item)
                        <div class="shop-item rounded-xl border p-4 text-center" 
                             data-id="{{ $key }}"
                             data-price="{{ $item['price'] }}"
                             data-name="{{ $item['name'] }}"
                             data-unlocked="{{ $item['condition'] ? 'true' : 'false' }}"
                             style="border-color: {{ $item['condition'] ? 'rgba(216,162,74,0.4)' : 'rgba(47,93,70,0.16)' }};
                                    background: {{ $item['condition'] ? 'rgba(216,162,74,0.05)' : 'rgba(47,93,70,0.02)' }};">
                            <div class="text-4xl mb-2">{{ $item['icon'] }}</div>
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

                {{-- Room container --}}
                <div class="room-container">
                    {{-- Background image (empty room) --}}
                    <img
                        src="{{ asset('images/room/room.png') }}"
                        alt="Empty room"
                        class="absolute inset-0 w-full h-full object-cover pointer-events-none select-none"
                        onerror="this.style.display='none';"
                    >

                    {{-- Grid guide (toggle with G) --}}
                    <div id="gridGuide" class="grid-guide">
                        @for($i = 0; $i < 96; $i++)
                            <div class="grid-cell"></div>
                        @endfor
                    </div>

                    {{-- Furniture layer --}}
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
    // Get current user ID from Laravel
    const userId = {{ $user->id }};
    
    // User data
    let coins = Number(@json($coins));
    const level = Number(@json($level));
    const achievements = @json($achievements);
    
    // 🔴 FIXED: Storage keys are now UNIQUE PER USER
    const STORAGE_OWNED = `brusave.room.owned.${userId}`;
    const STORAGE_POSITIONS = `brusave.room.positions.${userId}`;
    
    // Load owned furniture - PER USER
    const loadOwned = () => {
        try {
            const raw = localStorage.getItem(STORAGE_OWNED);
            return raw ? new Set(JSON.parse(raw)) : new Set();
        } catch (e) {
            return new Set();
        }
    };
    
    // Load saved positions - PER USER
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
    
    // State - each user has their OWN furniture
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
    
    // Furniture definitions
    const items = [
        {
            id:'plant',
            name:'Cozy Plant',
            src:"{{ asset('images/room/plant.png') }}",
            icon: '🌱',
            price:50,
            reqAchievement: 'plant',
            defaultPos: { left: 60, top: 350, width: 120 },
            z: 10
        },
        {
            id:'chair',
            name:'Study Chair',
            src:"{{ asset('images/room/chair.png') }}",
            icon: '🪑',
            price:100,
            reqAchievement: 'chair',
            defaultPos: { left: 450, top: 280, width: 140 },
            z: 30
        },
        {
            id:'desk',
            name:'Study Desk',
            src:"{{ asset('images/room/desk.png') }}",
            icon: '🪵',
            price:150,
            reqAchievement: 'desk',
            defaultPos: { left: 600, top: 150, width: 180 },
            z: 25
        },
        {
            id:'bed',
            name:'Comfy Bed',
            src:"{{ asset('images/room/bed.png') }}",
            icon: '🛏️',
            price:250,
            reqAchievement: 'bed',
            defaultPos: { left: 700, top: 300, width: 200 },
            z: 35
        },
        {
            id:'wardrobe',
            name:'Storage Wardrobe',
            src:"{{ asset('images/room/wardrobe.png') }}",
            icon: '🚪',
            price:400,
            reqAchievement: 'wardrobe',
            defaultPos: { left: 100, top: 80, width: 150 },
            z: 20
        },
    ];

    function createTooltip(text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip-tag';
        tooltip.textContent = text;
        return tooltip;
    }

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

    function render() {
        layer.innerHTML = '';

        items.forEach(item => {
            if (!owned.has(item.id)) return;
            
            const pos = savedPositions[item.id] || item.defaultPos;
            
            const wrap = document.createElement('div');
            wrap.className = 'furniture-item owned';
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
            wrap.appendChild(createTooltip(`✅ ${item.name} (drag to move)`));
            
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
        shopItem.addEventListener('click', () => {
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
                coins -= price;
                owned.add(id);
                saveOwned(owned);
                updateCoinDisplay();
                render();
                alert(`✅ Purchased ${name}! Drag it anywhere to position it.`);
            }
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'g' || e.key === 'G') {
            gridGuide.classList.toggle('visible');
        }
    });

    render();
    console.log('User', userId, 'owned furniture:', owned);
});
</script>
</body>
</html>