<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function checkoutMonthly()
    {
        $checkout = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'bnd',
                    'product_data' => [
                        'name' => 'BruSave Premium - Monthly',
                        'description' => 'Unlock premium lessons, quizzes, furniture, and exclusive partner deals. Cancel anytime.',
                    ],
                    'unit_amount' => 300,
                    'recurring' => ['interval' => 'month'],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscription'),
            'metadata' => [
                'user_id' => auth()->id(),
                'type' => 'monthly'
            ]
        ]);

        return redirect($checkout->url);
    }

    public function checkoutYearly()
    {
        $checkout = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'bnd',
                    'product_data' => [
                        'name' => 'BruSave Premium - Yearly',
                        'description' => 'Everything in Monthly, plus priority access to new features and partner rewards. Save $6 with yearly billing!',
                    ],
                    'unit_amount' => 3000,
                    'recurring' => ['interval' => 'year'],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscription'),
            'metadata' => [
                'user_id' => auth()->id(),
                'type' => 'yearly'
            ]
        ]);

        return redirect($checkout->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        
        if (!$sessionId) {
            return redirect()->route('subscription')->with('error', 'Invalid session');
        }

        $session = Session::retrieve($sessionId);
        
        $user = auth()->user();
        
        if ($session->metadata->type == 'yearly') {
            $user->premium_until = now()->addYear();
        } else {
            $user->premium_until = now()->addMonth();
        }
        
        $user->is_premium = true;
        $user->subscription_type = $session->metadata->type;
        $user->save();

        return redirect()->route('subscription')->with('success', '🎉 Welcome to Premium! You now have access to all premium content!');
    }

    /**
     * Cancel subscription but KEEP access until premium_until expires
     */
    public function cancel()
    {
        $user = auth()->user();
        
        // Remove subscription type (stops auto-renewal)
        $user->subscription_type = null;
        $user->save();

        // Get expiry date safely
        $expiryDate = $user->premium_until ? date('F j, Y', strtotime($user->premium_until)) : 'unknown';
        
        return redirect()->route('subscription')->with('success', 
            "Your subscription has been cancelled. You will have premium access until {$expiryDate}."
        );
    }
}