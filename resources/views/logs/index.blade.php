@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">Audit Logs</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Track all changes and activities in the portal</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form action="{{ route('logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description..." class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Module</label>
                <select name="module" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $module)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Action</label>
                <select name="action" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
                <select name="user_id" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div class="lg:col-span-6 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition text-sm font-medium">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('logs.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition text-sm font-medium">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($logs->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date/Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Module</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Reason</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white whitespace-nowrap">
                            <div>{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-amber-500 flex items-center justify-center text-white text-xs font-medium mr-2">
                                    {{ strtoupper(substr($log->user_name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium">{{ $log->user_name ?? 'System' }}</div>
                                    @if($log->ip_address)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->ip_address }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ $log->module_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full {{ $log->action_color }}">
                                {{ $log->action_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white max-w-xs truncate" title="{{ $log->description }}">
                            {{ Str::limit($log->description, 50) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate" title="{{ $log->reason }}">
                            {{ $log->reason ? Str::limit($log->reason, 30) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('logs.show', $log->id) }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 font-medium">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $logs->withQueryString()->links() }}
        </div>
        @else
        <div class="p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="mt-2 text-gray-600 dark:text-gray-400">No audit logs found.</p>
            <p class="text-sm text-gray-500 dark:text-gray-500">Logs will appear here as changes are made in the portal.</p>
        </div>
        @endif
    </div>
</div>
@endsection
