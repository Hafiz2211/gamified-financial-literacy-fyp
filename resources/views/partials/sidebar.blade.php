@props(['nav', 'active', 'GREEN', 'GOLD'])

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

    {{-- Remove profile dropdown from sidebar --}}
    {{-- It will now be in the top right corner --}}
</div>