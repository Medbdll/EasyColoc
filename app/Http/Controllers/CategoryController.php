<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'colocation_id' => 'required|exists:colocations,id',
        ]);

        // Check if user is owner of the colocation
        $colocation = Colocation::findOrFail($request->colocation_id);
        $currentUserRole = $colocation->users()->where('user_id', Auth::id())->first()?->pivot->colocation_role;
        if ($currentUserRole !== 'owner') {
            return redirect()->back()->with('error', 'Only the owner can create categories.');
        }

        // Check if category already exists for this colocation
        $existingCategory = Category::where('name', $request->name)
            ->where('colocation_id', $request->colocation_id)
            ->first();
        
        if ($existingCategory) {
            return redirect()->back()->with('error', 'Category already exists.');
        }

        Category::create([
            'name' => $request->name,
            'colocation_id' => $request->colocation_id,
        ]);

        return redirect()->back()->with('success', 'Category created successfully!');
    }

    public function destroy(Category $category)
    {
        // Check if user is owner of the colocation
        $currentUserRole = $category->colocation->users()->where('user_id', Auth::id())->first()?->pivot->colocation_role;
        if ($currentUserRole !== 'owner') {
            return redirect()->back()->with('error', 'Only the owner can delete categories.');
        }

        // Check if category has expenses
        if ($category->expenses()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with expenses.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}
