<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('lessons')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $lessons = [
            [
                'title' => 'Needs vs Wants',
                'content' => json_encode([
                    'A university student receives a monthly allowance of BND 200 from their parents. At the beginning of the month, they plan to use the money for meals, transportation to campus, mobile data for academic use, and printing assignments.',
                    'However, during the week, the student notices that many of their classmates frequently purchase coffee or specialty drinks before lectures. Some also upgrade their phone accessories or buy branded sportswear for training sessions.',
                    'One afternoon, the student considers buying a BND 6 drink after class. The purchase feels small and harmless. However, if the same decision is made daily after lectures, the weekly spending becomes BND 30 and approximately BND 120 per month.',
                    'Although the drink provides enjoyment, it does not directly contribute to survival, academic performance, or work-related responsibilities. It is therefore categorized as a want rather than a need.',
                    'If the student delays the purchase decision by one day using the 24-hour rule, they may later realize that the money could instead be used for transportation or saving towards badminton tournament registration fees next month.',
                    'This situation demonstrates how distinguishing between needs and wants helps students make rational financial decisions instead of emotionally influenced ones.'
                ]),
                'xp_reward' => 50,
                'coin_reward' => 50,
                'order_number' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Budgeting',
                'content' => json_encode([
                    'A student receives BND 250 at the start of each month. Instead of spending freely until the allowance runs out, they decide to create a budget plan.',
                    'They allocate:',
                    'BND 150 for essential expenses (meals and transport)',
                    'BND 50 for savings',
                    'BND 50 for leisure activities',
                    'Immediately after receiving the allowance, the student transfers BND 50 into a separate savings account.',
                    'Later in the month, when invited to dine out several times, the student refers to the leisure category in their budget before making a decision. This prevents overspending and ensures that essential expenses remain covered.',
                    'This example demonstrates how budgeting allows students to proactively manage financial resources rather than reacting to spending impulses.'
                ]),
                'xp_reward' => 50,
                'coin_reward' => 50,
                'order_number' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Income vs Expense',
                'content' => json_encode([
                    'A part-time student earns BND 300 per month from tutoring and also receives BND 150 in allowance. This results in a total monthly income of BND 450.',
                    'Throughout the month, the student spends:',
                    'BND 180 on food',
                    'BND 70 on transportation',
                    'BND 60 on mobile data',
                    'BND 90 on dining out with friends',
                    'BND 80 on online shopping',
                    'By the end of the month, the total expenses amount to BND 480.',
                    'Although the student had income at the start of the month, their expenses exceeded earnings by BND 30. If this pattern continues for several months, savings will gradually decrease and financial stress may occur.',
                    'This scenario illustrates that financial stability depends not only on receiving money but on ensuring that expenses remain within sustainable limits over time.'
                ]),
                'xp_reward' => 50,
                'coin_reward' => 50,
                'order_number' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Saving Goals',
                'content' => json_encode([
                    'A student’s laptop battery requires replacement, which is expected to cost BND 120 within three months.',
                    'Instead of postponing the issue until the device fails completely, the student sets a saving goal to accumulate the required amount within twelve weeks.',
                    'By dividing BND 120 by twelve weeks, the student determines that saving BND 10 per week will achieve the target within the deadline.',
                    'Each week, the student transfers BND 10 into a designated savings account. By the end of the third month, the required amount is available without the need to borrow money from family or friends.',
                    'This scenario illustrates how setting specific saving goals with clear deadlines promotes consistent financial discipline.'
                ]),
                'xp_reward' => 50,
                'coin_reward' => 50,
                'order_number' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Tracking Spending',
                'content' => json_encode([
                    'A student believes they only spend around BND 50 per month on snacks and drinks.',
                    'To verify this assumption, they begin recording daily purchases in a mobile notes application for four weeks. Each time they buy bottled drinks, snacks between lectures, or small convenience items, they record the amount spent.',
                    'At the end of the month, the recorded data shows that total spending on snacks and drinks was actually BND 135.',
                    'The difference between perceived and actual spending highlights how relying on memory can result in inaccurate financial judgments. By tracking spending behaviour using simple recording methods, students can identify unnecessary expenditure and make adjustments for the following month.'
                ]),
                'xp_reward' => 50,
                'coin_reward' => 50,
                'order_number' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Emergency Fund',
                'content' => json_encode([
                    'While commuting to campus, a student accidentally drops their smartphone, causing screen damage that requires immediate repair costing BND 90.',
                    'Without emergency savings, the student may need to borrow money from friends or delay the repair, which could disrupt communication and academic coordination.',
                    'However, if the student had previously built an emergency fund by saving BND 5 per week over several months, they would have sufficient funds to cover the repair cost immediately.',
                    'This preparation reduces financial stress and prevents reliance on external assistance during urgent situations.'
                ]),
                'xp_reward' => 50,
                'coin_reward' => 50,
                'order_number' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($lessons as $lesson) {
            Lesson::create($lesson);
        }
        
        $this->command->info('Lessons seeded successfully!');
    }
}