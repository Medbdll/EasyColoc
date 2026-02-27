<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Remove Member
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Remove Member</h2>
                            <p class="text-red-100 text-sm">{{ $colocation->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Member Info -->
                    <div class="flex items-center mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                            <span class="text-gray-600 font-semibold">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $member->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $member->email }}</p>
                        </div>
                    </div>

                    <!-- Alert -->
                    @if($hasDebt)
                        <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4 mb-6 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334.213 2.984.213 4.346 0l5.58-9.92c.75-1.334-.213-2.984-.213-4.346 0L8.257 3.099z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Debt Transfer Required</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Member has <strong>€{{ number_format(abs($memberBalance), 2) }}</strong> debt</p>
                                        <p class="mt-1">This debt will be transferred to you as the colocation owner.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="border-l-4 border-green-400 bg-green-50 p-4 mb-6 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Clean Removal</h3>
                                    <p class="mt-2 text-sm text-green-700">This member has no debt and can be removed without financial adjustments.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <form action="{{ route('colocations.remove', [$colocation, $member]) }}?confirm=1" method="POST">
                        @csrf
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('colocations.show', $colocation) }}" 
                               class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2.5 rounded-lg text-white font-medium transition-all transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 {{ $hasDebt ? 'bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700' : 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700' }}">
                                {{ $hasDebt ? 'Accept Debt & Remove' : 'Remove Member' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
