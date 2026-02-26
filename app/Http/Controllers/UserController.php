<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
   public function index()
   {
       if (Auth::check() && Auth::user()->status === 'banned') {
           Auth::guard('web')->logout();
           session()->invalidate();
           session()->regenerateToken();
           return redirect('/login')->with('error', 'Your account has been banned.');
       }
       
       $users = User::where('role', 'user')->get();
       return view('dashboard', compact('users'));
   }
   
   public function ban($id)
   {
       $user = User::find($id);
       $user->update(['status' => 'banned']);
       
       if (Auth::check() && Auth::user()->id == $id) {
           Auth::guard('web')->logout();
           session()->invalidate();
           session()->regenerateToken();
           return redirect('/login')->with('error', 'Your account has been banned.');
       }
       
       return redirect()->route('dashboard');
   }
   
   public function unban($id)
   {
       $user = User::find($id);
       $user->update(['status' => 'active']);
       return redirect()->route('dashboard');
   }
}
