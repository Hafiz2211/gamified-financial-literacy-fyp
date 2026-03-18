<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile • BruSave</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="text-slate-900" style="background:#F6F1E6;">
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
        ['key'=>'progress','label'=>'My Room','href'=>'/progress','icon'=>'🏆'],
        ['key'=>'town','label'=>'Town','href'=>'/town','icon'=>'🏘️'],
    ];
@endphp

<div style="display:flex; height:100vh; width:100vw; overflow:hidden;">
    {{-- Sidebar --}}
    <div style="width:270px; height:100vh; background:{{ $GREEN }}; flex-shrink:0;">
        @include('partials.sidebar', ['nav' => $nav, 'active' => $active, 'GREEN' => $GREEN, 'GOLD' => $GOLD])
    </div>

    {{-- Main Content --}}
    <div style="flex:1; overflow-y:auto; padding:32px;">
        <div style="max-width:800px; margin:0 auto;">
            
            <div class="mb-6">
                <a href="{{ route('dashboard') }}" class="text-sm hover:underline" style="color: {{ $GREEN }};">
                    ← Back to Dashboard
                </a>
                <h1 class="text-3xl font-bold mt-2" style="color:{{ $GREEN }};">Edit Profile</h1>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Profile Photo --}}
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

                {{-- Basic Info --}}
                <div class="rounded-3xl border p-6" style="background:{{ $CARD }};">
                    <h2 class="text-xl font-bold mb-4" style="color:{{ $GREEN }};">Basic Information</h2>
                    
                    <div class="space-y-4">
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color:{{ $GREEN }};">Name</label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required
                                   class="w-full rounded-xl border px-4 py-3 focus:outline-none"
                                   style="border-color: rgba(47,93,70,0.18); background: white;">
                            @error('name')
                                <p class="text-xs mt-1" style="color: #b43c3c;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color:{{ $GREEN }};">Email</label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required
                                   class="w-full rounded-xl border px-4 py-3 focus:outline-none"
                                   style="border-color: rgba(47,93,70,0.18); background: white;">
                            @error('email')
                                <p class="text-xs mt-1" style="color: #b43c3c;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Change Password --}}
                <div class="rounded-3xl border p-6" style="background:{{ $CARD }};">
                    <h2 class="text-xl font-bold mb-4" style="color:{{ $GREEN }};">Change Password</h2>
                    <p class="text-sm mb-4" style="color: rgba(47,93,70,0.65);">Leave blank to keep current password</p>
                    
                    <div class="space-y-4">
                        {{-- Current Password --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color:{{ $GREEN }};">Current Password</label>
                            <input type="password" 
                                   name="current_password" 
                                   class="w-full rounded-xl border px-4 py-3 focus:outline-none"
                                   style="border-color: rgba(47,93,70,0.18); background: white;">
                        </div>

                        {{-- New Password --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color:{{ $GREEN }};">New Password</label>
                            <input type="password" 
                                   name="new_password" 
                                   class="w-full rounded-xl border px-4 py-3 focus:outline-none"
                                   style="border-color: rgba(47,93,70,0.18); background: white;">
                        </div>

                        {{-- Confirm New Password --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color:{{ $GREEN }};">Confirm New Password</label>
                            <input type="password" 
                                   name="new_password_confirmation" 
                                   class="w-full rounded-xl border px-4 py-3 focus:outline-none"
                                   style="border-color: rgba(47,93,70,0.18); background: white;">
                        </div>
                    </div>
                </div>

                {{-- Submit Buttons --}}
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
        </div>
    </div>
</div>
</body>
</html>