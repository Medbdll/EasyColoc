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
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Members</h3>
                    </div>
                    <div class="p-6">
                        @if($colocation->users->count() > 0)
                            <div class="space-y-3">
                                @foreach($colocation->users as $user)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $user->pivot->colocation_role === 'owner' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($user->pivot->colocation_role) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No members yet</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Pending Invitations</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-500 text-center py-4">No pending invitations</p>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-6">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Recent Expenses</h3>
                    <a href="#" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        Add Expense
                    </a>
                </div>
                <div class="p-6">
                    <p class="text-gray-500 text-center py-8">No expenses yet. <a href="#" class="text-blue-600 hover:text-blue-800">Add your first expense</a>.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="inviteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Invite Members</h3>
                    <button onclick="hideInviteModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form action="" method="POST">
                    @csrf
                    <input type="hidden" name="colocation_id" value="{{ $colocation->id }}">
                    
                    <div class="mb-4">
                        <label for="emails" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Addresses (comma separated)
                        </label>
                        <textarea 
                            id="emails" 
                            name="emails" 
                            rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="friend1@example.com, friend2@example.com"
                            required></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Personal Message (optional)
                        </label>
                        <textarea 
                            id="message" 
                            name="message" 
                            rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Join our colocation!"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button 
                            type="button" 
                            onclick="hideInviteModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Send Invitations
                        </button>
                    </div>
                </form>
            </div>
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

    <script>
        function showInviteModal() {
            document.getElementById('inviteModal').classList.remove('hidden');
        }
        
        function hideInviteModal() {
            document.getElementById('inviteModal').classList.add('hidden');
        }
        
        setTimeout(() => {
            const notifications = document.querySelectorAll('.fixed.bottom-4.right-4');
            notifications.forEach(notification => {
                notification.style.display = 'none';
            });
        }, 5000);
    </script>
</x-app-layout>