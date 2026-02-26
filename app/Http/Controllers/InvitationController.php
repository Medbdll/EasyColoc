<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
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
        
        // Check if user is member of this colocation
        if (!$colocation->users()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You are not a member of this colocation.');
        }

        $email = $request->email;

        // Check if user is already a member
        $existingUser = User::where('email', $email)->first();
        if ($existingUser && $colocation->users()->where('user_id', $existingUser->id)->exists()) {
            return back()->with('error', 'This user is already a member of the colocation.');
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
        
        // Add user to colocation
        $colocation->users()->attach(auth()->id(), [
            'colocation_role' => 'member',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Update invitation status
        $invitation->update(['status' => 'accepted']);

        return redirect()->route('colocations.show', $colocation->id)
            ->with('success', 'You have successfully joined the colocation!');
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
}
