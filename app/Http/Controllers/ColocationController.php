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
        $currentUserRole = $colocation->users()->where('user_id', Auth::id())->first()?->pivot->colocation_role;
        
        // Get owner for potential debt transfer
        $owner = $colocation->users()->wherePivot('colocation_role', 'owner')->first();
        
        // Calculate member's balance
        $memberBalances = app(\App\Services\BalanceCalculationService::class)->calculateMemberBalances($colocation);
        $memberBalance = $memberBalances[$user->id]['balance'] ?? 0;
        $hasDebt = $memberBalance < 0;
        
        // If user is owner, check if they need to transfer ownership
        if ($currentUserRole === 'owner') {
            $otherMembers = $colocation->users()->where('user_id', '!=', Auth::id())->get();
            
            if ($otherMembers->isEmpty()) {
                return redirect()->back()->with('error', 'You cannot leave the colocation as the only member. Delete the colocation instead.');
            }
            
            // If transferring ownership to another member
            if (request()->has('transfer_to')) {
                $newOwnerId = request('transfer_to');
                $newOwner = $otherMembers->where('id', $newOwnerId)->first();
                
                if (!$newOwner) {
                    return redirect()->back()->with('error', 'Invalid member selected for ownership transfer.');
                }
                
                if (request()->has('confirm_leave')) {
                    try {
                        DB::beginTransaction();
                        
                        // Transfer ownership
                        $colocation->users()->updateExistingPivot($newOwnerId, ['colocation_role' => 'owner']);
                        $colocation->users()->updateExistingPivot(Auth::id(), ['colocation_role' => 'member']);
                        
                        // If current owner has debt, transfer it to new owner
                        if ($hasDebt && $memberBalance < 0) {
                            \App\Models\Payment::create([
                                'amount' => abs($memberBalance),
                                'payer_id' => $newOwnerId,
                                'receiver_id' => $user->id,
                                'colocation_id' => $colocation->id,
                            ]);
                        }
                        
                        // Remove current owner from colocation
                        $colocation->users()->detach($user->id);
                        
                        DB::commit();
                        
                        $message = $hasDebt 
                            ? "You have transferred ownership to {$newOwner->name} and left the colocation. Your €" . number_format(abs($memberBalance), 2) . " debt has been transferred to the new owner."
                            : "You have transferred ownership to {$newOwner->name} and left the colocation.";
                            
                        return redirect()->route('dashboard')->with('success', $message);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Error transferring ownership and leaving colocation.');
                    }
                }
                
                return view('colocations.transfer-ownership-confirm', compact('colocation', 'newOwner', 'hasDebt', 'memberBalance'));
            }
            
            // Show transfer ownership page
            return view('colocations.transfer-ownership', compact('colocation', 'otherMembers', 'hasDebt', 'memberBalance'));
        }
        
        // Regular member leaving logic
        if (request()->has('confirm') || !$hasDebt) {
            try {
                DB::beginTransaction();
                
                // If member has debt, transfer it to owner
                if ($hasDebt && $memberBalance < 0 && $owner) {
                    // Create a payment record to represent the debt transfer
                    \App\Models\Payment::create([
                        'amount' => abs($memberBalance),
                        'payer_id' => $owner->id,
                        'receiver_id' => $user->id,
                        'colocation_id' => $colocation->id,
                    ]);
                }
                
                $colocation->users()->detach($user->id);
                
                DB::commit();
                
                $message = $hasDebt 
                    ? "You have left the colocation. Your €" . number_format(abs($memberBalance), 2) . " debt has been transferred to the owner."
                    : "You have successfully left the colocation.";
                    
                return redirect()->route('dashboard')->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                
                return redirect()->back()->with('error', 'Error leaving colocation.');
            }
        }
        
        return view('colocations.leave-confirm', compact('colocation', 'hasDebt', 'memberBalance'));
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

    public function markAsPaid(Colocation $colocation, User $member)
    {
        // Check if current user is owner
        $currentUserRole = $colocation->users()->where('user_id', Auth::id())->first()?->pivot->colocation_role;
        if ($currentUserRole !== 'owner') {
            return redirect()->back()->with('error', 'Only the owner can mark debts as paid.');
        }

        // Cannot mark yourself as paid
        if ($member->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot mark yourself as paid.');
        }

        // Check if member exists in colocation
        if (!$colocation->users()->where('user_id', $member->id)->exists()) {
            return redirect()->back()->with('error', 'Member not found in this colocation.');
        }

        // Calculate member's current balance
        $memberBalances = app(\App\Services\BalanceCalculationService::class)->calculateMemberBalances($colocation);
        $memberBalance = $memberBalances[$member->id]['balance'] ?? 0;

        // Only allow marking as paid if member has debt
        if ($memberBalance >= 0) {
            return redirect()->back()->with('error', 'This member does not have any debt to mark as paid.');
        }

        try {
            DB::beginTransaction();
            
            // Create a payment record to represent the debt forgiveness
            \App\Models\Payment::create([
                'amount' => abs($memberBalance),
                'payer_id' => Auth::id(),
                'receiver_id' => $member->id,
                'colocation_id' => $colocation->id,
            ]);
            
            DB::commit();
            
            return redirect()->route('colocations.show', $colocation)
                ->with('success', "Member's €" . number_format(abs($memberBalance), 2) . " debt has been marked as paid.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error marking debt as paid: ' . $e->getMessage());
        }
    }
}
