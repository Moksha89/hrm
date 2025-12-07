<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-100 dark:bg-neutral-950 transition-colors duration-200">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full text-center">
            <div class="bg-white dark:bg-neutral-900 shadow-xl rounded-xl border border-neutral-200 dark:border-neutral-800 p-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-500 rounded-full mb-6">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                
                <h1 class="text-6xl font-bold text-red-500 mb-2">500</h1>
                <h2 class="text-2xl font-semibold text-neutral-900 dark:text-white mb-4">Server Error</h2>
                <p class="text-neutral-600 dark:text-neutral-400 mb-8">
                    Something went wrong on our end. Our team has been notified and is working to fix the issue.
                </p>
                
                <div class="space-y-3">
                    <a href="{{ url('/dashboard') }}" class="block w-full bg-amber-500 hover:bg-amber-400 text-black font-semibold py-3 px-4 rounded-lg transition-colors duration-200">
                        Go to Dashboard
                    </a>
                    <button onclick="location.reload()" class="block w-full bg-neutral-200 dark:bg-neutral-800 hover:bg-neutral-300 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-300 font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                        Try Again
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
