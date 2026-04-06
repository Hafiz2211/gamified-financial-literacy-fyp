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

    <main class="relative min-h-screen flex items-center justify-center px-6 py-8 overflow-hidden">
        {{-- Soft cozy glow behind header --}}
        <div class="pointer-events-none absolute -top-24 left-1/2 -translate-x-1/2 h-[320px] w-[640px] rounded-full blur-3xl opacity-20"
             style="background:{{ $GOLD }};"></div>
        <div class="pointer-events-none absolute -top-10 left-1/2 -translate-x-1/2 h-[260px] w-[520px] rounded-full blur-3xl opacity-16"
             style="background:{{ $GREEN }};"></div>

        <div class="w-full max-w-3xl">
            {{-- Logo + Slogan --}}
            <div class="text-center">
                <img
                    src="{{ asset('images/brusave-logo.png') }}"
                    alt="BruSave logo"
                    class="mx-auto h-14 w-auto object-contain"
                >
                <div class="mt-3 text-3xl font-extrabold leading-tight" style="color:{{ $GREEN }};">
                    Bru<i>Save</i>
                </div>

                <div class="mt-1 text-sm font-semibold"
                     style="color:{{ $GOLD }}; opacity:0.95;">
                    Build Wealth, Build Your Town
                </div>

                <div class="mx-auto mt-3 h-1 w-14 rounded-full"
                     style="background: rgba(216,162,74,0.85);"></div>
            </div>

            {{-- Hero Rectangle --}}
            <section class="mt-6 rounded-2xl border shadow-lg overflow-hidden"
                     style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">

                {{-- Image Area - No grey space --}}
                <div class="border-b overflow-hidden"
                     style="border-color: rgba(47,93,70,0.12); background: rgba(47,93,70,0.06);">
                    <img
                        src="{{ asset('images/welcome-town.png') }}"
                        alt="Cozy town"
                        class="w-full h-auto block"
                    >
                </div>

                {{-- Mission Text --}}
                <div class="p-8">
                    <h1 class="text-2xl font-extrabold" style="color:{{ $GREEN }};">
                        Welcome, Mayor. Your Town Awaits.
                    </h1>

                    <p class="mt-3 text-base leading-relaxed" style="color: rgba(47,93,70,0.85);">
                        Learn financial basics, track your spending and earn coins to unlock and furnish your town.
                        Every responsible choice helps your town grow stronger.
                    </p>
                </div>
            </section>

            {{-- Buttons --}}
            <div class="mt-6 flex flex-col items-center">
                @auth
                    <a href="/dashboard"
                       class="w-full md:w-auto md:min-w-[280px] inline-flex items-center justify-center px-8 py-3 rounded-xl font-semibold transition hover:opacity-90"
                       style="background:{{ $GREEN }}; color:{{ $GOLD }};">
                        Go to Dashboard
                    </a>
                @else
                    <a href="/login"
                       class="w-full md:w-auto md:min-w-[280px] inline-flex items-center justify-center px-8 py-3 rounded-xl font-semibold transition hover:opacity-90"
                       style="background:{{ $GREEN }}; color:{{ $GOLD }};">
                        Login
                    </a>

                    <div class="mt-3 text-center text-sm" style="color: rgba(47,93,70,0.78);">
                        New here?
                        <a href="/register"
                           class="font-semibold underline underline-offset-4"
                           style="color:{{ $GOLD }};">
                            Register
                        </a>
                    </div>
                @endauth
            </div>

            <footer class="mt-8 text-center text-xs" style="color: rgba(47,93,70,0.75);">
                © {{ date('Y') }} Bru<i>Save</i>
            </footer>
        </div>
    </main>
</body>
</html>