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
        'population' => 100,
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
            if (is_null($user->population)) $user->population = 100;
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