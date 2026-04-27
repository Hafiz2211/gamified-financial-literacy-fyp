<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us • BruSave</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
        .success-message {
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .contact-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .contact-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #D8A24A !important;
            box-shadow: 0 0 0 3px rgba(216,162,74,0.15);
        }

        /* Style for readonly email field */
        input[readonly] {
            background-color: #f3f4f6;
            cursor: default;
            color: #6b7280;
        }
    </style>
</head>

<body class="text-slate-900" style="background:#F6F1E6;">
@php
    $user = auth()->user();
    $userName = $user->name ?? 'Guest';
    $GREEN = '#2F5D46';
    $GOLD  = '#D8A24A';
    $BG    = '#F6F1E6';
    $CARD  = '#FFFBF2';
    $active = 'contact';

    $nav = [
        ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard','icon'=>'🏠'],
        ['key'=>'learn','label'=>'Learn','href'=>'/learn','icon'=>'📖'],
        ['key'=>'quiz','label'=>'Quiz','href'=>'/quiz','icon'=>'❓'],
        ['key'=>'track','label'=>'Track Spending','href'=>'/track-spending','icon'=>'🧾'],
        ['key'=>'progress','label'=>'Achievement','href'=>'/progress','icon'=>'🏆'],
        ['key'=>'town','label'=>'Town','href'=>'/town','icon'=>'🏘️'],
        ['key'=>'contact','label'=>'Contact Us','href'=>'/contact','icon'=>'📬'],
    ];
@endphp

