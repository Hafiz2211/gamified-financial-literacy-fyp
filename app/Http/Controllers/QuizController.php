<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\UserQuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get all quizzes with their status for the user
        $quizzes = Quiz::with('questions')->orderBy('order')->get();
        
        $quizStatus = [];
        $totalAvailable = 0;
        
        foreach ($quizzes as $quiz) {
            $status = $this->getQuizStatus($user, $quiz);
            
            // Add best attempt for completed quizzes
            if ($status['status'] == 'completed') {
                $status['best_attempt'] = $quiz->getBestAttempt($user->id);
            }
            
            $quizStatus[$quiz->id] = $status;
            if ($status['available']) {
                $totalAvailable++;
            }
        }
        
        return view('quiz.index', compact('quizzes', 'quizStatus', 'totalAvailable'));
    }
    
    public function show(Quiz $quiz)
    {
        $user = auth()->user();
        
        // Check if quiz is available for user
        $status = $this->getQuizStatus($user, $quiz);
        
        if (!$status['available']) {
            return redirect()->route('quiz.index')
                ->with('error', 'This quiz is not available yet. ' . $status['reason']);
        }
        
        // Load questions
        $quiz->load('questions');
        
        // Get attempt number
        $attemptNumber = $quiz->getNextAttemptNumber($user->id);
        
        // Get previous attempts
        $previousAttempts = UserQuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->orderBy('attempt_number', 'desc')
            ->get();
        
        return view('quiz.take', compact('quiz', 'attemptNumber', 'previousAttempts', 'status'));
    }
    
    public function submit(Request $request, Quiz $quiz)
    {
        $user = auth()->user();
        
        // Validate request
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|in:A,B,C,D'
        ]);
        
        // Check if quiz is available
        $status = $this->getQuizStatus($user, $quiz);
        if (!$status['available']) {
            return response()->json([
                'success' => false,
                'message' => 'This quiz is not available.'
            ], 403);
        }
        
        // Check if already passed (prevent farming)
        if (UserQuizAttempt::hasClaimedReward($user->id, $quiz->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You have already completed this quiz.'
            ], 403);
        }
        
        // 🔴 Determine if this is a premium quiz (Levels 4-6)
        $isPremiumQuiz = $quiz->order > 3;
        
        if ($isPremiumQuiz && !$user->isPremium()) {
            return response()->json([
                'success' => false,
                'message' => 'This quiz requires a premium subscription. Upgrade to access!'
            ], 403);
        }
        
        // Calculate score
        $questions = $quiz->questions;
        $totalQuestions = $questions->count();
        $correctCount = 0;
        
        foreach ($questions as $question) {
            $userAnswer = $request->answers[$question->id] ?? null;
            if ($userAnswer && $question->isCorrect($userAnswer)) {
                $correctCount++;
            }
        }
        
        $score = round(($correctCount / $totalQuestions) * 100);
        $passed = $score >= $quiz->passing_score;
        
        // Get attempt number
        $attemptNumber = $quiz->getNextAttemptNumber($user->id);
        
        // 🔴 Calculate reward based on premium status
        $xpEarned = 0;
        $coinsEarned = 0;
        $rewardClaimed = false;
        
        if ($passed) {
            if ($isPremiumQuiz) {
                // Premium reward: starts at 150, decreases by 10 until 90
                $reward = 150 - (($attemptNumber - 1) * 10);
                $reward = max(90, $reward);
                $xpEarned = $reward;
                $coinsEarned = $reward;
            } else {
                // Free reward: starts at 90, decreases by 5 until 60
                $reward = 90 - (($attemptNumber - 1) * 5);
                $reward = max(60, $reward);
                $xpEarned = $reward;
                $coinsEarned = $reward;
            }
            $rewardClaimed = true;
        }
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Create attempt record
            $attempt = UserQuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'attempt_number' => $attemptNumber,
                'score' => $score,
                'passed' => $passed,
                'reward_claimed' => $rewardClaimed,
                'xp_earned' => $xpEarned,
                'coins_earned' => $coinsEarned,
                'completed_at' => now()
            ]);
            
            // Save each answer to database
            foreach ($questions as $question) {
                $userAnswer = $request->answers[$question->id] ?? null;
                $isCorrect = $userAnswer && $question->isCorrect($userAnswer);
                
                DB::table('user_quiz_answers')->insert([
                    'user_id' => $user->id,
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'selected_option' => $userAnswer,
                    'is_correct' => $isCorrect,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Award XP and coins if passed
            if ($passed && $rewardClaimed) {
                $user->xp += $xpEarned;
                $user->coins += $coinsEarned;
                
                // Check for level up
                $oldLevel = $user->level;
                if (method_exists($user, 'updateLevel')) {
                    $user->updateLevel();
                }
                $user->save();
                
                $leveledUp = $user->level > $oldLevel;
                
                // Level up notification
                if ($leveledUp) {
                    session()->flash('level_up', ['new_level' => $user->level]);
                }
                
            } else {
                $leveledUp = false;
            }
            
            DB::commit();
            
            // Prepare response
            $response = [
                'success' => true,
                'passed' => $passed,
                'score' => $score,
                'correct' => $correctCount,
                'total' => $totalQuestions,
                'attempt_number' => $attemptNumber,
                'attempt_id' => $attempt->id,
                'is_premium_quiz' => $isPremiumQuiz,
                'message' => $passed 
                    ? ($isPremiumQuiz
                        ? "🎉 Premium Quiz Passed! You earned {$xpEarned} XP and {$coinsEarned} coins!"
                        : "🎉 Congratulations! You passed with {$score}% and earned {$xpEarned} XP and {$coinsEarned} coins!")
                    : "You scored {$score}%. You need {$quiz->passing_score}% to pass. Try again!",
                'reward_claimed' => $rewardClaimed
            ];
            
            if ($passed && $rewardClaimed) {
                $response['xp_earned'] = $xpEarned;
                $response['coins_earned'] = $coinsEarned;
                
                if ($leveledUp) {
                    $response['level_up'] = [
                        'new_level' => $user->level
                    ];
                }
                
                // Check next quiz unlock
                $nextQuiz = Quiz::where('order', $quiz->order + 1)->first();
                if ($nextQuiz) {
                    $response['next_unlocked'] = $nextQuiz->title;
                }
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Results method with answers
    public function results(Quiz $quiz, UserQuizAttempt $attempt)
    {
        $user = auth()->user();
        
        // Ensure attempt belongs to user
        if ($attempt->user_id !== $user->id) {
            abort(403);
        }
        
        // Load questions
        $quiz->load('questions');
        
        // Get user's answers for this attempt
        $userAnswers = DB::table('user_quiz_answers')
            ->where('quiz_attempt_id', $attempt->id)
            ->get()
            ->keyBy('question_id');
        
        return view('quiz.results', compact('quiz', 'attempt', 'userAnswers'));
    }
    
    private function getQuizStatus($user, $quiz)
    {
        // Check if already passed
        $hasPassed = UserQuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('passed', true)
            ->exists();
        
        if ($hasPassed) {
            return [
                'available' => false,
                'reason' => 'You have already completed this quiz.',
                'status' => 'completed',
                'locked' => false
            ];
        }
        
        // 🔴 Check premium quiz access
        $isPremiumQuiz = $quiz->order > 3;
        if ($isPremiumQuiz && !$user->isPremium()) {
            return [
                'available' => false,
                'reason' => 'Premium subscription required. Upgrade to unlock!',
                'status' => 'premium_locked',
                'locked' => true
            ];
        }
        
        // Check previous quiz completion
        if ($quiz->order > 1) {
            $previousQuiz = Quiz::where('order', $quiz->order - 1)->first();
            if ($previousQuiz) {
                $passedPrevious = UserQuizAttempt::where('user_id', $user->id)
                    ->where('quiz_id', $previousQuiz->id)
                    ->where('passed', true)
                    ->exists();
                
                if (!$passedPrevious) {
                    return [
                        'available' => false,
                        'reason' => "Complete the previous level first.",
                        'status' => 'locked',
                        'locked' => true
                    ];
                }
            }
        }
        
        // Get attempt info
        $attemptNumber = $quiz->getNextAttemptNumber($user->id);
        $previousAttempts = UserQuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->count();
        
        // 🔴 Calculate next reward based on premium status
        if ($isPremiumQuiz) {
            $reward = 150 - (($attemptNumber - 1) * 10);
            $reward = max(90, $reward);
        } else {
            $reward = 90 - (($attemptNumber - 1) * 5);
            $reward = max(60, $reward);
        }
        
        $nextReward = ['xp' => $reward, 'coins' => $reward];
        
        return [
            'available' => true,
            'status' => 'available',
            'locked' => false,
            'attempt_number' => $attemptNumber,
            'previous_attempts' => $previousAttempts,
            'next_reward' => $nextReward,
            'passing_score' => $quiz->passing_score,
            'is_premium' => $isPremiumQuiz
        ];
    }
}