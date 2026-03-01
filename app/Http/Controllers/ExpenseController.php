<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        if ($request->auto_split) {
            // Simplified validation for auto-split mode
            $request->validate([
                'description' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0.01',
                'date' => 'required|date',
                'category_id' => 'nullable|exists:categories,id',
                'payer_id' => 'required|exists:users,id',
                'colocation_id' => 'required|exists:colocations,id',
            ]);
        } else {
            // Full validation for custom split mode
            $request->validate([
                'description' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0.01',
                'date' => 'required|date',
                'category_id' => 'nullable|exists:categories,id',
                'payer_id' => 'required|exists:users,id',
                'colocation_id' => 'required|exists:colocations,id',
                'participants' => 'required|array|min:1',
                'participants.*' => 'exists:users,id',
                'amounts' => 'required|array',
                'amounts.*' => 'required|numeric|min:0',
            ]);
        }

        // Check if user is member of the colocation
        $colocation = Colocation::findOrFail($request->colocation_id);
        if (!$colocation->users()->where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('error', 'You are not a member of this colocation.');
        }

        // Verify category belongs to this colocation if provided
        if ($request->category_id) {
            $category = \App\Models\Category::find($request->category_id);
            if (!$category || $category->colocation_id != $request->colocation_id) {
                return redirect()->back()->with('error', 'Invalid category.');
            }
        }

        // Verify payer is member of colocation
        if (!$colocation->users()->where('user_id', $request->payer_id)->exists()) {
            return redirect()->back()->with('error', 'Payer must be a member of this colocation.');
        }

        $expense = Expense::create([
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
            'payer_id' => $request->payer_id,
            'colocation_id' => $request->colocation_id,
            'category_id' => $request->category_id,
        ]);

        if ($request->auto_split) {
            // Auto-split among all members
            $shareAmount = number_format($request->amount / $colocation->users->count(), 2, '.', '');
            foreach ($colocation->users as $member) {
                $expense->participants()->attach($member->id, [
                    'share_amount' => $shareAmount
                ]);
            }
        } else {
            // Custom splitting with validation
            // Verify all participants are members of colocation
            foreach ($request->participants as $participantId) {
                if (!$colocation->users()->where('user_id', $participantId)->exists()) {
                    return redirect()->back()->with('error', 'All participants must be members of this colocation.');
                }
            }

            // Validate that total split amounts match the expense amount
            $totalSplitAmount = array_sum($request->amounts);
            if (abs($totalSplitAmount - $request->amount) > 0.01) {
                return redirect()->back()->with('error', 'Split amounts must equal the total expense amount.');
            }

            // Add participants with their custom amounts
            foreach ($request->participants as $participantId) {
                $expense->participants()->attach($participantId, [
                    'share_amount' => $request->amounts[$participantId]
                ]);
            }
        }

        return redirect()->back()->with('success', 'Expense created successfully!');
    }

    public function destroy(Expense $expense)
    {
        // Check if user is member of the colocation
        if (!$expense->colocation->users()->where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('error', 'You are not a member of this colocation.');
        }

        // Only the payer can delete the expense
        if ($expense->payer_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Only the person who paid can delete this expense.');
        }

        $expense->participants()->detach();
        $expense->delete();

        return redirect()->back()->with('success', 'Expense deleted successfully!');
    }

    public function index(Request $request, Colocation $colocation)
    {
        // Check if user is member of the colocation
        if (!$colocation->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'You are not a member of this colocation.');
        }

        $monthFilter = $request->get('month', 'all');
        $expenseService = new \App\Services\ExpenseService();
        
        $expenses = $expenseService->getExpensesWithFilters($colocation, $monthFilter);
        $monthlyOptions = $expenseService->getMonthlyOptions($colocation);
        $categoryStats = $expenseService->getCategoryStats($colocation, $monthFilter);
        $monthlyStats = $expenseService->getMonthlyStats($colocation);
        $totalStats = $expenseService->getTotalStats($colocation, $monthFilter);

        return view('expenses.index', compact(
            'colocation',
            'expenses',
            'monthlyOptions',
            'monthFilter',
            'categoryStats',
            'monthlyStats',
            'totalStats'
        ));
    }
}
