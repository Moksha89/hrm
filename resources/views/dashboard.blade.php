<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen">
        <nav class="bg-white dark:bg-gray-800 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ config('app.name') }} Dashboard</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Welcome, {{ Auth::user()->name }}!</h2>
                <p class="text-gray-600 dark:text-gray-400">You are now logged into the HRM system.</p>
                <div class="mt-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Mobile: {{ Auth::user()->mobile }}</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