<div class="app-container">
    @include('partials.sidebar', ['nav' => $nav, 'active' => $active, 'GREEN' => $GREEN, 'GOLD' => $GOLD])

    <div class="main-content">
        <div style="display: flex; justify-content: flex-end; padding: 20px 24px 0;">
            @include('components.profile-dropdown', ['user' => auth()->user()])
        </div>
        
        <div style="max-width:1200px; margin:0 auto; padding:32px 24px;">
            {{-- Success Message --}}
            @if(session('success'))
                <div id="successMessage" class="success-message mb-6 p-4 rounded-xl" 
                     style="background: {{ $GREEN }}; color: white;">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-xl">
                            ✅
                        </div>
                        <div class="flex-1">
                            <div class="font-bold">Message Sent Successfully!</div>
                            <div class="text-sm text-white/90">{{ session('success') }}</div>
                        </div>
                        <button onclick="document.getElementById('successMessage').remove()" 
                                class="w-8 h-8 rounded-full hover:bg-white/20 transition">✕</button>
                    </div>
                </div>
            @endif

            {{-- Main Card - matching dashboard style --}}
            <section class="rounded-2xl border shadow-lg p-6 md:p-7"
                     style="border-color: rgba(47,93,70,0.16); background: {{ $CARD }};">
                
                {{-- Header --}}
                <div class="text-center mb-6">
                    <div class="text-6xl mb-4">📬</div>
                    <h1 class="text-3xl md:text-4xl font-extrabold" style="color: {{ $GREEN }};">Contact Us</h1>
                    <p class="mt-3 text-base max-w-md mx-auto" style="color: rgba(47,93,70,0.75); line-height: 1.5;">
                        We'd love to hear from you! Whether you have a question, feedback or just want to say hi.
                    </p>
                </div>

                {{-- Gold line --}}
                <div class="w-24 h-1 rounded-full mx-auto mb-8" style="background: {{ $GOLD }};"></div>

                {{-- 3 CARDS SIDE BY SIDE - matching dashboard stat cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    {{-- Email Card --}}
                    <div class="rounded-2xl border p-5 shadow-sm contact-card"
                         style="border-color: rgba(47,93,70,0.16); background: {{ $CARD }};">
                        <div class="flex flex-col items-center text-center">
                            <div class="h-12 w-12 rounded-xl flex items-center justify-center text-2xl mb-3 border"
                                 style="background: rgba(216,162,74,0.20); border-color: rgba(216,162,74,0.40);">
                                📧
                            </div>
                            <div class="text-xs font-semibold tracking-wide uppercase mb-1" style="color: rgba(47,93,70,0.65);">Email</div>
                            <a href="mailto:brusavesite@gmail.com" 
                               class="font-medium hover:underline text-sm" style="color: {{ $GREEN }};">
                                brusavesite@gmail.com
                            </a>
                        </div>
                    </div>

                    {{-- Phone / WhatsApp Card - FIXED LINK --}}
                    <div class="rounded-2xl border p-5 shadow-sm contact-card"
                         style="border-color: rgba(47,93,70,0.16); background: {{ $CARD }};">
                        <div class="flex flex-col items-center text-center">
                            <div class="h-12 w-12 rounded-xl flex items-center justify-center text-2xl mb-3 border"
                                 style="background: rgba(216,162,74,0.20); border-color: rgba(216,162,74,0.40);">
                                📱
                            </div>
                            <div class="text-xs font-semibold tracking-wide uppercase mb-1" style="color: rgba(47,93,70,0.65);">Phone / WhatsApp</div>
                            <a href="https://wa.me/6737201634" 
                               target="_blank"
                               class="font-medium hover:underline text-sm" style="color: {{ $GREEN }};">
                                +673 720 1634
                            </a>
                        </div>
                    </div>

                    {{-- Instagram Card --}}
                    <div class="rounded-2xl border p-5 shadow-sm contact-card"
                         style="border-color: rgba(47,93,70,0.16); background: {{ $CARD }};">
                        <div class="flex flex-col items-center text-center">
                            <div class="h-12 w-12 rounded-xl flex items-center justify-center text-2xl mb-3 border"
                                 style="background: rgba(216,162,74,0.20); border-color: rgba(216,162,74,0.40);">
                                📷
                            </div>
                            <div class="text-xs font-semibold tracking-wide uppercase mb-1" style="color: rgba(47,93,70,0.65);">Instagram</div>
                            <a href="https://www.instagram.com/brusave.bn" 
                               target="_blank"
                               class="font-medium hover:underline text-sm" style="color: {{ $GREEN }};">
                                @brusave.bn
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Contact Form BELOW the cards --}}
                <div class="rounded-xl p-6 border"
                     style="border-color: rgba(47,93,70,0.16); background: {{ $CARD }};">
                    <h2 class="text-xl font-extrabold mb-6 flex items-center justify-center gap-2" style="color: {{ $GOLD }};">
                        <span class="text-2xl">✏️</span> Send us a message
                    </h2>
                    
                    <form method="POST" action="{{ route('contact.submit') }}">
                        @csrf
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold mb-2" style="color: {{ $GREEN }};">
                                    Your Name <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="name" required 
                                       value="{{ old('name', $userName) }}"
                                       class="w-full rounded-xl border px-4 py-3 focus:outline-none"
                                       style="border-color: rgba(47,93,70,0.18); background: white;">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2" style="color: {{ $GREEN }};">
                                    Email Address <span class="text-red-400">*</span>
                                </label>
                                <input type="email" name="email" required
                                       value="{{ old('email', $user->email ?? '') }}"
                                       readonly
                                       class="w-full rounded-xl border px-4 py-3 focus:outline-none"
                                       style="border-color: rgba(47,93,70,0.18); background: #f3f4f6; color: #6b7280; cursor: default;">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs mt-1" style="color: rgba(47,93,70,0.55);">Email address is locked and cannot be changed.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2" style="color: {{ $GREEN }};">
                                    Your Message <span class="text-red-400">*</span>
                                </label>
                                <textarea name="message" rows="5" required
                                          class="w-full rounded-xl border px-4 py-3 focus:outline-none resize-none"
                                          style="border-color: rgba(47,93,70,0.18); background: white;"
                                          placeholder="How can we help you?">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                    class="w-full text-white py-3 rounded-lg font-semibold transition-all hover:opacity-90 mt-4"
                                    style="background: {{ $GREEN }};">
                                Send Message 💌
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Help Section --}}
                <div class="mt-6 rounded-xl p-5 border"
                     style="border-color: rgba(47,93,70,0.16); background: {{ $CARD }};">
                    <h3 class="font-semibold mb-3 flex items-center justify-center gap-2" style="color: {{ $GREEN }};">
                        <span class="text-xl">💡</span> What can you contact us about?
                    </h3>
                    <div class="grid sm:grid-cols-3 gap-3">
                        <div class="flex items-center justify-center gap-2 text-sm" style="color: rgba(47,93,70,0.75);">
                            <span class="text-lg">❓</span> Questions about BruSave
                        </div>
                        <div class="flex items-center justify-center gap-2 text-sm" style="color: rgba(47,93,70,0.75);">
                            <span class="text-lg">💡</span> Feedback or suggestions
                        </div>
                        <div class="flex items-center justify-center gap-2 text-sm" style="color: rgba(47,93,70,0.75);">
                            <span class="text-lg">🐛</span> Technical issues or bugs
                        </div>
                    </div>
                </div>
            </section>

            <footer class="text-center text-xs pt-8 pb-2" style="color: rgba(47,93,70,0.75);">
                © {{ date('Y') }} Bru<i>Save</i>
            </footer>
        </div>
    </div>
</div>

<script>
    setTimeout(function() {
        let msg = document.getElementById('successMessage');
        if (msg) {
            msg.style.transition = 'opacity 0.3s';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 300);
        }
    }, 5000);
</script>
</body>
</html>