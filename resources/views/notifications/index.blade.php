<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - HRM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="flex h-screen overflow-hidden">
        @include('components.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden ml-20">
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 md:px-6 py-3 md:py-4 flex items-center justify-between">
                <h2 class="text-lg md:text-xl lg:text-2xl font-semibold">Notifications</h2>

                <div class="flex items-center space-x-2 md:space-x-4">
                    @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.markAllRead') }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs md:text-sm">
                            Mark All Read
                        </button>
                    </form>
                    @endif

                    <button @click="darkMode = !darkMode" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg x-show="!darkMode" class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center space-x-2 px-3 md:px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm md:text-base">
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @if(session('success'))
                <div class="mb-4 md:mb-6 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
                @endif

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
            </main>
        </div>
    </div>
</body>
</html>
