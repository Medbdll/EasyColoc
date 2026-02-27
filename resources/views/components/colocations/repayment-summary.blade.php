@props(['repayments'])

<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-6">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Repayment Summary</h3>
    </div>
    <div class="p-6">
        @if(empty($repayments))
            <p class="text-gray-500 text-center py-4">All balances are settled!</p>
        @else
            <div class="space-y-2">
                @foreach($repayments as $repayment)
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="text-sm">
                                <span class="font-medium text-gray-900">{{ $repayment['from'] }}</span>
                                <span class="mx-2 text-gray-500">→</span>
                                <span class="font-medium text-gray-900">{{ $repayment['to'] }}</span>
                            </div>
                        </div>
                        <div class="text-sm font-semibold text-blue-600">€{{ number_format($repayment['amount'], 2) }}</div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 p-3 bg-yellow-50 rounded-lg">
                <p class="text-xs text-yellow-800">
                    <strong>Note:</strong> These are suggested repayments to settle all balances. Members can arrange actual payments as needed.
                </p>
            </div>
        @endif
    </div>
</div>
