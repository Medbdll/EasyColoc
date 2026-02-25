<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $colocation->load(['users', 'expenses.user', 'invitations']);

        return view('colocations.show', compact('colocation'));
    }


   
}
