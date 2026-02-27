@props(['colocation'])

<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Categories</h3>
        <button onclick="showCategoryModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
            Add Category
        </button>
    </div>
    <div class="p-6">
        @if($colocation->categories->count() > 0)
            <div class="space-y-2">
                @foreach($colocation->categories as $category)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-900">{{ $category->name }}</span>
                            <span class="ml-2 text-xs text-gray-500">({{ $category->expenses->count() }} expenses)</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-green-600">€{{ number_format($category->getTotalExpenses(), 2) }}</span>
                            @if($category->expenses->count() == 0)
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure you want to delete this category?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No categories yet. <button onclick="showCategoryModal()" class="text-purple-600 hover:text-purple-800">Create your first category</button>.</p>
        @endif
    </div>
</div>
