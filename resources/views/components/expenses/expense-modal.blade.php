@props(['colocation'])

<div id="expenseModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 glass-effect rounded-2xl shadow-2xl w-full max-w-md slide-up">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    Add Expense
                </h3>
                <button onclick="hideExpenseModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <input type="hidden" name="colocation_id" value="{{ $colocation->id }}">
                
                <div class="mb-5">
                    <label for="expense_description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Description
                    </label>
                    <input 
                        type="text" 
                        id="expense_description" 
                        name="description" 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 bg-white/80 backdrop-blur-sm transition-all"
                        placeholder="e.g., Grocery shopping, Gas bill, Restaurant dinner"
                        required>
                </div>
                
                <div class="mb-5">
                    <label for="expense_date" class="block text-sm font-semibold text-gray-700 mb-2">
                        Date
                    </label>
                    <input 
                        type="date" 
                        id="expense_date" 
                        name="date" 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 bg-white/80 backdrop-blur-sm transition-all"
                        value="{{ now()->format('Y-m-d') }}"
                        required>
                </div>
                
                <div class="mb-5">
                    <label for="expense_amount" class="block text-sm font-semibold text-gray-700 mb-2">
                        Amount (€)
                    </label>
                    <input 
                        type="number" 
                        id="expense_amount" 
                        name="amount" 
                        step="0.01"
                        min="0.01"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 bg-white/80 backdrop-blur-sm transition-all"
                        placeholder="0.00"
                        required>
                </div>
                
                <div class="mb-5">
                    <label for="expense_category" class="block text-sm font-semibold text-gray-700 mb-2">
                        Category
                    </label>
                    <select 
                        id="expense_category" 
                        name="category_id" 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 bg-white/80 backdrop-blur-sm transition-all">
                        <option value="">Select a category</option>
                        @foreach($colocation->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-5">
                    <label for="expense_payer" class="block text-sm font-semibold text-gray-700 mb-2">
                        Paid By
                    </label>
                    <select 
                        id="expense_payer" 
                        name="payer_id" 
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 bg-white/80 backdrop-blur-sm transition-all"
                        required>
                        <option value="">Select who paid</option>
                        @foreach($colocation->users as $user)
                            <option value="{{ $user->id }}" {{ $user->id == auth()->id() ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <input type="hidden" name="participants[]" value="{{ $colocation->users->pluck('id')->implode(',') }}">
                <input type="hidden" name="auto_split" value="true">
                
                <div class="flex justify-end space-x-3">
                    <button 
                        type="button" 
                        onclick="hideExpenseModal()" 
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium">
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-105">
                        Add Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
