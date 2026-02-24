<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserQuizAttempt extends Model
{
    protected $table = 'user_quiz_attempts';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'attempt_number',
        'score',
        'passed',
        'reward_claimed',
        'xp_earned',
        'coins_earned',
        'completed_at'
    ];

    protected $casts = [
        'attempt_number' => 'integer',
        'score' => 'integer',
        'passed' => 'boolean',
        'reward_claimed' => 'boolean',
        'xp_earned' => 'integer',
        'coins_earned' => 'integer',
        'completed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    // Calculate reward based on attempt number
    public static function calculateReward(int $attemptNumber): array
    {
        $rewardMap = [
            1 => 90,
            2 => 85,
            3 => 80,
            4 => 75,
            5 => 70,
            6 => 65,
        ];

        $reward = $rewardMap[$attemptNumber] ?? 60; // 60 for attempt 7+

        return [
            'xp' => $reward,
            'coins' => $reward
        ];
    }

    // Check if user has already claimed reward for this quiz
    public static function hasClaimedReward($userId, $quizId): bool
    {
        return self::where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->where('passed', true)
            ->where('reward_claimed', true)
            ->exists();
    }
}