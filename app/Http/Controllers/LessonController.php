<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Map string IDs to actual lesson database IDs (FREE lessons 7-12, PREMIUM 13-18)
        $idMap = [
            'needs-vs-wants' => 7,
            'budgeting-simple' => 8,
            'income-vs-expense' => 9,
            'saving-goals' => 10,
            'tracking-spending' => 11,
            'emergency-fund' => 12,
            // PREMIUM lessons (13-18)
            'opportunity-cost' => 13,
            'delayed-gratification' => 14,
            'fixed-vs-variable' => 15,
            'financial-prioritization' => 16,
            'digital-spending' => 17,
            'peer-influence' => 18
        ];
        
        $numericLessonId = $idMap[$lessonId] ?? null;
        
        if (!$numericLessonId) {
            return response()->json(['message' => 'Lesson not found'], 404);
        }

        // Check if already completed
        $alreadyCompleted = DB::table('user_lessons')
            ->where('user_id', $user->id)
            ->where('lesson_id', $numericLessonId)
            ->exists();
        
        if ($alreadyCompleted) {
            return response()->json([
                'message' => 'Lesson already completed',
                'already_completed' => true
            ], 400);
        }

        // Find the lesson in database
        $lesson = Lesson::find($numericLessonId);

        if (!$lesson) {
            return response()->json(['message' => 'Lesson not found in database'], 404);
        }

        // Determine if this is a premium lesson
        $isPremiumLesson = $lesson->is_premium ?? false;
        
        if ($isPremiumLesson && !$user->isPremium()) {
            return response()->json([
                'message' => 'This lesson requires a premium subscription. Upgrade to access!'
            ], 403);
        }
        
        // Set rewards based on premium status
        $xpReward = $isPremiumLesson ? 100 : 50;
        $coinReward = $isPremiumLesson ? 100 : 50;

        // Store old level BEFORE updating
        $oldLevel = $user->level;
        $oldXP = $user->xp;
        
        // Add XP and coins
        $user->xp += $xpReward;
        $user->coins += $coinReward;
        
        // 🔴 KEEP YOUR ORIGINAL LEVEL UPDATE METHOD
        $user->updateLevel();  // Uses your custom thresholds!
        
        $user->save();
        
        // Check for level up
        $leveledUp = $user->level > $oldLevel;
        
        // Insert into user_lessons
        DB::table('user_lessons')->insert([
            'user_id' => $user->id,
            'lesson_id' => $numericLessonId,
            'xp_earned' => $xpReward,
            'coins_earned' => $coinReward,
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Flash level up notification
        if ($leveledUp) {
            session()->flash('level_up', ['new_level' => $user->level]);
            
            Log::info('✅ LEVEL UP', [
                'user_id' => $user->id,
                'old_level' => $oldLevel,
                'new_level' => $user->level,
                'old_xp' => $oldXP,
                'new_xp' => $user->xp
            ]);
        }

        // Get updated progress count
        $completedCount = DB::table('user_lessons')
            ->where('user_id', $user->id)
            ->count();
        $totalLessons = Lesson::count();

        return response()->json([
            'success' => true,
            'message' => $leveledUp
                ? "🎉 Level Up! You reached Level {$user->level}!\n+{$xpReward} XP, +{$coinReward} coins!"
                : ($isPremiumLesson 
                    ? "🎉 Premium Lesson completed! +{$xpReward} XP, +{$coinReward} coins!"
                    : "✅ Lesson completed! +{$xpReward} XP, +{$coinReward} coins"),
            'xp_earned' => $xpReward,
            'coins_earned' => $coinReward,
            'is_premium' => $isPremiumLesson,
            'level_up' => $leveledUp,
            'new_level' => $leveledUp ? $user->level : null,
            'user' => [
                'xp' => $user->xp,
                'coins' => $user->coins,
                'level' => $user->level
            ],
            'progress' => [
                'completed_count' => $completedCount,
                'total_lessons' => $totalLessons
            ]
        ]);
    }

    /**
     * Get user's lesson progress
     */
    public function progress(Request $request)
    {
        $user = $request->user();
        $completedCount = DB::table('user_lessons')
            ->where('user_id', $user->id)
            ->count();
        $totalLessons = Lesson::count();

        return response()->json([
            'completed_count' => $completedCount,
            'total_lessons' => $totalLessons
        ]);
    }
}