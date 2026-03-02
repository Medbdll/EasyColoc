<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InvitationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'colocation_id' => 'required|exists:colocations,id'
        ]);

        $colocation = Colocation::findOrFail($request->colocation_id);
        
        // Check if user is owner of this colocation
        $currentUserRole = $colocation->users()->where('user_id', auth()->id())->first()?->pivot->colocation_role;
        if ($currentUserRole !== 'owner') {
            return back()->with('error', 'Only the owner can invite members to this colocation.');
        }

        $email = $request->email;

        // Check if user is already a member
        $existingUser = User::where('email', $email)->first();
        if ($existingUser && $colocation->users()->where('user_id', $existingUser->id)->exists()) {
            return back()->with('error', 'This user is already a member of the colocation.');
        }

        // Check if target user already has an active colocation membership
        if ($existingUser && $existingUser->hasActiveColocation()) {
            return back()->with('error', 'This user already has an active colocation membership and cannot join another colocation.');
        }

        // Check if invitation already exists
        if (Invitation::where('email', $email)
            ->where('colocation_id', $colocation->id)
            ->where('status', 'pending')
            ->exists()) {
            return back()->with('error', 'An invitation has already been sent to this email.');
        }

        // Create invitation
        $invitation = Invitation::create([
            'email' => $email,
            'token' => Str::uuid()->toString(),
            'colocation_id' => $colocation->id,
            'created_by' => auth()->id(),
            'status' => 'pending'
        ]);

        // Send invitation email
        try {
            Mail::to($email)->send(new \App\Mail\InvitationMail($invitation, null, $colocation));
            return back()->with('success', 'Invitation sent successfully!');
        } catch (\Exception $e) {
            // Log error but continue
            \Log::error('Failed to send invitation email: ' . $e->getMessage());
            return back()->with('success', 'Invitation created but email failed to send. Please try again.');
        }
    }

    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->with('colocation')
            ->firstOrFail();

        return view('invitations.accept', compact('invitation'));
    }

    public function confirmAccept(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('info', 'Please login to accept the invitation.');
        }

        // Verify email matches
        if (auth()->user()->email !== $invitation->email) {
            return back()->with('error', 'This invitation is for a different email address.');
        }

        $colocation = $invitation->colocation;

        // Check if user already has an active colocation membership
        if (auth()->user()->hasActiveColocation()) {
            $activeColocation = auth()->user()->getActiveColocation();
            return back()->with('error', 'You already have an active colocation membership. You cannot join another colocation while being part of an existing one.');
        }

        // Check if user has previously been a member of this colocation
        $previousMembership = \App\Models\ColocationMembershipHistory::where('user_id', auth()->id())
            ->where('colocation_id', $colocation->id)
            ->whereNotNull('left_at')
            ->first();

        if ($previousMembership) {
            return back()->with('error', 'You cannot rejoin a colocation you have previously left. You can only view its history from your old colocations.');
        }

        $newMember = auth()->user();
        
        try {
            DB::beginTransaction();
            
            // Get current member count before adding new member
            $currentMemberCount = $colocation->users()->count();
            
            // Calculate current balances before adding new member
            $currentBalances = app(\App\Services\BalanceCalculationService::class)->calculateMemberBalances($colocation);
            
            // Add user to colocation
            $colocation->users()->attach($newMember->id, [
                'colocation_role' => 'member',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create membership history record
            \App\Models\ColocationMembershipHistory::create([
                'user_id' => $newMember->id,
                'colocation_id' => $colocation->id,
                'colocation_role' => 'member',
                'joined_at' => now(),
            ]);

            // Redistribute existing debt among all members (including new member)
            $this->redistributeDebtAmongMembers($colocation, $currentBalances, $currentMemberCount, $newMember);

            // Update invitation status
            $invitation->update(['status' => 'accepted']);
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error joining colocation: ' . $e->getMessage());
        }

        return redirect()->route('colocations.show', $colocation->id)
            ->with('success', 'You have successfully joined the colocation!');
    }

    private function redistributeDebtAmongMembers($colocation, $currentBalances, $oldMemberCount, $newMember)
    {
        // Get all expenses in the colocation
        $expenses = $colocation->expenses()->with('participants')->get();
        
        if ($expenses->isEmpty()) {
            // No expenses to redistribute
            return;
        }
        
        // New total number of members
        $newMemberCount = $oldMemberCount + 1;
        
        // For each expense, redistribute it as if all members were present from the beginning
        foreach ($expenses as $expense) {
            // Calculate what each person should have paid if all members were present
            $amountPerMember = $expense->amount / $newMemberCount;
            
            // Clear the original expense distribution
            // For each participant, cancel out their original obligation
            foreach ($expense->participants as $participant) {
                $originalShare = $participant->pivot->share_amount;
                
                if ($participant->id == $expense->payer_id) {
                    // Original payer: They originally paid the full amount but had a share
                    // We need to refund their share since they'll pay the new amount
                    Payment::create([
                        'amount' => $originalShare,
                        'payer_id' => $expense->payer_id, // Refund to original payer
                        'receiver_id' => $participant->id, // From participant (same person)
                        'colocation_id' => $colocation->id,
                    ]);
                } else {
                    // Original participant (not payer): They originally owed their share
                    // We need to cancel this debt
                    Payment::create([
                        'amount' => $originalShare,
                        'payer_id' => $expense->payer_id, // Original payer cancels the debt
                        'receiver_id' => $participant->id, // Participant receives refund
                        'colocation_id' => $colocation->id,
                    ]);
                }
            }
            
            // Now redistribute the expense among all current members
            $allMembers = $colocation->users()->get();
            foreach ($allMembers as $member) {
                if ($member->id != $expense->payer_id) { // Only non-payers pay their share
                    // Everyone pays their new share to the original payer
                    Payment::create([
                        'amount' => $amountPerMember,
                        'payer_id' => $member->id, // Member pays
                        'receiver_id' => $expense->payer_id, // To original payer
                        'colocation_id' => $colocation->id,
                    ]);
                }
            }
        }
    }

    public function decline($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $invitation->update(['status' => 'declined']);

        return redirect()->route('dashboard')
            ->with('info', 'Invitation declined.');
    }

    public function cancel(Invitation $invitation)
    {
        // Load the colocation relationship
        $invitation->load('colocation');
        
        // Check if user is owner of the colocation
        $currentUserRole = $invitation->colocation->users()->where('user_id', auth()->id())->first()?->pivot->colocation_role;
        if ($currentUserRole !== 'owner') {
            return back()->with('error', 'Only the owner can cancel invitations.');
        }

        // Check if invitation is still pending
        if ($invitation->status !== 'pending') {
            return back()->with('error', 'This invitation cannot be cancelled.');
        }

        try {
            DB::beginTransaction();
            
            // Update invitation status
            $invitation->update(['status' => 'cancelled']);
            
            // No reputation change for cancelled invitations
            
            DB::commit();
            
            return back()->with('success', 'Invitation cancelled successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error cancelling invitation: ' . $e->getMessage());
        }
    }
}
