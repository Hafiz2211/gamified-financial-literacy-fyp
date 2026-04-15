<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delete Account • BruSave</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="text-slate-900" style="background:#F6F1E6; margin:0;">
@php
    $GREEN = '#2F5D46';
    $GOLD = '#D8A24A';
    $BG = '#F6F1E6';
    $CARD = '#FFFBF2';
    $active = 'profile';
    
    $nav = [
        ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard','icon'=>'🏠'],
        ['key'=>'learn','label'=>'Learn','href'=>'/learn','icon'=>'📖'],
        ['key'=>'quiz','label'=>'Quiz','href'=>'/quiz','icon'=>'❓'],
        ['key'=>'track','label'=>'Track Spending','href'=>'/track-spending','icon'=>'🧾'],
        ['key'=>'progress','label'=>'Achievement','href'=>'/progress','icon'=>'🏆'],
        ['key'=>'town','label'=>'Town','href'=>'/town','icon'=>'🏘️'],
    ];
@endphp

<div class="app-container" style="display:flex; height:100vh; width:100vw; overflow:hidden;">
    {{-- Sidebar --}}
    @include('partials.sidebar', ['nav' => $nav, 'active' => $active, 'GREEN' => $GREEN, 'GOLD' => $GOLD])

    {{-- Main Content --}}
    <div class="main-content" style="flex:1; overflow-y:auto; background:#F6F1E6;">
        {{-- Profile dropdown in top right --}}
        <div style="display: flex; justify-content: flex-end; padding: 20px 24px 0;">
            @include('components.profile-dropdown', ['user' => auth()->user()])
        </div>
        
        <div style="max-width:600px; margin:0 auto; padding:32px 24px;">
            
            <div class="mb-6">
                <a href="{{ route('profile.edit') }}" class="text-sm hover:underline" style="color: {{ $GREEN }};">
                    ← Back to Profile
                </a>
                <h1 class="text-3xl font-bold mt-2" style="color: #b43c3c;">Delete Account</h1>
            </div>

            <div class="rounded-3xl border p-6" style="background:{{ $CARD }}; border-color: #b43c3c;">
                <div class="text-center mb-6">
                    <span class="text-6xl">⚠️</span>
                    <h2 class="text-xl font-bold mt-2" style="color: #b43c3c;">This action cannot be undone!</h2>
                    <p class="text-sm mt-2" style="color: rgba(47,93,70,0.7);">
                        All your data will be permanently deleted:
                    </p>
                </div>

                <ul class="mb-6 space-y-2 text-sm" style="color: rgba(47,93,70,0.8);">
                    <li class="flex items-center gap-2"><span>❌</span> Profile information</li>
                    <li class="flex items-center gap-2"><span>❌</span> Lessons completed</li>
                    <li class="flex items-center gap-2"><span>❌</span> Quiz attempts and scores</li>
                    <li class="flex items-center gap-2"><span>❌</span> Spending records</li>
                    <li class="flex items-center gap-2"><span>❌</span> Room furniture and decorations</li>
                    <li class="flex items-center gap-2"><span>❌</span> All progress and achievements</li>
                </ul>

                <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                    @csrf
                    @method('DELETE')

                    <div>
                        <label class="block text-sm font-semibold mb-1" style="color:{{ $GREEN }};">Confirm Password</label>
                        <input type="password" 
                               name="password" 
                               required
                               autocomplete="current-password"
                               class="w-full rounded-xl border px-4 py-3 focus:outline-none"
                               style="border-color: rgba(47,93,70,0.18); background: white;">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" 
                               name="confirmation" 
                               id="confirmation" 
                               required
                               class="rounded border-gray-300">
                        <label for="confirmation" class="text-sm" style="color: rgba(47,93,70,0.8);">
                            I understand that this action is permanent and cannot be undone
                        </label>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" 
                                class="px-8 py-3 rounded-xl font-semibold transition hover:opacity-90"
                                style="background: #b43c3c; color: white;">
                            Permanently Delete Account
                        </button>
                        <a href="{{ route('profile.edit') }}" 
                           class="px-8 py-3 rounded-xl font-semibold border transition hover:opacity-90"
                           style="border-color: rgba(47,93,70,0.2); color: {{ $GREEN }};">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
            <footer class="text-center text-xs pt-8 pb-2" style="color: rgba(47,93,70,0.75);">
                © {{ date('Y') }} Bru<i>Save</i>
            </footer>
        </div>
    </div>
</div>
</body>
</html>