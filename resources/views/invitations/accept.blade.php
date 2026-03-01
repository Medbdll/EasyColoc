@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">
                            You're Invited to Join!
                        </h1>
                        
                        <p class="text-gray-600 mb-6">
                            You've been invited to join the colocation <strong>{{ $invitation->colocation->name }}</strong>
                        </p>

                        @if($invitation->colocation->description)
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h3 class="font-semibold text-gray-900 mb-2">About this colocation:</h3>
                                <p class="text-gray-600">{{ $invitation->colocation->description }}</p>
                            </div>
                        @endif

                        <div class="bg-blue-50 rounded-lg p-4 mb-6">
                            <h3 class="font-semibold text-blue-900 mb-2">Colocation Details:</h3>
                            <div class="text-left text-sm text-blue-800 space-y-1">
                                <p><strong>Name:</strong> {{ $invitation->colocation->name }}</p>
                                <p><strong>Members:</strong> {{ $invitation->colocation->users->count() }} member(s)</p>
                                <p><strong>Invited by:</strong> {{ $invitation->createdBy->name ?? 'A member' }}</p>
                            </div>
                        </div>

                        @if(auth()->check())
                            @if(auth()->user()->email === $invitation->email)
                                <form action="{{ route('invitations.confirm', $invitation->token) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div class="flex space-x-4 justify-center">
                                        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                            Accept Invitation
                                        </button>
                                        <a href="{{ route('invitations.decline', $invitation->token) }}" 
                                           class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                                            Decline
                                        </a>
                                    </div>
                                </form>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <p class="text-yellow-800">
                                        This invitation was sent to <strong>{{ $invitation->email }}</strong>, 
                                        but you're logged in as <strong>{{ auth()->user()->email }}</strong>.
                                    </p>
                                    <p class="text-yellow-700 text-sm mt-2">
                                        Please log out and log back in with the correct email address, or 
                                        contact the person who invited you.
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="space-y-4">
                                <p class="text-gray-600">
                                    Please log in or register to accept this invitation.
                                </p>
                                <div class="flex space-x-4 justify-center">
                                    <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                        Login
                                    </a>
                                    <a href="{{ route('register') }}" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                        Register
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
