<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        // Store old level BEFORE updating
        $oldLevel = $user->level;
        
        // Update user totals
        $user->xp += 50;
        $user->coins += 50;
        
        // Update level based on new XP
        if (method_exists($user, 'updateLevel')) {
            $user->updateLevel();
        }
        
        $user->save();
        
        // Check for level up
        $leveledUp = $user->level > $oldLevel;
        
        // Flash for next page load
        if ($leveledUp) {
            session()->flash('level_up', ['new_level' => $user->level]);
            
            Log::info('✅ LEVEL UP NOTIFICATION SET', [
                'user_id' => $user->id,
                'old_level' => $oldLevel,
                'new_level' => $user->level
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lesson completed! +50 XP, +50 coins',
            'xp_earned' => 50,
            'coins_earned' => 50,
            'level_up' => $leveledUp,
            'new_level' => $leveledUp ? $user->level : null,
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
}