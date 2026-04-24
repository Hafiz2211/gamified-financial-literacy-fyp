<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome • BruSave</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial,
                         "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji";
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body class="min-h-screen text-slate-900" style="background:#F6F1E6;">
    @php
        $GREEN = '#2F5D46';
        $GOLD = '#D8A24A';
        $CARD = '#FFFBF2';
    @endphp

    <main class="relative min-h-screen px-6 py-6 overflow-hidden">
        {{-- Soft cozy glow behind --}}
        <div class="pointer-events-none absolute -top-24 left-1/2 -translate-x-1/2 h-[320px] w-[640px] rounded-full blur-3xl opacity-20"
             style="background:{{ $GOLD }};"></div>
        <div class="pointer-events-none absolute -top-10 left-1/2 -translate-x-1/2 h-[260px] w-[520px] rounded-full blur-3xl opacity-16"
             style="background:{{ $GREEN }};"></div>

        {{-- Top Bar with Logo (left) and Auth Buttons (right) --}}
        <div class="flex items-center justify-between max-w-3xl mx-auto">
            {{-- Logo Area --}}
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/brusave-logo.png') }}" alt="BruSave logo" class="h-10 w-auto object-contain">
                <div>
                    <div class="text-2xl font-extrabold leading-tight" style="color:{{ $GREEN }};">
                        Bru<i>Save</i>
                    </div>
                    <div class="text-xs font-semibold" style="color:{{ $GOLD }}; opacity:0.95;">
                        Build Wealth, Build Your Town
                    </div>
                </div>
            </div>

            {{-- Auth Buttons (Top Right Corner) --}}
            <div>
                @auth
                    <a href="/dashboard"
                       class="inline-flex items-center px-5 py-2 rounded-xl font-semibold transition hover:opacity-90"
                       style="background:{{ $GREEN }}; color:{{ $GOLD }};">
                        Dashboard
                    </a>
                @else
                    <div class="flex items-center gap-3">
                        <a href="/login"
                           class="inline-flex items-center px-5 py-2 rounded-xl font-semibold transition hover:opacity-90"
                           style="background:{{ $GREEN }}; color:{{ $GOLD }};">
                            Login
                        </a>
                        <a href="/register"
                           class="inline-flex items-center px-5 py-2 rounded-xl font-semibold transition hover:opacity-90"
                           style="background:rgba(47,93,70,0.1); color:{{ $GREEN }}; border:1px solid rgba(47,93,70,0.3);">
                            Register
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        {{-- Main Content - ONE WHITE CARD with title on top, image below (no extra frames) --}}
        <div class="max-w-3xl mx-auto mt-10">
            <div class="rounded-2xl border shadow-lg overflow-hidden"
                 style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                
                {{-- 🔴 MOBILE WARNING BANNER (Only visible on phones) --}}
                <div class="block lg:hidden m-4 p-3 rounded-xl text-center" style="background: rgba(216,162,74,0.12); border: 1px solid {{ $GOLD }};">
                    <p style="color: {{ $GOLD }}; font-size: 12px; margin: 0;">
                        💡 <strong>Tip:</strong> For the best BruSave experience (Town Builder & Room Decor), use a <strong>laptop or desktop</strong>. 
                        Mobile version will be implemented in a future update.
                    </p>
                </div>

                {{-- Title and Description (top section of the card) --}}
                <div class="p-8 pb-4 text-center">
                    <h1 class="text-3xl md:text-4xl font-extrabold" style="color:{{ $GREEN }};">
                        Welcome, Mayor
                    </h1>
                    <p class="mt-4 text-base leading-relaxed max-w-2xl mx-auto" style="color: rgba(47,93,70,0.85);">
                        Learn financial basics, track your spending and earn coins to unlock and furnish your town. Every smart decision helps your town grow stronger.
                    </p>
                </div>

                {{-- Hero Image - directly below, touching edges of card, no extra frame --}}
                <div>
                    <img src="{{ asset('images/welcome-town.png') }}" alt="Cozy town" class="w-full h-auto block">
                </div>
            </div>

            {{-- Footer --}}
            <footer class="mt-10 text-center text-xs" style="color: rgba(47,93,70,0.75);">
                © {{ date('Y') }} Bru<i>Save</i>
            </footer>
        </div>
    </main>
</body>
</html>