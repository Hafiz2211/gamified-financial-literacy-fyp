<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get selected month or use current month
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);
        $selectedDate = now()->setYear($selectedYear)->setMonth($selectedMonth);
        
        // Get available months for dropdown
        $availableMonths = $user->transactions()
            ->select(DB::raw('YEAR(date) as year, MONTH(date) as month'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'value' => $item->year . '-' . $item->month,
                    'label' => date('F Y', mktime(0, 0, 0, $item->month, 1, $item->year))
                ];
            });
        
        // Get category breakdown for selected month
        $categoryData = $user->transactions()
            ->select('category', DB::raw('sum(amount) as total'))
            ->where('type', 'expense')
            ->whereMonth('date', $selectedMonth)
            ->whereYear('date', $selectedYear)
            ->groupBy('category')
            ->get();

        // DEBUG: Get daily spending for last 7 days
        $dailyData = [];
        $today = Carbon::now();
        
        // Add debug comments that will appear in HTML source
        echo "<!-- DEBUG: Today is " . $today->format('Y-m-d') . " -->";
        echo "<!-- DEBUG: Server time: " . date('Y-m-d H:i:s') . " -->";
        
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $displayDate = $date->format('D d');
            
            $total = $user->transactions()
                ->where('type', 'expense')
                ->whereDate('date', $dateString)
                ->sum('amount');
            
            $dailyData[$displayDate] = $total ?: 0;
            
            echo "<!-- DEBUG: Day $i = $displayDate ($dateString) total = $total -->";
        }

        // Get monthly comparison (last 3 months)
        $monthlyData = [];
        for ($i = 2; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $income = $user->transactions()
                ->where('type', 'income')
                ->whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->sum('amount') ?: 0;
            
            $expense = $user->transactions()
                ->where('type', 'expense')
                ->whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->sum('amount') ?: 0;
                
            $monthlyData[$month->format('M Y')] = [
                'income' => $income,
                'expense' => $expense
            ];
        }

        // Calculate summary stats for selected month
        $totalIncome = $user->transactions()
            ->where('type', 'income')
            ->whereMonth('date', $selectedMonth)
            ->whereYear('date', $selectedYear)
            ->sum('amount') ?: 0;
            
        $totalExpense = $user->transactions()
            ->where('type', 'expense')
            ->whereMonth('date', $selectedMonth)
            ->whereYear('date', $selectedYear)
            ->sum('amount') ?: 0;

        $daysInMonth = $selectedDate->daysInMonth;
        $avgDaily = $daysInMonth > 0 ? round($totalExpense / $daysInMonth, 2) : 0;
        
        $topCategory = $categoryData->sortByDesc('total')->first();
        
        $savingsRate = $totalIncome > 0 
            ? round((($totalIncome - $totalExpense) / $totalIncome) * 100, 1)
            : 0;

        return view('statistics', [
            'categoryData' => $categoryData,
            'dailyData' => $dailyData,
            'monthlyData' => $monthlyData,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'avgDaily' => $avgDaily,
            'topCategory' => $topCategory ? $topCategory->category : 'None',
            'savingsRate' => $savingsRate,
            'availableMonths' => $availableMonths,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedLabel' => $selectedDate->format('F Y')
        ]);
    }
}