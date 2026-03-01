<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Expenses - {{ $colocation->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Amount</p>
                            <p class="text-2xl font-bold text-gray-900">€{{ number_format($totalStats['total_amount'], 2) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Expenses</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalStats['total_count'] }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Average Amount</p>
                            <p class="text-2xl font-bold text-gray-900">€{{ number_format($totalStats['average_amount'], 2) }}</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Filter</p>
                            <form method="GET" action="{{ route('expenses.index', $colocation) }}">
                                <select name="month" onchange="this.form.submit()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    @foreach($monthlyOptions as $value => $label)
                                        <option value="{{ $value }}" {{ $monthFilter == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="bg-orange-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Statistics -->
            @if(!empty($categoryStats))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">By Category</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($categoryStats as $stat)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-900">{{ $stat['name'] ?? 'Uncategorized' }}</p>
                                <p class="text-sm text-gray-500">{{ $stat['count'] }} expense{{ $stat['count'] > 1 ? 's' : '' }}</p>
                            </div>
                            <p class="text-lg font-bold text-blue-600">€{{ number_format($stat['total'], 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Monthly Statistics -->
            @if(!empty($monthlyStats))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Overview</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($monthlyStats as $stat)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-900">{{ $stat['month_name'] }}</p>
                                <p class="text-sm text-gray-500">{{ $stat['count'] }} expense{{ $stat['count'] > 1 ? 's' : '' }}</p>
                            </div>
                            <p class="text-lg font-bold text-green-600">€{{ number_format($stat['total'], 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Expenses List -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">Expense History</h3>
                        <button onclick="showExpenseModal()" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Add Expense
                        </button>
                    </div>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @forelse($expenses as $expense)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900">{{ $expense->description }}</h4>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <p class="text-sm text-gray-500">
                                                <span class="font-medium">{{ $expense->date->format('M d, Y') }}</span>
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Paid by <span class="font-medium">{{ $expense->payer->name }}</span>
                                            </p>
                                            @if($expense->category)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $expense->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                @if($expense->participants->count() > 0)
                                <div class="mt-3">
                                    <p class="text-sm text-gray-500">Split between:</p>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @foreach($expense->participants as $participant)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-gray-100 text-gray-700">
                                            {{ $participant->name }}: €{{ number_format($participant->pivot->share_amount, 2) }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-4 ml-6">
                                <div class="text-right">
                                    <p class="text-xl font-bold text-gray-900">€{{ number_format($expense->amount, 2) }}</p>
                                    @if($expense->payer_id === auth()->id())
                                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="mt-2" onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-gray-500">No expenses found</p>
                        <p class="text-sm text-gray-400 mt-2">Start by adding your first expense</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Include the expense modal -->
    <x-expenses.expense-modal :colocation="$colocation" />
</x-app-layout>
