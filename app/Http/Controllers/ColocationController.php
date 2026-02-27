<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ColocationController extends Controller
{
    public function create()
    {
        return view('colocations.create');
    }

    public function store(Request $request)
    {
        // Check if user already has a colocation
        if (Auth::user()->colocations()->exists()) {
            return redirect()->route('dashboard')->with('error', 'You can only have one colocation.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $colocation = Colocation::create($validated);
        Auth::user()->colocations()->attach($colocation->id, ['colocation_role' => 'owner']);
        
        return redirect()->route('colocations.show', $colocation)->with('success', 'Colocation created successfully!');
    }

    public function show(Colocation $colocation)
    {
        if (!$colocation->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'You are not a member of this colocation.');
        }

        $colocation->load(['users', 'expenses.payer', 'expenses.category', 'invitations']);

        return view('colocations.show', compact('colocation'));
    }

    public function leaveColocation(Colocation $colocation)
    {
        if (!$colocation->users()->where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('error', 'You are not a member of this colocation.');
        }

        $user = Auth::user();
        
        // Simple leave without debt handling for now
        try {
            DB::beginTransaction();
            
            $colocation->users()->detach($user->id);
            
            DB::commit();
            
            return redirect()->route('dashboard')->with('success', 'You have successfully left the colocation.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->with('error', 'Error leaving colocation.');
        }
    }

    public function removeMember(Colocation $colocation, User $member)
    {
        // Check if current user is owner
        $currentUserRole = $colocation->users()->where('user_id', Auth::id())->first()?->pivot->colocation_role;
        if ($currentUserRole !== 'owner') {
            return redirect()->back()->with('error', 'Only the owner can remove members.');
        }

        // Cannot remove yourself
        if ($member->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot remove yourself. Use leave instead.');
        }

        // Check if member exists in colocation
        if (!$colocation->users()->where('user_id', $member->id)->exists()) {
            return redirect()->back()->with('error', 'Member not found in this colocation.');
        }

        $owner = Auth::user();
        
        $memberBalances = app(\App\Services\BalanceCalculationService::class)->calculateMemberBalances($colocation);
        $memberBalance = $memberBalances[$member->id]['balance'] ?? 0;
        $hasDebt = $memberBalance < 0;

        if (request()->has('confirm')) {
            try {
                DB::beginTransaction();
                
                // If member has debt, transfer it to owner
                if ($hasDebt && $memberBalance < 0) {
                    // Create a payment record to represent the debt transfer
                    \App\Models\Payment::create([
                        'amount' => abs($memberBalance),
                        'payer_id' => $owner->id,
                        'receiver_id' => $member->id,
                        'colocation_id' => $colocation->id,
                    ]);
                }
                
                $colocation->users()->detach($member->id);
                
                DB::commit();
                
                $message = $hasDebt 
                    ? "Member removed. Their €" . number_format(abs($memberBalance), 2) . " debt has been transferred to you."
                    : "Member removed successfully.";
                    
                return redirect()->route('colocations.show', $colocation)->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Error removing member: ' . $e->getMessage());
            }
        }

        return view('colocations.remove-confirm', compact('colocation', 'member', 'hasDebt', 'memberBalance'));
    }
}
