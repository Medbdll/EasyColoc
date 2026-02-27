@props(['memberBalances'])

<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-6">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Member Balances</h3>
    </div>
    <div class="p-6">
        <div class="space-y-3">
            @foreach($memberBalances as $memberId => $balance)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                            <span class="text-xs font-medium text-gray-700">
                                {{ strtoupper(substr($balance['name'], 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $balance['name'] }}</div>
                            <div class="text-xs text-gray-500">
                                Paid: €{{ number_format($balance['total_paid'], 2) }} | Owed: €{{ number_format($balance['total_owed'], 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($balance['balance'] > 0)
                            <div class="text-sm font-semibold text-green-600">+€{{ number_format($balance['balance'], 2) }}</div>
                            <div class="text-xs text-green-500">To receive</div>
                        @elseif($balance['balance'] < 0)
                            <div class="text-sm font-semibold text-red-600">€{{ number_format($balance['balance'], 2) }}</div>
                            <div class="text-xs text-red-500">To pay</div>
                        @else
                            <div class="text-sm font-semibold text-gray-600">€0.00</div>
                            <div class="text-xs text-gray-500">Balanced</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
