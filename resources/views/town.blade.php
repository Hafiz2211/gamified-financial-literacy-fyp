@extends('layouts.app')

@section('title', 'Town Builder')

@section('content')
@php
    $GREEN = '#2F5D46';
    $GOLD = '#D8A24A';
    $CARD = '#FFFBF2';
@endphp

<div class="w-full">
    {{-- 🔴 MOBILE WARNING BANNER (Only visible on phones) --}}
    <div class="block lg:hidden mb-4 p-3 rounded-xl text-center" style="background: rgba(216,162,74,0.12); border: 1px solid {{ $GOLD }};">
        <p style="color: {{ $GOLD }}; font-size: 12px; margin: 0;">
            💡 <strong>Tip:</strong> For the town building experience, use a <strong>laptop or desktop</strong>. 
            Drag and drop works best with a mouse. Mobile version will be implemented in a future update.
        </p>
    </div>

    {{-- Header Section - NO BOX, just text --}}
    <div class="mb-6">
        <h1 class="text-3xl md:text-4xl font-extrabold" style="color:{{ $GREEN }};">
            Build Your Town
        </h1>
        <p class="mt-2 text-base" style="color: rgba(47,93,70,0.78); line-height:1.6;">
            Grow and develop your own town as you progress. Use your coins and resources to place buildings, 
            expand your settlement and watch your town come to life.
        </p>
    </div>

    {{-- Tutorial Dropdown - matches Learn page style --}}
    <details class="rounded-2xl border shadow-sm mb-6"
             style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
        <summary class="cursor-pointer font-extrabold p-5 flex items-center justify-between rounded-2xl"
                 style="color:{{ $GOLD }};">
            <span class="flex items-center gap-2">
                <span class="text-xl">📖</span>
                <span>Game Tutorial & Mechanics</span>
            </span>
            <span class="text-lg" style="color: rgba(47,93,70,0.55);">▾</span>
        </summary>
        
        <div class="px-5 pb-5 border-t" style="border-color: rgba(47,93,70,0.12);">
            
            {{-- Level Unlocks --}}
            <div class="mt-4 rounded-xl p-4" style="background: rgba(47,93,70,0.05);">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🎯</span>
                    <h3 class="font-extrabold" style="color:{{ $GREEN }};">Level Unlocks</h3>
                </div>
                <p class="text-sm mt-1" style="color: rgba(47,93,70,0.75);">
                    Level unlocks new buildings and features. Earned outside the game through the website.
                </p>
            </div>

            {{-- Resources Section --}}
            <h3 class="font-extrabold mt-5 mb-3" style="color:{{ $GREEN }};">📊 Resources</h3>
            <div class="space-y-3">
                
                <!-- Coins -->
                <div class="rounded-xl border p-4" style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xl">🪙</span>
                        <h4 class="font-extrabold" style="color:{{ $GREEN }};">Coins</h4>
                    </div>
                    <p class="text-sm" style="color: rgba(47,93,70,0.75);">
                        Used for building and expansion. Also earned through the website.
                    </p>
                </div>

                <!-- Population -->
                <div class="rounded-xl border p-4" style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xl">👥</span>
                        <h4 class="font-extrabold" style="color:{{ $GREEN }};">Population</h4>
                    </div>
                    <p class="text-sm" style="color: rgba(47,93,70,0.75);">
                        Comes from houses. More villagers increase production, but also consume food.
                    </p>
                </div>

                <!-- Food -->
                <div class="rounded-xl border p-4" style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xl">🍎</span>
                        <h4 class="font-extrabold" style="color:{{ $GREEN }};">Food</h4>
                    </div>
                    <p class="text-sm" style="color: rgba(47,93,70,0.75);">
                        Produced by food buildings and feeds villagers daily. If it runs out, villagers lose energy.
                    </p>
                </div>

                <!-- Wood -->
                <div class="rounded-xl border p-4" style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xl">🪵</span>
                        <h4 class="font-extrabold" style="color:{{ $GREEN }};">Wood</h4>
                    </div>
                    <p class="text-sm" style="color: rgba(47,93,70,0.75);">
                        Produced by industry buildings. Used to construct and expand your town.
                    </p>
                </div>

                <!-- Stone -->
                <div class="rounded-xl border p-4" style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xl">🪨</span>
                        <h4 class="font-extrabold" style="color:{{ $GREEN }};">Stone</h4>
                    </div>
                    <p class="text-sm" style="color: rgba(47,93,70,0.75);">
                        Produced by industry buildings. Used to construct and expand your town.
                    </p>
                </div>

                <!-- Balance Tip -->
                <div class="rounded-xl border p-4" style="border-color: {{ $GOLD }}40; background: rgba(216,162,74,0.10);">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xl">⚖️</span>
                        <h4 class="font-extrabold" style="color:{{ $GREEN }};">Keep Balanced</h4>
                    </div>
                    <p class="text-sm" style="color: rgba(47,93,70,0.75);">
                        Keep resources balanced to avoid shortages.
                    </p>
                </div>
            </div>

            {{-- Building Categories --}}
            <h3 class="font-extrabold mt-6 mb-3" style="color:{{ $GREEN }};">🏗️ Building Categories</h3>
            <div class="space-y-2">
                
                <div class="rounded-xl border p-3" style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🏠</span>
                        <span class="font-extrabold text-sm" style="color:{{ $GREEN }};">Residential</span>
                    </div>
                    <p class="text-xs mt-1" style="color: rgba(47,93,70,0.7);">Provides housing and increases population.</p>
                </div>

                <div class="rounded-xl border p-3" style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📚</span>
                        <span class="font-extrabold text-sm" style="color:{{ $GREEN }};">Education</span>
                    </div>
                    <p class="text-xs mt-1" style="color: rgba(47,93,70,0.7);">Improves efficiency and unlocks bonuses.</p>
                </div>

                <div class="rounded-xl border p-3" style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🍎</span>
                        <span class="font-extrabold text-sm" style="color:{{ $GREEN }};">Food</span>
                    </div>
                    <p class="text-xs mt-1" style="color: rgba(47,93,70,0.7);">Produces food for villagers.</p>
                </div>

                <div class="rounded-xl border p-3" style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🏭</span>
                        <span class="font-extrabold text-sm" style="color:{{ $GREEN }};">Industry</span>
                    </div>
                    <p class="text-xs mt-1" style="color: rgba(47,93,70,0.7);">Generates wood and stone.</p>
                </div>

                <div class="rounded-xl border p-3" style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">⚽</span>
                        <span class="font-extrabold text-sm" style="color:{{ $GREEN }};">Sports</span>
                    </div>
                    <p class="text-xs mt-1" style="color: rgba(47,93,70,0.7);">Supports villager activity and well-being.</p>
                </div>

                <div class="rounded-xl border p-3" style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🎭</span>
                        <span class="font-extrabold text-sm" style="color:{{ $GREEN }};">Entertainment</span>
                    </div>
                    <p class="text-xs mt-1" style="color: rgba(47,93,70,0.7);">Increases happiness and appeal.</p>
                </div>

                <div class="rounded-xl border p-3" style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🏛️</span>
                        <span class="font-extrabold text-sm" style="color:{{ $GREEN }};">Civic</span>
                    </div>
                    <p class="text-xs mt-1" style="color: rgba(47,93,70,0.7);">Provides global bonuses like health and efficiency.</p>
                </div>
            </div>
        </div>
    </details>

    {{-- Game Iframe Section with Fullscreen Button --}}
    <div class="rounded-2xl border shadow-lg overflow-hidden"
         style="border-color: rgba(47,93,70,0.16); background:{{ $CARD }};">
        <div class="p-4 border-b flex items-center justify-between" style="border-color: rgba(47,93,70,0.12);">
            <div class="flex items-center gap-2">
                <span class="text-xl">🎮</span>
                <h2 class="font-extrabold" style="color:{{ $GREEN }};">Play Your Town</h2>
            </div>
            <button onclick="toggleFullscreen()" 
                    class="px-3 py-1.5 rounded-lg text-sm font-semibold transition hover:opacity-80 flex items-center gap-1"
                    style="background: rgba(216,162,74,0.15); color: {{ $GOLD }}; border: 1px solid {{ $GOLD }}40;">
                <span>⛶</span> Fullscreen
            </button>
        </div>
        <div id="gameContainer" style="position:relative; width:100%; padding-bottom:56.25%;">
            <iframe 
                src="{{ asset('brusave_city_builder_v3/City_Builder_Game_Final.html') }}"
                style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;"
                allow="fullscreen"
                id="gameFrame"
                title="Town Builder Game"
            ></iframe>
        </div>
    </div>

</div>

<script>
    function toggleFullscreen() {
        const container = document.getElementById('gameContainer');
        
        if (!document.fullscreenElement) {
            if (container.requestFullscreen) {
                container.requestFullscreen();
            } else if (container.webkitRequestFullscreen) {
                container.webkitRequestFullscreen();
            } else if (container.msRequestFullscreen) {
                container.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    }
</script>
@endsection