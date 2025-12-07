<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-100 dark:bg-neutral-950 transition-colors duration-200">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full text-center">
            <div class="bg-white dark:bg-neutral-900 shadow-xl rounded-xl border border-neutral-200 dark:border-neutral-800 p-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-amber-500 rounded-full mb-6">
                    <svg class="w-12 h-12 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                
                <h1 class="text-6xl font-bold text-amber-500 mb-2">403</h1>
                <h2 class="text-2xl font-semibold text-neutral-900 dark:text-white mb-4">Access Denied</h2>
                <p class="text-neutral-600 dark:text-neutral-400 mb-8">
                    You don't have permission to access this page. Please contact your administrator if you believe this is an error.
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
