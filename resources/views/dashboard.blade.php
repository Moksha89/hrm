<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8fafc] dark:bg-gray-900 overflow-hidden">
    <div class="flex h-screen">
        <x-sidebar />

        <div class="flex-1 flex flex-col ml-20">
            <x-header breadcrumb="Dashboard" />

            <main class="flex-1 overflow-y-auto mt-16 p-6">
                <div class="max-w-7xl mx-auto">
                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back, {{ Auth::user()->name }}!</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Employees</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">0</p>
                                    </div>
                                    <div class="w-12 h-12 bg-teal-100 dark:bg-teal-900/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Active Teams</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">0</p>
                                    </div>
                                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Pending Requests</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">0</p>
                                    </div>
                                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Payments</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">₹0</p>
                                    </div>
                                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Quick Overview</h2>
                            <p class="text-gray-600 dark:text-gray-400">Welcome to the HRM system dashboard. Use the sidebar to navigate through different modules and manage your organization efficiently.</p>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
