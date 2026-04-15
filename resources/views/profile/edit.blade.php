<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile • BruSave</title>
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
    @include('partials.sidebar', ['nav' => $nav, 'active' => $active, 'GREEN' => $GREEN, 'GOLD' => $GOLD])

    <div class="main-content" style="flex:1; overflow-y:auto; background:#F6F1E6;">
        <div style="display: flex; justify-content: flex-end; padding: 20px 24px 0;">
            @include('components.profile-dropdown', ['user' => auth()->user()])
        </div>
        
        <div style="max-width:800px; margin:0 auto; padding:32px 24px;">
            
            <div class="mb-6">
                <a href="{{ route('dashboard') }}" class="text-sm hover:underline" style="color: {{ $GREEN }};">
                    ← Back to Dashboard
                </a>
                <h1 class="text-3xl font-bold mt-2" style="color:{{ $GREEN }};">Edit Profile</h1>
                <p class="text-sm mt-1" style="color: rgba(47,93,70,0.65);">Manage your account settings and preferences</p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- 1. Profile Picture --}}
                <div class="rounded-3xl border p-6" style="background:{{ $CARD }};">
                    <h2 class="text-xl font-bold mb-4" style="color:{{ $GREEN }};">Profile Picture</h2>
                    
                    <div class="flex items-center gap-6">
                        <div class="relative">
                            @if($user->profile_photo_path)
                                <img src="{{ Storage::url($user->profile_photo_path) }}" 
                                     class="h-20 w-20 rounded-full object-cover border-2"
                                     style="border-color: {{ $GOLD }};">
                            @else
                                <div class="h-20 w-20 rounded-full flex items-center justify-center text-2xl font-bold border-2"
                                     style="background: {{ $GREEN }}; color: {{ $GOLD }}; border-color: {{ $GOLD }};">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <input type="file" 
                                   name="profile_photo" 
                                   accept="image/*"
                                   class="w-full text-sm"
                                   style="color: rgba(47,93,70,0.8);">
                            <p class="text-xs mt-1" style="color: rgba(47,93,70,0.65);">
                                Max 2MB. Square images work best.
                            </p>
                            @error('profile_photo')
                                <p class="text-xs mt-1" style="color: #b43c3c;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- 2. Basic Information --}}
                <div class="rounded-3xl border p-6" style="background:{{ $CARD }};">
                    <h2 class="text-xl font-bold mb-4" style="color:{{ $GREEN }};">Basic Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color:{{ $GREEN }};">Name</label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required
                                   autocomplete="name"
                                   class="w-full rounded-xl border px-4 py-3 focus:outline-none"
                                   style="border-color: rgba(47,93,70,0.18); background: white;">
                            @error('name')
                                <p class="text-xs mt-1" style="color: #b43c3c;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color:{{ $GREEN }};">Email</label>
                            <input type="email" 
                                   value="{{ $user->email }}" 
                                   disabled
                                   class="w-full rounded-xl border px-4 py-3 bg-gray-50 cursor-not-allowed"
                                   style="border-color: rgba(47,93,70,0.18); background: #f9fafb; color: #6b7280;">
                            <p class="text-xs mt-1" style="color: rgba(47,93,70,0.55);">Email cannot be changed</p>
                        </div>
                    </div>
                </div>

                {{-- 3. Change Password --}}
                <div class="rounded-3xl border p-6" style="background:{{ $CARD }};">
                    <h2 class="text-xl font-bold mb-4" style="color:{{ $GREEN }};">Change Password</h2>
                    <p class="text-sm mb-4" style="color: rgba(47,93,70,0.65);">Leave blank to keep current password</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color:{{ $GREEN }};">Current Password</label>
                            <input type="password" 
                                   name="current_password" 
                                   autocomplete="current-password"
                                   class="w-full rounded-xl border px-4 py-3 focus:outline-none"
                                   style="border-color: rgba(47,93,70,0.18); background: white;">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color:{{ $GREEN }};">New Password</label>
                            <input type="password" 
                                   name="new_password" 
                                   autocomplete="new-password"
                                   class="w-full rounded-xl border px-4 py-3 focus:outline-none"
                                   style="border-color: rgba(47,93,70,0.18); background: white;">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color:{{ $GREEN }};">Confirm New Password</label>
                            <input type="password" 
                                   name="new_password_confirmation" 
                                   autocomplete="new-password"
                                   class="w-full rounded-xl border px-4 py-3 focus:outline-none"
                                   style="border-color: rgba(47,93,70,0.18); background: white;">
                        </div>
                    </div>
                </div>

                {{-- 4. MUSIC SETTINGS --}}
                <div class="rounded-3xl border p-6" style="background:{{ $CARD }}; border-color: rgba(47,93,70,0.16);">
                    <h2 class="text-xl font-bold mb-4" style="color:{{ $GREEN }};">🎵 Audio Settings for Achievement and Town</h2>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-semibold" style="color:{{ $GREEN }};">Background Music</div>
                            <p class="text-xs mt-1" style="color: rgba(47,93,70,0.65);">Cozy background music for your BruSave experience</p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="musicToggle" 
                                   style="width: 20px; height: 20px; cursor: pointer; accent-color: #D8A24A;">
                            <span class="text-sm" style="color: {{ $GREEN }};">ON / OFF</span>
                        </div>
                    </div>
                    
                    {{-- Music Credit --}}
                    <div class="mt-4 pt-4 border-t text-center" style="border-color: rgba(47,93,70,0.12);">
                        <p class="text-xs" style="color: rgba(47,93,70,0.55);">
                            🎵 "Days Off" by Matrika<br>
                            Music from <a href="https://uppbeat.io/t/matrika/days-off" target="_blank" style="color: {{ $GOLD }};">Uppbeat (free for Creators!)</a>
                        </p>
                    </div>
                </div>

                {{-- 5. 🔴 DELETE ACCOUNT SECTION (MOVED FROM SIDEBAR) --}}
                <div class="rounded-3xl border p-6" style="background:{{ $CARD }}; border-color: rgba(180,60,60,0.3);">
                    <h2 class="text-xl font-bold mb-4" style="color: #b43c3c;">⚠️ Danger Zone</h2>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-semibold" style="color: #b43c3c;">Delete Account</div>
                            <p class="text-xs mt-1" style="color: rgba(47,93,70,0.65);">Permanently delete your account and all data. This action cannot be undone.</p>
                        </div>
                        
                        <a href="{{ route('profile.delete') }}" 
                           class="px-4 py-2 rounded-xl font-semibold transition hover:opacity-90"
                           style="background: #b43c3c; color: white; text-decoration: none;">
                            Delete Account
                        </a>
                    </div>
                </div>

                {{-- 6. Submit Buttons --}}
                <div class="flex gap-4">
                    <button type="submit" 
                            class="px-8 py-3 rounded-xl font-semibold transition hover:opacity-90"
                            style="background: {{ $GREEN }}; color: {{ $GOLD }};">
                        Save Changes
                    </button>
                    <a href="{{ route('dashboard') }}" 
                       class="px-8 py-3 rounded-xl font-semibold border transition hover:opacity-90"
                       style="border-color: rgba(47,93,70,0.2); color: {{ $GREEN }};">
                        Cancel
                    </a>
                </div>
            </form>

            <footer class="text-center text-xs pt-8 pb-2" style="color: rgba(47,93,70,0.75); margin-top: 40px;">
                © {{ date('Y') }} Bru<i>Save</i>
            </footer>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const musicToggle = document.getElementById('musicToggle');
        
        if (musicToggle) {
            // Default to ON for new users
            let musicEnabled = localStorage.getItem('musicEnabled');
            if (musicEnabled === null) {
                musicEnabled = 'true';
                localStorage.setItem('musicEnabled', 'true');
            }
            const isEnabled = musicEnabled === 'true';
            
            musicToggle.checked = isEnabled;
            
            // Handle toggle change
            musicToggle.addEventListener('change', function() {
                const isChecked = this.checked;
                localStorage.setItem('musicEnabled', isChecked ? 'true' : 'false');
                
                // Dispatch storage event to notify other pages
                window.dispatchEvent(new StorageEvent('storage', {
                    key: 'musicEnabled',
                    newValue: isChecked ? 'true' : 'false'
                }));
                
                console.log('Music ' + (isChecked ? 'ON' : 'OFF'));
            });
        }
    });
</script>
</body>
</html>