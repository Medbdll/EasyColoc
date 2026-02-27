@props(['colocation'])

<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-6">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Recent Expenses</h3>
        <button onclick="showExpenseModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
            Add Expense
        </button>
    </div>
    <div class="p-6">
        @if($colocation->expenses->count() > 0)
            <div class="space-y-3">
                @foreach($colocation->expenses->take(5) as $expense)
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <p class="text-sm font-medium text-gray-900">{{ $expense->description }}</p>
                                    @if($expense->category)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $expense->category->name }}
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-1 flex items-center text-xs text-gray-500">
                                    <span>Paid by {{ $expense->payer->name }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $expense->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900">€{{ number_format($expense->amount, 2) }}</div>
                                <div class="flex items-center space-x-2">
                                    <a href="#" class="text-xs text-blue-600 hover:text-blue-800">View</a>
                                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure you want to delete this expense?')">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Individual member shares -->
                        <div class="border-t border-gray-100 pt-3 mt-3">
                            <div class="text-xs text-gray-500 mb-2">Individual Shares:</div>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($expense->participants as $participant)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <span class="text-xs font-medium text-gray-700">{{ $participant->name }}</span>
                                        <span class="text-xs font-semibold text-green-600">€{{ number_format($participant->pivot->share_amount, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($colocation->expenses->count() > 5)
                <div class="mt-4 text-center">
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all {{ $colocation->expenses->count() }} expenses →
                    </a>
                </div>
            @endif
        @else
            <p class="text-gray-500 text-center py-8">No expenses yet. <button onclick="showExpenseModal()" class="text-blue-600 hover:text-blue-800">Add your first expense</button>.</p>
        @endif
    </div>
</div>
