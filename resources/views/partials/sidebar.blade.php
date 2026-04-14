@props(['nav', 'active', 'GREEN', 'GOLD'])

<style>
    /* Sidebar base styling */
    .brusave-sidebar {
        background: {{ $GREEN }} !important;
        width: 270px;
        height: 100vh;
        flex-shrink: 0;
        border-right: 1px solid rgba(47,93,70,0.22);
        display: flex;
        flex-direction: column;
    }
    
    /* Mobile styles */
    @media (max-width: 768px) {
        .brusave-sidebar {
            position: fixed;
            left: -270px;
            top: 0;
            transition: left 0.3s ease;
            z-index: 1000;
        }
        .brusave-sidebar.open {
            left: 0;
        }
        .menu-toggle {
            display: block;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: {{ $GREEN }};
            color: {{ $GOLD }};
            padding: 10px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 20px;
            border: none;
        }
        .menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .menu-overlay.active {
            display: block;
        }
    }
    @media (min-width: 769px) {
        .menu-toggle, .menu-overlay {
            display: none;
        }
    }
</style>

{{-- Menu Toggle Button for Mobile --}}
<button class="menu-toggle" onclick="toggleSidebar()">☰</button>

{{-- Overlay for mobile --}}
<div class="menu-overlay" onclick="closeSidebar()"></div>

{{-- Sidebar --}}
<div class="brusave-sidebar" id="mainSidebar">
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
                      color:       {{ $isActive ? $GOLD : 'rgba(255,255,255,0.92)' }};"
               onclick="closeSidebar()">
                <span class="text-lg">{{ $item['icon'] }}</span>
                <span class="font-semibold">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('mainSidebar');
        const overlay = document.querySelector('.menu-overlay');
        sidebar.classList.toggle('open');
        if (overlay) overlay.classList.toggle('active');
    }
    
    function closeSidebar() {
        const sidebar = document.getElementById('mainSidebar');
        const overlay = document.querySelector('.menu-overlay');
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
    }
</script>