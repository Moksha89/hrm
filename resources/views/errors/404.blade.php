<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-100 dark:bg-neutral-950 transition-colors duration-200">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full text-center">
            <div class="bg-white dark:bg-neutral-900 shadow-xl rounded-xl border border-neutral-200 dark:border-neutral-800 p-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-amber-500 rounded-full mb-6">
                    <svg class="w-12 h-12 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                <h1 class="text-6xl font-bold text-amber-500 mb-2">404</h1>
                <h2 class="text-2xl font-semibold text-neutral-900 dark:text-white mb-4">Page Not Found</h2>
                <p class="text-neutral-600 dark:text-neutral-400 mb-8">
                    Sorry, we couldn't find the page you're looking for. It might have been moved or deleted.
                </p>
                
                <div class="space-y-3">
                    <a href="{{ url('/dashboard') }}" class="block w-full bg-amber-500 hover:bg-amber-400 text-black font-semibold py-3 px-4 rounded-lg transition-colors duration-200">
                        Go to Dashboard
                    </a>
                    <button onclick="history.back()" class="block w-full bg-neutral-200 dark:bg-neutral-800 hover:bg-neutral-300 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                        Go Back
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
