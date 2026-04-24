<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subscription • BruSave</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
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
        .plan-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .plan-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }
        .check-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            background: #2F5D46;
            color: #D8A24A;
            border-radius: 50%;
            font-size: 12px;
            margin-right: 10px;
        }

        /* 🔴 FIXED: Mobile responsive for cards */
        @media (max-width: 768px) {
            .cards-container {
                flex-direction: column !important;
            }
            .plan-card {
                width: 100% !important;
                margin-bottom: 20px;
            }
            h1 {
                font-size: 32px !important;
            }
        }
    </style>
</head>

<body class="text-slate-900" style="background:#F6F1E6; margin:0; height:100vh; overflow:hidden;">

@php
    $user = auth()->user();
    $GREEN = '#2F5D46';
    $GOLD = '#D8A24A';
    $CARD = '#FFFBF2';
    $active = '';
    
    $nav = [
        ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard','icon'=>'🏠'],
        ['key'=>'learn','label'=>'Learn','href'=>'/learn','icon'=>'📖'],
        ['key'=>'quiz','label'=>'Quiz','href'=>'/quiz','icon'=>'❓'],
        ['key'=>'track','label'=>'Track Spending','href'=>'/track-spending','icon'=>'🧾'],
        ['key'=>'progress','label'=>'Achievement','href'=>'/progress','icon'=>'🏆'],
        ['key'=>'town','label'=>'Town','href'=>'/town','icon'=>'🏘️'],
        ['key'=>'contact','label'=>'Contact Us','href'=>'/contact','icon'=>'📬'],
    ];

    $isAlreadyPremium = $user->isPremium();
    $expiryDate = $user->premium_until ? date('F j, Y', strtotime($user->premium_until)) : 'N/A';
    $hasActiveSubscription = $user->subscription_type !== null;
    $planType = $user->subscription_type === 'yearly' ? 'Yearly Plan' : 'Monthly Plan';
@endphp

