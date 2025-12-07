@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Notifications</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Stay updated with your latest notifications</p>
        </div>
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.markAllRead') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm transition-colors">
                Mark All Read
            </button>
        </form>
        @endif
    </div>

    <div class="space-y-3 md:space-y-4">
                    @forelse($notifications as $notification)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 md:p-6 {{ $notification->read_at ? 'opacity-60' : 'border-l-4 border-blue-500' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-2 py-1 text-xs md:text-sm font-semibold rounded-full
                                        @if($notification->type === 'request_created') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($notification->type === 'request_approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($notification->type === 'request_rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                    </span>
                                    @if(!$notification->read_at)
                                    <span class="px-2 py-1 text-xs bg-blue-600 text-white rounded-full">New</span>
                                    @endif
                                </div>
                                <p class="text-sm md:text-base mb-2">{{ $notification->message }}</p>
                                <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notification->read_at)
                            <form method="POST" action="{{ route('notifications.markRead', $notification->id) }}">
                                @csrf
                                <button type="submit" class="ml-4 px-3 py-1 text-xs md:text-sm bg-blue-600 hover:bg-blue-700 text-white rounded">
                                    Mark Read
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center">
                        <p class="text-gray-500 dark:text-gray-400 text-sm md:text-base">No notifications yet</p>
                    </div>
                    @endforelse
                </div>

    @if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
