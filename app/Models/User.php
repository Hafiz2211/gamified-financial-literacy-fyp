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
     * This sets defaults WHEN CREATING a new model instance
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
     *
     * @var array<int, string>
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
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
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
            // Ensure null values are replaced with defaults
            if (is_null($user->level)) {
                $user->level = 1;
            }
            if (is_null($user->xp)) {
                $user->xp = 0;
            }
            if (is_null($user->coins)) {
                $user->coins = 0;
            }
            if (is_null($user->town_level)) {
                $user->town_level = 1;
            }
            if (is_null($user->population)) {
                $user->population = 100;
            }
        });
    }

    // 🔴 ADD THIS METHOD - Level up logic
    public function updateLevel()
    {
        // Level progression formula: 200 + (current level × 100)
        $levelThresholds = [
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
        
        $newLevel = $this->level;
        foreach ($levelThresholds as $level => $threshold) {
            if ($this->xp >= $threshold) {
                $newLevel = $level;
            }
        }
        
        if ($newLevel > $this->level) {
            $this->level = $newLevel;
        }
        
        return $this;
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

    // 🔴 ADD THESE NEW RELATIONSHIPS for Quiz system
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