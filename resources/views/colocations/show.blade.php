@extends('layouts.app')

@section('content')
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
                        @php
                            $currentUserRole = $colocation->users()->where('user_id', Auth::id())->first()?->pivot->colocation_role;
                        @endphp
                        @if($currentUserRole === 'owner')
                            <div class="flex space-x-2">
                                <button onclick="showInviteModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Invite Members
                                </button>
                            </div>
                        @endif
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
            
            <x-colocations.member-balances :memberBalances="$memberBalances" :colocation="$colocation" />
            <x-colocations.repayment-summary :repayments="$repayments" />

            <div class="mb-6">
                <a href="{{ route('expenses.index', $colocation) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg text-sm font-semibold transition-all transform hover:scale-105 shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    View All Expenses
                </a>
            </div>

            <x-expenses.expense-list :colocation="$colocation" />

            @if($currentUserRole === 'owner')
                <x-colocations.invite-modal :colocation="$colocation" />
                <x-categories.category-modal :colocation="$colocation" />
            @endif
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
@endsection