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
        
        // Convert to integers to prevent Carbon error
        $selectedMonth = (int) $request->get('month', now()->month);
        $selectedYear = (int) $request->get('year', now()->year);
        
        // Use Carbon::create() instead of setYear/setMonth
        $selectedDate = Carbon::create($selectedYear, $selectedMonth, 1);
        
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

        // Get daily spending for last 7 days
        $dailyData = [];
        $today = Carbon::now();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $displayDate = $date->format('D d');
            
            $total = $user->transactions()
                ->where('type', 'expense')
                ->whereDate('date', $dateString)
                ->sum('amount');
            
            $dailyData[$displayDate] = $total ?: 0;
        }

        // Get monthly comparison (last 3 months)
        $monthlyData = [];
        for ($i = 2; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
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