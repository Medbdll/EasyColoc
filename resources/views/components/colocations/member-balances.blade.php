@props(['memberBalances', 'colocation'])

@php
    $currentUserRole = $colocation->users()->where('user_id', Auth::id())->first()?->pivot->colocation_role;
@endphp

<div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden mt-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Member Balances</h3>
            <span class="bg-white bg-opacity-20 text-white text-sm px-3 py-1 rounded-full">
                {{ count($memberBalances) }} member{{ count($memberBalances) > 1 ? 's' : '' }}
            </span>
        </div>
    </div>
    
    <!-- Balances List -->
    <div class="p-6">
        <div class="space-y-4">
            @foreach($memberBalances as $memberId => $balance)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                    <!-- User Info -->
                    <div class="flex items-center flex-1">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center shadow-lg mr-3">
                            <span class="text-white font-bold text-sm">{{ strtoupper(substr($balance['name'], 0, 1)) }}</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $balance['name'] }}</div>
                        </div>
                    </div>
                    
                    <!-- Balance -->
                    <div class="text-right ml-4">
                        @if($balance['balance'] > 0)
                            <div class="text-lg font-bold text-green-600">+€{{ number_format($balance['balance'], 2) }}</div>
                            <div class="text-xs text-green-500 font-medium">To receive</div>
                        @elseif($balance['balance'] < 0)
                            <div class="flex items-center justify-end">
                                <div>
                                    <div class="text-lg font-bold text-red-600">€{{ number_format(abs($balance['balance']), 2) }}</div>
                                    <div class="text-xs text-red-500 font-medium">To pay</div>
                                </div>
                                @if($currentUserRole === 'owner' && $memberId !== Auth::id())
                                    <form action="{{ route('members.mark-as-paid', [$colocation, $memberId]) }}" method="POST" class="ml-3">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors"
                                                onclick="return confirm('Mark this member\'s debt as paid? This will reset their balance to €0.00.')">
                                            Mark as Paid
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @else
                            <div class="text-lg font-bold text-gray-600">€0.00</div>
                            <div class="text-xs text-gray-500 font-medium">Balanced</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