<div class="app-container">
    @include('partials.sidebar', ['nav' => $nav, 'active' => $active, 'GREEN' => $GREEN, 'GOLD' => $GOLD])

    <div class="main-content">
        <div style="display: flex; justify-content: flex-end; padding: 20px 24px 0;">
            @include('components.profile-dropdown', ['user' => auth()->user()])
        </div>

        <div style="max-width:1200px; margin:0 auto; padding:32px 24px;">
            {{-- Error/Success Messages --}}
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl text-center" style="background: {{ $GREEN }}20; border: 1px solid {{ $GOLD }};">
                    <span style="color: {{ $GREEN }};">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl text-center" style="background: rgba(180,60,60,0.1); border: 1px solid rgba(180,60,60,0.3);">
                    <span style="color: #b43c3c;">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Header --}}
            <section style="text-align: center; margin-bottom: 48px;">
                <h1 style="font-size:42px; font-weight:800; color:{{ $GREEN }}; margin-bottom:16px;">
                    ✨ Go Premium ✨
                </h1>
                <p style="font-size:18px; color:rgba(47,93,70,0.85); max-width:600px; margin:0 auto;">
                    Unlock exclusive rewards while building better financial habits.
                </p>
            </section>

            {{-- Current Plan Section --}}
            @if($isAlreadyPremium)
                <div style="max-width:600px; margin:0 auto 32px auto; padding:24px; border-radius:24px; text-align:center; background:{{ $GREEN }}; color:{{ $GOLD }};">
                    <span style="font-size:32px;">👑</span>
                    
                    @if($hasActiveSubscription)
                        <p style="margin-top:12px; font-weight:700; font-size:18px;">You are on the {{ $planType }}</p>
                        <p style="font-size:14px; margin-top:8px;">Your premium access is active until <strong>{{ $expiryDate }}</strong></p>
                        <p style="font-size:12px; margin-top:12px; opacity:0.8;">Cancel anytime — you'll keep access until your expiry date.</p>
                        
                        <form method="POST" action="{{ route('subscription.cancel') }}" class="mt-4">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to cancel? You will keep premium access until {{ $expiryDate }}.')"
                                    style="padding:10px 24px; border-radius:40px; font-size:14px; font-weight:600; background:rgba(255,255,255,0.15); color:#ffcccc; border:1px solid rgba(255,255,255,0.3); cursor:pointer; transition:all 0.2s;">
                                Cancel Subscription
                            </button>
                        </form>
                    @else
                        <p style="margin-top:12px; font-weight:700; font-size:18px;">⚠️ Subscription Cancelled</p>
                        <p style="font-size:14px; margin-top:8px;">You still have premium access until <strong>{{ $expiryDate }}</strong></p>
                        <p style="font-size:12px; margin-top:12px; opacity:0.8;">Your subscription will not renew. After {{ $expiryDate }}, premium features will be locked.</p>
                        
                        <div style="margin-top:16px; padding:8px 16px; border-radius:40px; display:inline-block; background:rgba(0,0,0,0.2); font-size:13px;">
                            ✅ Cancelled — Access until {{ $expiryDate }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- Two Plan Cards --}}
            <div class="cards-container" style="display: flex; flex-direction: row; gap: 32px; max-width: 1000px; margin: 0 auto;">
                
                {{-- Monthly Plan Card --}}
                <div class="plan-card" style="flex: 1; background:{{ $CARD }}; border-radius:32px; border:1px solid rgba(47,93,70,0.16); padding:32px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
                    <div style="text-align: center; margin-bottom: 24px;">
                        <div style="font-size:48px; margin-bottom:8px;">📱</div>
                        <h2 style="font-size:28px; font-weight:800; color:{{ $GREEN }};">Monthly Plan</h2>
                        <div style="margin-top:12px;">
                            <span style="font-size:36px; font-weight:800; color:{{ $GOLD }};">BND 3</span>
                            <span style="color:rgba(47,93,70,0.65);">/month</span>
                        </div>
                        <p style="margin-top:12px; color:rgba(47,93,70,0.75);">Perfect for flexibility and trying out the benefits.</p>
                    </div>

                    <ul style="list-style: none; padding: 0; margin: 24px 0;">
                        <li style="margin-bottom:12px; display: flex; align-items: center;"><span class="check-mark">✓</span> Premium access to lessons and quiz pages</li>
                        <li style="margin-bottom:12px; display: flex; align-items: center;"><span class="check-mark">✓</span> Earn 100 XP from lessons and quiz categories</li>
                        <li style="margin-bottom:12px; display: flex; align-items: center;"><span class="check-mark">✓</span> Exclusive partner deals</li>
                        <li style="margin-bottom:12px; display: flex; align-items: center;"><span class="check-mark">✓</span> Access to purchase premium furniture</li>
                    </ul>

                    <div style="text-align: center; margin-top: 24px;">
                        <p style="font-size:12px; color:rgba(47,93,70,0.65); margin-bottom:16px;">Pay monthly, cancel anytime.</p>
                        
                        @if($isAlreadyPremium && $hasActiveSubscription)
                            <button disabled style="width:100%; padding:14px; border-radius:16px; font-weight:600; background:rgba(47,93,70,0.22); color:rgba(47,93,70,0.5); cursor:not-allowed;">
                                Current Plan
                            </button>
                        @elseif($isAlreadyPremium && !$hasActiveSubscription)
                            <button disabled style="width:100%; padding:14px; border-radius:16px; font-weight:600; background:rgba(47,93,70,0.22); color:rgba(47,93,70,0.5); cursor:not-allowed;">
                                Previously Subscribed
                            </button>
                        @else
                            <form method="POST" action="{{ route('subscribe.monthly') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="flex items-center justify-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="accept_terms" required class="rounded" style="accent-color: {{ $GREEN }};">
                                        <span class="text-xs" style="color: rgba(47,93,70,0.7);">
                                            I agree to the 
                                            <button type="button" onclick="openTermsModal()" class="font-semibold hover:underline" style="color: {{ $GOLD }};">
                                                Terms & Conditions
                                            </button>
                                        </span>
                                    </label>
                                </div>
                                <button type="submit" style="width:100%; padding:14px; border-radius:16px; font-weight:600; background:{{ $GREEN }}; color:{{ $GOLD }}; border:none; cursor:pointer; transition:opacity 0.2s;">
                                    Subscribe Monthly
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Yearly Plan Card --}}
                <div class="plan-card" style="flex: 1; background:{{ $CARD }}; border-radius:32px; border:2px solid {{ $GOLD }}; padding:32px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); position: relative;">
                    <div style="position: absolute; top:-12px; right:24px; background:{{ $GOLD }}; color:{{ $GREEN }}; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                        BEST VALUE
                    </div>
                    
                    <div style="text-align: center; margin-bottom: 24px;">
                        <div style="font-size:48px; margin-bottom:8px;">⭐</div>
                        <h2 style="font-size:28px; font-weight:800; color:{{ $GREEN }};">Yearly Plan</h2>
                        <div style="margin-top:12px;">
                            <span style="font-size:36px; font-weight:800; color:{{ $GOLD }};">BND 30</span>
                            <span style="color:rgba(47,93,70,0.65);">/year</span>
                        </div>
                        <p style="margin-top:12px; color:rgba(47,93,70,0.75);">Best value for long-term users.</p>
                        <p style="margin-top:8px; font-size:14px; color:{{ $GOLD }}; font-weight:600;">Save BND 6 compared to monthly</p>
                    </div>

                    <ul style="list-style: none; padding: 0; margin: 24px 0;">
                        <li style="margin-bottom:12px; display: flex; align-items: center;"><span class="check-mark">✓</span> Premium access to lessons and quiz pages</li>
                        <li style="margin-bottom:12px; display: flex; align-items: center;"><span class="check-mark">✓</span> Earn 100 XP from lessons and quiz categories</li>
                        <li style="margin-bottom:12px; display: flex; align-items: center;"><span class="check-mark">✓</span> Exclusive partner deals</li>
                        <li style="margin-bottom:12px; display: flex; align-items: center;"><span class="check-mark">✓</span> Access to purchase premium furniture</li>
                        <li style="margin-bottom:12px; display: flex; align-items: center;"><span class="check-mark">✓</span> Priority access to new features and partner rewards</li>
                    </ul>

                    <div style="text-align: center; margin-top: 24px;">
                        <p style="font-size:12px; color:rgba(47,93,70,0.65); margin-bottom:16px;">One payment for full year access.</p>
                        
                        @if($isAlreadyPremium && $hasActiveSubscription)
                            <button disabled style="width:100%; padding:14px; border-radius:16px; font-weight:600; background:rgba(47,93,70,0.22); color:rgba(47,93,70,0.5); cursor:not-allowed;">
                                Current Plan
                            </button>
                        @elseif($isAlreadyPremium && !$hasActiveSubscription)
                            <button disabled style="width:100%; padding:14px; border-radius:16px; font-weight:600; background:rgba(47,93,70,0.22); color:rgba(47,93,70,0.5); cursor:not-allowed;">
                                Previously Subscribed
                            </button>
                        @else
                            <form method="POST" action="{{ route('subscribe.yearly') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="flex items-center justify-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="accept_terms" required class="rounded" style="accent-color: {{ $GREEN }};">
                                        <span class="text-xs" style="color: rgba(47,93,70,0.7);">
                                            I agree to the 
                                            <button type="button" onclick="openTermsModal()" class="font-semibold hover:underline" style="color: {{ $GOLD }};">
                                                Terms & Conditions
                                            </button>
                                        </span>
                                    </label>
                                </div>
                                <button type="submit" style="width:100%; padding:14px; border-radius:16px; font-weight:600; background:{{ $GREEN }}; color:{{ $GOLD }}; border:none; cursor:pointer; transition:opacity 0.2s;">
                                    Subscribe Yearly
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Why Go Premium Section --}}
            <section style="margin-top: 64px; text-align: center; max-width: 700px; margin-left: auto; margin-right: auto;">
                <div style="width: 80px; height: 4px; background: {{ $GOLD }}; margin: 0 auto 24px auto; border-radius: 2px;"></div>
                <h2 style="font-size:28px; font-weight:800; color:{{ $GREEN }}; margin-bottom:16px;">Why Go Premium</h2>
                <p style="font-size:16px; color:rgba(47,93,70,0.85);">
                    Enjoy real-world rewards while improving your knowledge and earning XP, all in one membership.
                </p>
            </section>

            <footer style="text-align:center; font-size:12px; padding-top:48px; padding-bottom:32px; color:rgba(47,93,70,0.75);">
                © {{ date('Y') }} Bru<i>Save</i>
            </footer>
        </div>
    </div>
