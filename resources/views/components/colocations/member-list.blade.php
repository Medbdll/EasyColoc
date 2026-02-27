@props(['colocation'])

<div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Members</h3>
            <span class="bg-white bg-opacity-20 text-white text-sm px-3 py-1 rounded-full">
                {{ $colocation->users->count() }} member{{ $colocation->users->count() > 1 ? 's' : '' }}
            </span>
        </div>
    </div>
    
    <!-- Members List -->
    <div class="p-6">
        @if($colocation->users->count() > 0)
            <div class="space-y-4">
                @foreach($colocation->users as $user)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all duration-200">
                        <!-- User Info -->
                        <div class="flex items-center flex-1">
                            <div class="relative">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-white font-bold text-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                @if($user->pivot->colocation_role === 'owner')
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center">
                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 01.95.69h1.952c.9 0 1.65-.672 1.65-1.65V8.85a1.1 1.1 0 00-.025-.316l-1.07-3.292A1.1 1.1 0 009.049 2.927zM7.752 6.8a1.1 1.1 0 00-1.1 1.1v4.296a1.1 1.1 0 001.1 1.1h.003v-6.5z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <h4 class="font-semibold text-gray-900">{{ $user->name }}</h4>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $user->pivot->colocation_role === 'owner' ? 'bg-gradient-to-r from-yellow-400 to-orange-400 text-white' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($user->pivot->colocation_role) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center space-x-2 ml-4">
                            @if(auth()->user()->id !== $user->id)
                                @if(auth()->user()->id === $colocation->users()->where('colocation_role', 'owner')->first()->id)
                                    <button onclick="confirmRemove('{{ $user->name }}', '{{ route('colocations.remove', [$colocation, $user]) }}')" 
                                            class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 transition-all duration-200 text-sm font-medium">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Remove
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('colocations.leave', $colocation) }}" 
                                   class="px-4 py-2 bg-orange-50 text-orange-600 border border-orange-200 rounded-lg hover:bg-orange-100 hover:border-orange-300 transition-all duration-200 text-sm font-medium inline-block">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4 4m4-4H3m2 4h13a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                    </svg>
                                    Leave
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1m0 0l6-6m-6 6h6m-6 6h6"/>
                </svg>
                <p class="text-gray-500">No members yet</p>
                <p class="text-sm text-gray-400 mt-2">Invite members to get started</p>
            </div>
        @endif
    </div>
</div>

<script>
function confirmRemove(memberName, url) {
    if (confirm(`Are you sure you want to remove ${memberName} from the colocation? This action cannot be undone.`)) {
        window.location.href = url;
    }
}
</script>
