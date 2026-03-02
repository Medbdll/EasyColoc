<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\ColocationMembershipHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OldColocationController extends Controller
{
    public function index()
    {
        $oldColocations = Auth::user()
            ->oldColocations()
            ->with('colocation')
            ->orderBy('left_at', 'desc')
            ->get()
            ->map(function ($history) {
                return [
                    'colocation' => $history->colocation,
                    'history' => $history,
                ];
            });

        return view('old-colocations.index', compact('oldColocations'));
    }

    public function show(Colocation $colocation)
    {
        // Check if user has history with this colocation
        $history = ColocationMembershipHistory::where('user_id', Auth::id())
            ->where('colocation_id', $colocation->id)
            ->whereNotNull('left_at')
            ->first();

        if (!$history) {
            abort(403, 'You do not have access to view this colocation history.');
        }

        // Load all expenses and payments for historical view
        $colocation->load([
            'expenses.payer',
            'expenses.category',
            'expenses.participants',
            'payments.payer',
            'payments.receiver'
        ]);

        return view('old-colocations.show', compact('colocation', 'history'));
    }
}
