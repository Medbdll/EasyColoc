<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $colocation->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $colocation->name }}</h3>
                            @if($colocation->description)
                                <p class="mt-1 text-sm text-gray-600">{{ $colocation->description }}</p>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="showInviteModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                Invite Members
                            </button>
                        </div>
                    </div>
                    
                    <x-colocations.statistics-cards :colocation="$colocation" />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-colocations.member-list :colocation="$colocation" />
                <x-categories.category-list :colocation="$colocation" />
            </div>
            
            @php
                $balanceService = new \App\Services\BalanceCalculationService();
                $memberBalances = $balanceService->calculateMemberBalances($colocation);
                $repayments = $balanceService->calculateRepayments($memberBalances);
            @endphp
            
            <x-colocations.member-balances :memberBalances="$memberBalances" />
            <x-colocations.repayment-summary :repayments="$repayments" />

            <x-expenses.expense-list :colocation="$colocation" />

            <x-colocations.invite-modal :colocation="$colocation" />
            <x-categories.category-modal :colocation="$colocation" />
            <x-expenses.expense-modal :colocation="$colocation" />
        </div>
    </div>

    @if(session('success'))
        <div class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50">
            {{ session('error') }}
        </div>
    @endif

    @vite(['resources/js/app.js'])
</x-app-layout>