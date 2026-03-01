<?php

namespace App\Services;

use App\Models\Colocation;
use App\Models\Expense;
use Carbon\Carbon;

class ExpenseService
{
    public function getExpensesWithFilters(Colocation $colocation, ?string $monthFilter = null)
    {
        $query = $colocation->expenses()->with(['payer', 'category', 'participants']);
        
        if ($monthFilter && $monthFilter !== 'all') {
            $query->whereYear('date', '=', Carbon::parse($monthFilter)->year)
                  ->whereMonth('date', '=', Carbon::parse($monthFilter)->month);
        }
        
        return $query->orderBy('date', 'desc')->get();
    }
    
    public function getMonthlyOptions(Colocation $colocation): array
    {
        $months = $colocation->expenses()
            ->selectRaw('DISTINCT DATE_FORMAT(date, "%Y-%m") as month')
            ->orderBy('month', 'desc')
            ->pluck('month')
            ->toArray();
        
        $options = ['all' => 'All Months'];
        
        foreach ($months as $month) {
            $carbon = Carbon::parse($month . '-01');
            $options[$month] = $carbon->format('F Y');
        }
        
        return $options;
    }
    
    public function getCategoryStats(Colocation $colocation, ?string $monthFilter = null): array
    {
        $query = $colocation->expenses();
        
        if ($monthFilter && $monthFilter !== 'all') {
            $query->whereYear('date', '=', Carbon::parse($monthFilter)->year)
                  ->whereMonth('date', '=', Carbon::parse($monthFilter)->month);
        }
        
        $stats = $query->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, SUM(expenses.amount) as total, COUNT(expenses.id) as count')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total', 'desc')
            ->get();
        
        return $stats->toArray();
    }
    
    public function getMonthlyStats(Colocation $colocation): array
    {
        $stats = $colocation->expenses()
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
        
        $result = [];
        foreach ($stats as $stat) {
            $carbon = Carbon::parse($stat->month . '-01');
            $result[] = [
                'month' => $stat->month,
                'month_name' => $carbon->format('F Y'),
                'total' => $stat->total,
                'count' => $stat->count,
            ];
        }
        
        return $result;
    }
    
    public function getTotalStats(Colocation $colocation, ?string $monthFilter = null): array
    {
        $query = $colocation->expenses();
        
        if ($monthFilter && $monthFilter !== 'all') {
            $query->whereYear('date', '=', Carbon::parse($monthFilter)->year)
                  ->whereMonth('date', '=', Carbon::parse($monthFilter)->month);
        }
        
        return [
            'total_amount' => $query->sum('amount'),
            'total_count' => $query->count(),
            'average_amount' => $query->avg('amount'),
        ];
    }
}
