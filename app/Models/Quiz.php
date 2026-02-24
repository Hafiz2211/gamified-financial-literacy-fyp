<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'description',
        // 'level_required', // 🔴 Commented out - not needed for pass-based unlocking
        'order',
        'passing_score'
    ];

    protected $casts = [
        // 'level_required' => 'integer', // 🔴 Commented out
        'order' => 'integer',
        'passing_score' => 'integer'
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function userAttempts(): HasMany
    {
        return $this->hasMany(UserQuizAttempt::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_quiz_attempts')
                    ->withPivot(['score', 'passed', 'attempt_number', 'reward_claimed'])
                    ->withTimestamps();
    }

    // Check if user has passed this quiz
    public function isPassedByUser($userId): bool
    {
        return $this->userAttempts()
            ->where('user_id', $userId)
            ->where('passed', true)
            ->exists();
    }

    // Get user's best attempt
    public function getBestAttempt($userId): ?UserQuizAttempt
    {
        return $this->userAttempts()
            ->where('user_id', $userId)
            ->orderBy('score', 'desc')
            ->first();
    }

    // Get next attempt number for user
    public function getNextAttemptNumber($userId): int
    {
        return $this->userAttempts()
            ->where('user_id', $userId)
            ->max('attempt_number') + 1;
    }
}