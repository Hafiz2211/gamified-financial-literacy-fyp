{{-- resources/views/livewire/pages/auth/register.blade.php --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register • BruSave</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial,
                         "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji";
        }
        .bs-input:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(216, 162, 74, 0.20);
            border-color: rgba(216, 162, 74, 0.65) !important;
        }
    </style>
</head>

<body class="min-h-screen text-slate-900" style="background:#F6F1E6;">
<main class="relative min-h-screen flex items-center justify-center px-6 py-10 overflow-hidden">

    {{-- Cozy glow --}}
    <div class="pointer-events-none absolute -top-24 left-1/2 -translate-x-1/2 h-[320px] w-[640px] rounded-full blur-3xl opacity-20"
         style="background:#D8A24A;"></div>
    <div class="pointer-events-none absolute -top-10 left-1/2 -translate-x-1/2 h-[260px] w-[520px] rounded-full blur-3xl opacity-16"
         style="background:#2F5D46;"></div>

    <div class="w-full max-w-md">
        {{-- Header --}}
        <div class="text-center">
            <img src="{{ asset('images/brusave-logo.png') }}"
                 alt="BruSave logo"
                 class="mx-auto h-14 w-auto object-contain">

            <div class="mt-3 text-4xl font-extrabold leading-tight" style="color:#2F5D46;">
                Bru<i>Save</i>
            </div>

            <div class="mt-1 text-sm font-semibold" style="color:#D8A24A; opacity:0.95;">
                Build Wealth, Build Your Town
            </div>

            <div class="mx-auto mt-4 h-1 w-16 rounded-full"
                 style="background: rgba(216,162,74,0.85);"></div>
        </div>

        {{-- Card --}}
        <section class="mt-8 rounded-3xl border shadow-lg"
                 style="background:#FFFBF2; border-color: rgba(47,93,70,0.16);">

            {{-- Inner padding wrapper (fixes “touching edges”) --}}
            <div class="p-6 sm:p-8">
                <h1 class="text-3xl font-extrabold tracking-tight" style="color:#2F5D46;">
                    Register
                </h1>
                <p class="mt-2 text-sm" style="color: rgba(47,93,70,0.78);">
                    Create your account and start building your town.
                </p>

                {{-- Errors --}}
                @if ($errors->any())
                    <div class="mt-5 rounded-2xl border px-4 py-3 text-sm"
                         style="border-color: rgba(180,60,60,0.30); background: rgba(180,60,60,0.08); color: rgba(120,30,30,0.95);">
                        <div class="font-semibold">Please fix the following:</div>
                        <ul class="mt-2 list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold" style="color:#2F5D46;">Name</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            class="bs-input mt-2 w-full rounded-2xl border px-4 py-3"
                            style="background:#FFFFFF; border-color: rgba(47,93,70,0.18);"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold" style="color:#2F5D46;">Email</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            class="bs-input mt-2 w-full rounded-2xl border px-4 py-3"
                            style="background:#FFFFFF; border-color: rgba(47,93,70,0.18);"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold" style="color:#2F5D46;">Password</label>
                        <input
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="bs-input mt-2 w-full rounded-2xl border px-4 py-3"
                            style="background:#FFFFFF; border-color: rgba(47,93,70,0.18);"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold" style="color:#2F5D46;">Confirm Password</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="bs-input mt-2 w-full rounded-2xl border px-4 py-3"
                            style="background:#FFFFFF; border-color: rgba(47,93,70,0.18);"
                        >
                    </div>

                    <div class="pt-2 flex flex-col items-center">
                        <button
                            type="submit"
                            class="w-full md:w-auto md:min-w-[280px] inline-flex items-center justify-center px-8 py-3 rounded-2xl font-semibold transition hover:opacity-90"
                            style="background:#2F5D46; color:#D8A24A;"
                        >
                            Register
                        </button>

                        <div class="mt-4 text-sm" style="color: rgba(47,93,70,0.78);">
                            Already have an account?
                            <a href="{{ route('login') }}"
                               class="font-semibold underline underline-offset-4"
                               style="color:#D8A24A;">
                                Login
                            </a>
                        </div>

                        <a href="/"
                           class="mt-2 text-xs underline underline-offset-4"
                           style="color: rgba(47,93,70,0.70);">
                            ← Back to Welcome
                        </a>
                    </div>
                </form>
            </div>
        </section>

        <footer class="mt-8 text-center text-xs" style="color: rgba(47,93,70,0.75);">
            © {{ date('Y') }} Bru<i>Save</i>
        </footer>
    </div>
</main>
</body>
</html>