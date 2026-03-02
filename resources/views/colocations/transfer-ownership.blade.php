@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Transfer Ownership Before Leaving
            </h3>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            You are the owner of this colocation
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Before you can leave, you must transfer ownership to another member.</p>
                            @if($hasDebt)
                                <p class="mt-1"><strong>Note:</strong> You have a debt of €{{ number_format(abs($memberBalance), 2) }} that will be transferred to the new owner.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
                @csrf
                
                <div class="mb-6">
                    <label for="transfer_to" class="block text-sm font-medium text-gray-700 mb-2">
                        Select New Owner
                    </label>
                    <select name="transfer_to" id="transfer_to" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Choose a member to transfer ownership to...</option>
                        @foreach($otherMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('colocations.show', $colocation) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Continue to Confirmation
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
@endsection
