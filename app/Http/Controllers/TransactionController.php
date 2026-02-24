<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\DailyStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * Display a listing of user's transactions.
     */
    public function index(Request $request)
    {
        $transactions = $request->user()
            ->transactions()
            ->latest()
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'category' => $transaction->category,
                    'date' => $transaction->date,
                    'note' => $transaction->note,
                    'photo_url' => $transaction->photo_path ? Storage::url($transaction->photo_path) : null,
                    'xp_earned' => $transaction->xp_earned,
                    'coins_earned' => $transaction->coins_earned,
                    'full_reward' => $transaction->full_reward,
                    'created_at' => $transaction->created_at->toDateTimeString(),
                ];
            });

        return response()->json($transactions);
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        // Get today's transaction count
        $todayCount = $user->transactions()
            ->whereDate('created_at', $today)
            ->count();

        // Calculate rewards
        $dailyLimit = 10;
        $isFullReward = $todayCount < $dailyLimit;
        
        $xpReward = $isFullReward ? 10 : 1;
        $coinReward = $isFullReward ? 10 : 1;

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('transaction-photos', 'public');
        }

        // Create transaction
        $transaction = $user->transactions()->create([
            'type' => $request->type,
            'amount' => $request->amount,
            'category' => $request->category,
            'date' => $request->date,
            'note' => $request->note,
            'photo_path' => $photoPath,
            'xp_earned' => $xpReward,
            'coins_earned' => $coinReward,
            'daily_count' => $todayCount + 1,
            'full_reward' => $isFullReward,
        ]);

        // Update daily stats
        DailyStat::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['transaction_count' => $todayCount + 1]
        );

        // Update user totals
        $user->xp += $xpReward;
        $user->coins += $coinReward;
        $user->save();

        // Check for level up - FIXED
        $levelUpData = $this->checkLevelUp($user);

        return response()->json([
            'success' => true,
            'message' => $isFullReward 
                ? "✅ +10 XP, +10 coins! (First 10 bonus)"
                : "📝 +1 XP, +1 coin (Daily limit reached)",
            'transaction' => $transaction,
            'level_up' => $levelUpData['leveled_up'],
            'new_level' => $levelUpData['new_level'] ?? null,
            'user' => [
                'xp' => $user->xp,
                'coins' => $user->coins,
                'level' => $user->level
            ]
        ]);
    }

    /**
     * Remove the specified transaction.
     */
    public function destroy(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($transaction->photo_path) {
            Storage::disk('public')->delete($transaction->photo_path);
        }

        $transaction->delete();

        return response()->json(['success' => true, 'message' => 'Transaction deleted']);
    }

    /**
     * Check if user levels up - FIXED VERSION
     */
    private function checkLevelUp($user)
    {
        $leveledUp = false;
        $oldLevel = $user->level;
        
        // Keep leveling up while enough XP
        while (true) {
            $xpNeededForNextLevel = 200 + ($user->level * 100);
            
            if ($user->xp >= $xpNeededForNextLevel) {
                $user->level += 1;
                $user->xp = $user->xp - $xpNeededForNextLevel;
                $leveledUp = true;
            } else {
                break;
            }
        }
        
        if ($leveledUp) {
            $user->save();
            
            // Store notification in session with put() instead of flash()
            session()->put('level_up', [
                'old_level' => $oldLevel,
                'new_level' => $user->level
            ]);
        }
        
        return [
            'leveled_up' => $leveledUp,
            'new_level' => $user->level
        ];
    }
}