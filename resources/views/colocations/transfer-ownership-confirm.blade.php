@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Confirm Ownership Transfer and Leave
            </h3>
            
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            This action cannot be undone
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>You are about to:</p>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li>Transfer ownership of "{{ $colocation->name }}" to {{ $newOwner->name }}</li>
                                <li>Leave the colocation permanently</li>
                                @if($hasDebt)
                                    <li>Transfer your €{{ number_format(abs($memberBalance), 2) }} debt to the new owner</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-md p-4 mb-6">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Transfer Details:</h4>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Colocation:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $colocation->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">New Owner:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $newOwner->name }} ({{ $newOwner->email }})</dd>
                    </div>
                    @if($hasDebt)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Your Debt:</dt>
                            <dd class="text-sm font-medium text-red-600">€{{ number_format(abs($memberBalance), 2) }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
                @csrf
                <input type="hidden" name="transfer_to" value="{{ $newOwner->id }}">
                <input type="hidden" name="confirm_leave" value="1">
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('colocations.show', $colocation) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Transfer Ownership and Leave
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
@endsection
