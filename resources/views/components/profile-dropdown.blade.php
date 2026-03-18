@props(['user'])

<div class="relative" x-data="{ open: false }">
    {{-- Profile Button --}}
    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
        <div class="relative">
            @if($user->profile_photo_path)
                <img src="{{ Storage::url($user->profile_photo_path) }}" 
                     alt="{{ $user->name }}"
                     class="h-10 w-10 rounded-full object-cover border-2"
                     style="border-color: #D8A24A;">
            @else
                <div class="h-10 w-10 rounded-full flex items-center justify-center text-lg font-bold border-2"
                     style="background: #2F5D46; color: #D8A24A; border-color: #D8A24A;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>
        <span class="text-sm font-medium hidden md:inline" style="color: #2F5D46;">{{ $user->name }}</span>
        <svg x-show="!open" class="h-4 w-4" style="color: #D8A24A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
        <svg x-show="open" class="h-4 w-4" style="color: #D8A24A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-56 rounded-xl shadow-lg py-1 z-50 border"
         style="background: #FFFBF2; border-color: rgba(47,93,70,0.16);"
         @click.away="open = false">
        
        {{-- User Info (optional - you can remove this too if you want) --}}
        <div class="px-4 py-3 border-b" style="border-color: rgba(47,93,70,0.12);">
            <p class="text-sm font-medium" style="color: #2F5D46;">{{ $user->name }}</p>
            <p class="text-xs truncate" style="color: rgba(47,93,70,0.65);">{{ $user->email }}</p>
        </div>

        {{-- Settings --}}
        <a href="{{ route('profile.edit') }}" 
           class="block px-4 py-2 text-sm hover:bg-opacity-80 transition"
           style="color: #2F5D46; hover:background: rgba(216,162,74,0.1);"
           @click="open = false">
            <span class="mr-2">⚙️</span> Settings
        </a>
        
        {{-- Delete Account --}}
        <a href="{{ route('profile.delete') }}" 
           class="block px-4 py-2 text-sm hover:bg-opacity-80 transition"
           style="color: #b43c3c; hover:background: rgba(180,60,60,0.1);"
           @click="open = false">
            <span class="mr-2">🗑️</span> Delete Account
        </a>
        
        <div class="border-t my-1" style="border-color: rgba(47,93,70,0.12);"></div>
        
        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="w-full text-left px-4 py-2 text-sm hover:bg-opacity-80 transition"
                    style="color: #2F5D46; hover:background: rgba(216,162,74,0.1);">
                <span class="mr-2">🚪</span> Logout
            </button>
        </form>
    </div>
</div>