<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @auth
                @php
                    $userColocation = Auth::user()->colocations()->withCount('users', 'expenses')->first();
                @endphp
                
                @if($userColocation)
                    <!-- User has a colocation - show overview -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 card-hover">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $userColocation->name }}</h3>
                                    <p class="text-sm text-gray-500">Your colocation</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('colocations.show', $userColocation->id) }}" 
                                   class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    View Colocation
                                </a>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 card-hover">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ $userColocation->users_count }}</div>
                                    <p class="text-sm text-gray-500">Members</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 card-hover">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ $userColocation->expenses_count }}</div>
                                    <p class="text-sm text-gray-500">Expenses</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                        </div>
                        <div class="p-6">
                            @if($userColocation->expenses->count() > 0)
                                <div class="space-y-3">
                                    @foreach($userColocation->expenses->take(5) as $expense)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $expense->description }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $expense->user->name }} • {{ $expense->date->format('M d, Y') }}
                                                </div>
                                            </div>
                                            <div class="text-sm font-medium text-gray-900">
                                                €{{ number_format($expense->amount, 2) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 text-center">
                                    <a href="{{ route('colocations.show', $userColocation->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View all activity
                                    </a>
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-8">No expenses yet. <a href="{{ route('colocations.show', $userColocation->id) }}" class="text-blue-600 hover:text-blue-800">Add your first expense</a>.</p>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- User doesn't have a colocation - show welcome/create -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="px-6 py-8 text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Welcome to EasyColoc!</h3>
                            <p class="text-gray-500 mb-6">Get started by creating your first colocation to manage expenses and invite members.</p>
                            <a href="{{ route('colocations.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Create Your Colocation
                            </a>
                        </div>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</x-app-layout>
