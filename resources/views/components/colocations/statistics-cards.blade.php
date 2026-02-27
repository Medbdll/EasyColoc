@props(['colocation'])

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    <div class="bg-blue-50 p-4 rounded-lg">
        <div class="text-2xl font-bold text-blue-600">{{ $colocation->users->count() }}</div>
        <div class="text-sm text-blue-800">Members</div>
    </div>
    <div class="bg-green-50 p-4 rounded-lg">
        <div class="text-2xl font-bold text-green-600">{{ $colocation->expenses->count() }}</div>
        <div class="text-sm text-green-800">Expenses</div>
    </div>
    <div class="bg-purple-50 p-4 rounded-lg">
        <div class="text-2xl font-bold text-purple-600">€{{ number_format($colocation->expenses->sum('amount'), 2) }}</div>
        <div class="text-sm text-purple-800">Total Expenses</div>
    </div>
</div>
