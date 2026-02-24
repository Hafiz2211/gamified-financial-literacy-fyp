<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'content', 'xp_reward', 'coin_reward', 'order_number'];
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_lessons')
                    ->withPivot('xp_earned', 'coins_earned', 'completed_at')
                    ->withTimestamps();
    }
}