</div>

{{-- Terms & Conditions Modal --}}
<div id="termsModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4" onclick="closeTermsModal(event)">
    <div class="rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto p-6" style="background: {{ $CARD }};" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-extrabold" style="color: {{ $GREEN }};">Terms & Conditions</h2>
            <button onclick="closeTermsModal()" class="text-2xl hover:opacity-70" style="color: {{ $GOLD }};">×</button>
        </div>
        <div class="space-y-4 text-sm" style="color: rgba(47,93,70,0.8);">
            <p class="font-bold">By proceeding with payment, you acknowledge and agree to the following terms:</p>
            <div><p class="font-semibold" style="color: {{ $GREEN }};">1. No Refund Policy</p><p>All payments made are final and non-refundable under any circumstances.</p></div>
            <div><p class="font-semibold" style="color: {{ $GREEN }};">2. Commitment</p><p>Once payment is confirmed, your subscription is secured and cannot be transferred or canceled.</p></div>
            <div><p class="font-semibold" style="color: {{ $GREEN }};">3. Changes by Provider</p><p>We reserve the right to make changes to pricing or features with prior notice.</p></div>
            <p class="mt-4 pt-3 border-t" style="border-color: rgba(47,93,70,0.16);">By making payment, you confirm that you have read, understood, and agreed to these Terms & Conditions.</p>
        </div>
        <div class="mt-6 flex justify-end"><button onclick="closeTermsModal()" class="px-4 py-2 rounded-lg font-semibold" style="background: {{ $GREEN }}; color: white;">Close</button></div>
    </div>
</div>

<script>
    function openTermsModal() { document.getElementById('termsModal').style.display = 'flex'; }
    function closeTermsModal(event) { if (!event || event.target === document.getElementById('termsModal') || !event) { document.getElementById('termsModal').style.display = 'none'; } }
</script>
</body>
</html>