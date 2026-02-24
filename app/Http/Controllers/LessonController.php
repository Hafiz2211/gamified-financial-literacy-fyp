<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Complete a lesson and award XP/coins
     */
    public function complete(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|string'
        ]);

        $user = $request->user();
        $lessonId = $request->lesson_id;

        // Check if already completed
        if ($user->lessons()->where('lesson_id', $lessonId)->exists()) {
            return response()->json([
                'message' => 'Lesson already completed'
            ], 400);
        }

        // Map string IDs to actual lesson titles
        $titleMap = [
            'needs-vs-wants' => 'Needs vs Wants',
            'budgeting-simple' => 'Budgeting',
            'income-vs-expense' => 'Income vs Expense',
            'saving-goals' => 'Saving Goals',
            'tracking-spending' => 'Tracking Spending',
            'emergency-fund' => 'Emergency Fund'
        ];

        $lessonTitle = $titleMap[$lessonId] ?? null;
        
        if (!$lessonTitle) {
            return response()->json(['message' => 'Lesson not found'], 404);
        }

        // Find the lesson in database
        $lesson = Lesson::where('title', $lessonTitle)->first();

        if (!$lesson) {
            return response()->json(['message' => 'Lesson not found in database'], 404);
        }

        // Award rewards (50 XP / 50 coins) - ONE TIME ONLY
        $user->lessons()->attach($lesson->id, [
            'xp_earned' => 50,
            'coins_earned' => 50,
            'completed_at' => now()
        ]);

        // Update user totals
        $user->xp += 50;
        $user->coins += 50;
        $user->save();

        // Check for level up - FIXED
        $levelUpData = $this->checkLevelUp($user);

        return response()->json([
            'success' => true,
            'message' => 'Lesson completed! +50 XP, +50 coins',
            'xp_earned' => 50,
            'coins_earned' => 50,
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
     * Get user's lesson progress
     */
    public function progress(Request $request)
    {
        $user = $request->user();
        $completedCount = $user->lessons()->count();
        $totalLessons = Lesson::count();

        return response()->json([
            'completed_count' => $completedCount,
            'total_lessons' => $totalLessons
        ]);
    }

    /**
     * Check if user levels up - FIXED VERSION
     */
    private function checkLevelUp($user)
    {
        $leveledUp = false;
        $oldLevel = $user->level;
        $levelsGained = 0;
        
        // Keep leveling up while enough XP
        while (true) {
            $xpNeededForNextLevel = 200 + ($user->level * 100);
            
            if ($user->xp >= $xpNeededForNextLevel) {
                $user->level += 1;
                $user->xp = $user->xp - $xpNeededForNextLevel;
                $leveledUp = true;
                $levelsGained++;
            } else {
                break;
            }
        }
        
        if ($leveledUp) {
            $user->save();
            
            // Store notification in session with put() instead of flash()
            session()->put('level_up', [
                'old_level' => $oldLevel,
                'new_level' => $user->level,
                'levels_gained' => $levelsGained
            ]);
            
            \Log::info('Level up triggered', [
                'user_id' => $user->id,
                'old_level' => $oldLevel,
                'new_level' => $user->level,
                'xp_left' => $user->xp
            ]);
        }
        
        return [
            'leveled_up' => $leveledUp,
            'new_level' => $user->level,
            'xp_left' => $user->xp
        ];
    }
}