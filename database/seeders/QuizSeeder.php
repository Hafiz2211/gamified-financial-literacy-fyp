<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run()
    {
        // Level 1 - Beginner Quiz
        $quiz1 = Quiz::create([
            'title' => 'Financial Awareness',
            'description' => 'Test your basic understanding of financial concepts.',
            'level_required' => 1, // Keep as 1 for all quizzes
            'order' => 1,
            'passing_score' => 70
        ]);

        $questions1 = [
            [
                'question' => 'A student has BND 20 remaining for the week. They still need to pay BND 10 for bus transport to campus tomorrow but are considering buying a BND 8 specialty drink today. What should the student do?',
                'options' => ['Buy the drink because they still have money left', 'Borrow money later for transport', 'Prioritize transport and skip the drink', 'Spend all remaining money today'],
                'correct' => 'C'
            ],
            [
                'question' => 'A student receives BND 200 allowance monthly but spends around BND 220 each month. What will most likely happen if this continues?',
                'options' => ['The student\'s savings will increase', 'The student will experience financial shortage', 'The student\'s allowance will increase', 'Spending habits will improve automatically'],
                'correct' => 'B'
            ],
            [
                'question' => 'A student believes they only spend BND 40 monthly on snacks. After recording their spending for one month, they realize they actually spent BND 110. What does this situation demonstrate?',
                'options' => ['Snacks are expensive everywhere', 'Tracking spending improves awareness', 'Students should stop eating snacks', 'Recording expenses is unnecessary'],
                'correct' => 'B'
            ],
            [
                'question' => 'A student receives BND 250 allowance and immediately spends BND 80 on shopping without planning. What could have helped prevent overspending?',
                'options' => ['Waiting until the end of the month', 'Spending faster', 'Creating a budget beforehand', 'Asking friends for advice'],
                'correct' => 'C'
            ],
            [
                'question' => 'A student needs BND 120 in three months to repair their laptop. Which is the most effective saving strategy?',
                'options' => ['Save randomly whenever possible', 'Save BND 10 weekly', 'Spend first and save what is left', 'Borrow money when needed'],
                'correct' => 'B'
            ],
            [
                'question' => 'A student suddenly needs BND 90 to repair their phone screen after dropping it. Which financial preparation would be most helpful?',
                'options' => ['Spending allowance quickly', 'Asking friends for loans', 'Having emergency savings', 'Ignoring the repair'],
                'correct' => 'C'
            ],
            [
                'question' => 'A student spends BND 150 monthly on optional purchases such as daily drinks. What is the opportunity cost of this decision?',
                'options' => ['Less enjoyment from drinks', 'Losing the chance to save for future needs', 'Higher phone usage', 'Increased academic performance'],
                'correct' => 'B'
            ],
            [
                'question' => 'After receiving a higher allowance, a student starts dining out more frequently. What concept does this situation represent?',
                'options' => ['Budget discipline', 'Emergency planning', 'Lifestyle inflation', 'Fixed expenses'],
                'correct' => 'C'
            ],
            [
                'question' => 'If a student consistently spends more than their monthly income, what is the long-term outcome?',
                'options' => ['Financial stability', 'Increased savings', 'Financial difficulty', 'Automatic income increase'],
                'correct' => 'C'
            ],
            [
                'question' => 'Why is saving money immediately after receiving income recommended?',
                'options' => ['It reduces spending flexibility', 'It ensures savings are not forgotten', 'It increases entertainment expenses', 'It makes budgeting harder'],
                'correct' => 'B'
            ],
        ];

        foreach ($questions1 as $index => $q) {
            QuizQuestion::create([
                'quiz_id' => $quiz1->id,
                'question_text' => $q['question'],
                'option_a' => $q['options'][0],
                'option_b' => $q['options'][1],
                'option_c' => $q['options'][2],
                'option_d' => $q['options'][3],
                'correct_option' => $q['correct'],
                'order' => $index + 1
            ]);
        }

        // Level 2 - Intermediate Quiz (CHANGED level_required to 1)
        $quiz2 = Quiz::create([
            'title' => 'Financial Application',
            'description' => 'Apply your knowledge to real-life scenarios.',
            'level_required' => 1, // 🔴 CHANGED from 5 to 1
            'order' => 2,
            'passing_score' => 70
        ]);

        $questions2 = [
            [
                'question' => 'A student has BND 50 remaining for the week. They need BND 30 for meals and BND 15 for transport to attend training sessions on campus. They are considering buying a BND 25 sports jersey because it is on sale. What is the most financially responsible decision?',
                'options' => ['Buy the jersey because it is discounted', 'Prioritize meals and transport first', 'Use all money before the week ends', 'Skip transport instead'],
                'correct' => 'B'
            ],
            [
                'question' => 'A student receives BND 300 monthly allowance and plans to use: 60% for essentials, 20% for savings, 20% for leisure. How much should the student allocate for savings?',
                'options' => ['BND 30', 'BND 40', 'BND 60', 'BND 90'],
                'correct' => 'C'
            ],
            [
                'question' => 'A student earns BND 200 from part-time work and receives BND 150 allowance. Their monthly expenses are: Food: BND 140, Transport: BND 60, Mobile Data: BND 50, Entertainment: BND 80. What should the student do to avoid overspending?',
                'options' => ['Increase entertainment spending', 'Reduce variable expenses', 'Ignore the expenses', 'Spend savings first'],
                'correct' => 'B'
            ],
            [
                'question' => 'A student wants to save BND 180 within 6 months to pay for tournament registration and equipment. How much should they save each month?',
                'options' => ['BND 20', 'BND 25', 'BND 30', 'BND 35'],
                'correct' => 'C'
            ],
            [
                'question' => 'A student has BND 100 saved for emergencies. They see a limited-time online sale offering wireless earphones for BND 90. What should the student do?',
                'options' => ['Use emergency savings for the purchase', 'Wait and keep emergency savings intact', 'Borrow money to buy it', 'Spend half of the emergency fund'],
                'correct' => 'B'
            ],
            [
                'question' => 'A student spends BND 10 daily on snacks during lecture breaks for one month (30 days). What is the total monthly spending?',
                'options' => ['BND 100', 'BND 200', 'BND 300', 'BND 400'],
                'correct' => 'C'
            ],
            [
                'question' => 'After tracking spending for one month, a student notices they spent: BND 160 on food, BND 100 on entertainment, BND 40 on snacks. Which category should they reduce first to improve savings?',
                'options' => ['Food', 'Transport', 'Entertainment', 'Savings'],
                'correct' => 'C'
            ],
            [
                'question' => 'A student budgets BND 70 monthly for leisure but has already spent BND 60 in the first two weeks. What should the student do next?',
                'options' => ['Continue spending normally', 'Increase leisure spending', 'Limit further leisure spending', 'Ignore the budget'],
                'correct' => 'C'
            ],
            [
                'question' => 'A student wants to buy a BND 50 game but also needs BND 100 for laptop repair next month. What is the best decision?',
                'options' => ['Buy the game immediately', 'Save money for laptop repair', 'Borrow for repair later', 'Spend half now'],
                'correct' => 'B'
            ],
            [
                'question' => 'If a student consistently saves 20% of their monthly allowance, what is the long-term benefit?',
                'options' => ['Increased impulse spending', 'Reduced financial preparedness', 'Improved financial stability', 'Higher entertainment expenses'],
                'correct' => 'C'
            ],
        ];

        foreach ($questions2 as $index => $q) {
            QuizQuestion::create([
                'quiz_id' => $quiz2->id,
                'question_text' => $q['question'],
                'option_a' => $q['options'][0],
                'option_b' => $q['options'][1],
                'option_c' => $q['options'][2],
                'option_d' => $q['options'][3],
                'correct_option' => $q['correct'],
                'order' => $index + 1
            ]);
        }

        // Level 3 - Advanced Quiz (CHANGED level_required to 1)
        $quiz3 = Quiz::create([
            'title' => 'Financial Analysis',
            'description' => 'Analyze complex financial situations and make strategic decisions.',
            'level_required' => 1, // 🔴 CHANGED from 10 to 1
            'order' => 3,
            'passing_score' => 70
        ]);

        $questions3 = [
            [
                'question' => 'A student has BND 120 saved. They need: BND 100 for upcoming tournament registration next month and BND 90 for new headphones currently on sale. If they buy the headphones now, what is the likely consequence?',
                'options' => ['Increased savings next month', 'Inability to pay for tournament registration', 'Improved budgeting skills', 'Reduced transport expenses'],
                'correct' => 'B'
            ],
            [
                'question' => 'A student budgets BND 250 monthly: Essentials: BND 150, Savings: BND 50, Leisure: BND 50. However, actual spending becomes: Essentials: BND 150, Savings: BND 20, Leisure: BND 80. What is the main issue?',
                'options' => ['Essentials spending too high', 'Savings not prioritised', 'Income too low', 'Transport costs increasing'],
                'correct' => 'B'
            ],
            [
                'question' => 'A student spends BND 8 daily on drinks during lectures. Over 5 days a week for 4 weeks, how much is spent? Then asks: If this spending continues for 6 months, what could be affected most?',
                'options' => ['BND 80 - Academic grades', 'BND 120 - Long-term savings goals', 'BND 160 - Class attendance', 'BND 200 - Phone battery life'],
                'correct' => 'C' // BND 160 monthly → BND 960 in 6 months
            ],
            [
                'question' => 'A student uses emergency savings to purchase discounted sports shoes. Two weeks later, their laptop charger stops working and needs replacement costing BND 70. What does this situation demonstrate?',
                'options' => ['Effective saving behaviour', 'Importance of opportunity cost', 'Benefits of spending early', 'Increased financial security'],
                'correct' => 'B'
            ],
            [
                'question' => 'A student consistently spends 90% of their allowance monthly. What is the most likely long-term outcome?',
                'options' => ['Stable emergency fund', 'Improved saving discipline', 'Limited financial flexibility', 'Increased financial independence'],
                'correct' => 'C'
            ],
            [
                'question' => 'After getting a part-time job, a student begins ordering food delivery more frequently, buying branded sportswear, and subscribing to additional online services. Despite increased income, their savings remain unchanged. What is the primary reason?',
                'options' => ['Lack of income', 'Poor academic planning', 'Lifestyle inflation', 'Increased transport fees'],
                'correct' => 'C'
            ],
            [
                'question' => 'A student plans to save BND 150 for laptop repair in 3 months. However, they spend BND 60 monthly on leisure activities. What adjustment would best support the saving goal?',
                'options' => ['Increase leisure spending', 'Reduce savings', 'Reduce leisure expenses', 'Borrow money later'],
                'correct' => 'C'
            ],
            [
                'question' => 'Monthly Income: BND 400, Monthly Expenses: BND 380. Unexpected repair needed: BND 50. What financial issue will most likely occur?',
                'options' => ['Increase in savings', 'Financial deficit', 'Budget surplus', 'Reduced income'],
                'correct' => 'B'
            ],
            [
                'question' => 'A student must choose between: Saving for future device maintenance vs. Purchasing a non-essential gaming accessory. What factor should be prioritised?',
                'options' => ['Social trends', 'Future functional needs', 'Discount availability', 'Brand popularity'],
                'correct' => 'B'
            ],
            [
                'question' => 'A student with no savings faces sudden transportation repair costs. What is the most likely immediate response?',
                'options' => ['Use emergency savings', 'Borrow money', 'Increase income instantly', 'Ignore repair'],
                'correct' => 'B'
            ],
        ];

        foreach ($questions3 as $index => $q) {
            QuizQuestion::create([
                'quiz_id' => $quiz3->id,
                'question_text' => $q['question'],
                'option_a' => $q['options'][0],
                'option_b' => $q['options'][1],
                'option_c' => $q['options'][2],
                'option_d' => $q['options'][3],
                'correct_option' => $q['correct'],
                'order' => $index + 1
            ]);
        }
    }
}