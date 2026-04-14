<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'level' => 1,
        'xp' => 0,
        'coins' => 0,
        'town_level' => 1,
        'population' => 0,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'level',
        'xp',
        'coins',
        'town_level',
        'population',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot the model and add model events.
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (is_null($user->level)) $user->level = 1;
            if (is_null($user->xp)) $user->xp = 0;
            if (is_null($user->coins)) $user->coins = 0;
            if (is_null($user->town_level)) $user->town_level = 1;
            if (is_null($user->population)) $user->population = 0;
        });
    }

    /**
     * Level thresholds (cumulative XP required for each level)
     */
    public function getLevelThresholds()
    {
        return [
            1 => 0,
            2 => 300,
            3 => 700,
            4 => 1200,
            5 => 1800,
            6 => 2500,
            7 => 3300,
            8 => 4200,
            9 => 5200,
            10 => 6300,
        ];
    }

    /**
     * Update user level based on XP thresholds
     * PRESERVES XP - does NOT reset it!
     */
    public function updateLevel()
    {
        $thresholds = $this->getLevelThresholds();
        $currentXP = $this->xp;
        
        // Find the correct level based on XP
        $newLevel = 1;
        foreach ($thresholds as $level => $threshold) {
            if ($currentXP >= $threshold) {
                $newLevel = $level;
            }
        }
        
        // Update level if changed
        if ($this->level != $newLevel) {
            $this->level = $newLevel;
        }
        
        return $this;
    }

    /**
     * Force recalculate level and save
     */
    public function recalculateLevel()
    {
        $thresholds = $this->getLevelThresholds();
        $currentXP = $this->xp;
        
        $correctLevel = 1;
        foreach ($thresholds as $level => $threshold) {
            if ($currentXP >= $threshold) {
                $correctLevel = $level;
            }
        }
        
        $this->level = $correctLevel;
        $this->save();
        
        return $this;
    }

    /**
     * Get XP needed for next level
     */
    public function getXpToNextLevel()
    {
        $thresholds = $this->getLevelThresholds();
        $currentLevel = $this->level;
        $nextLevel = min($currentLevel + 1, 10);
        
        $currentThreshold = $thresholds[$currentLevel];
        $nextThreshold = $thresholds[$nextLevel];
        
        return $nextThreshold - $currentThreshold;
    }

    /**
     * Get XP progress in current level
     */
    public function getXpInCurrentLevel()
    {
        $thresholds = $this->getLevelThresholds();
        $currentLevel = $this->level;
        $currentThreshold = $thresholds[$currentLevel];
        
        return $this->xp - $currentThreshold;
    }

    // ========== 🔴 PREMIUM METHODS ==========

    /**
     * 🔴 FIXED: Check if user has active premium subscription
     * User keeps premium access until premium_until date expires
     */
    public function isPremium(): bool
    {
        // Check if user has premium flag AND premium_until date is set AND not expired
        if ($this->is_premium && $this->premium_until) {
            return now()->lessThan($this->premium_until);
        }
        return false;
    }

    /**
     * Check if user can access a premium lesson
     */
    public function canAccessPremiumLesson($lessonNumber): bool
    {
        $freeLessons = [1, 2, 3, 4, 5, 6];
        if (in_array($lessonNumber, $freeLessons)) {
            return true;
        }
        return $this->isPremium();
    }

    /**
     * Check if user can access a premium quiz
     */
    public function canAccessPremiumQuiz($quizOrder): bool
    {
        $freeQuizzes = [1, 2, 3];
        if (in_array($quizOrder, $freeQuizzes)) {
            return true;
        }
        return $this->isPremium();
    }

    /**
     * Get reward for premium lesson
     */
    public function getPremiumLessonReward(): int
    {
        return 100;
    }

    /**
     * Get reward for free lesson
     */
    public function getFreeLessonReward(): int
    {
        return 50;
    }

    /**
     * Get reward for premium quiz based on attempt number
     */
    public function getPremiumQuizReward($attemptNumber): array
    {
        $reward = 150 - (($attemptNumber - 1) * 10);
        $reward = max(90, $reward);
        return ['xp' => $reward, 'coins' => $reward];
    }

    /**
     * Get reward for free quiz based on attempt number
     */
    public function getFreeQuizReward($attemptNumber): array
    {
        $reward = 90 - (($attemptNumber - 1) * 5);
        $reward = max(60, $reward);
        return ['xp' => $reward, 'coins' => $reward];
    }

    // Your existing relationships
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'user_lessons')
                    ->withPivot('xp_earned', 'coins_earned', 'completed_at')
                    ->withTimestamps();
    }

    public function dailyStats()
    {
        return $this->hasMany(DailyStat::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'user_quiz_attempts')
                    ->withPivot(['score', 'passed', 'attempt_number', 'reward_claimed', 'xp_earned', 'coins_earned'])
                    ->withTimestamps();
    }
}