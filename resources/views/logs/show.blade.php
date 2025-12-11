@extends('layouts.app')

@section('title', 'Log Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">Log Details</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $log->created_at->format('d M Y H:i:s') }}</p>
        </div>
        <a href="{{ route('logs.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition text-sm">
            Back to Logs
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-amber-500 flex items-center justify-center text-white font-medium mr-3">
                        {{ strtoupper(substr($log->user_name ?? 'S', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $log->user_name ?? 'System' }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                        {{ $log->module_label }}
                    </span>
                    <span class="px-2 py-1 text-xs rounded-full {{ $log->action_color }}">
                        {{ $log->action_label }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">Description</h3>
                <p class="text-gray-900 dark:text-white">{{ $log->description }}</p>
            </div>

            @if($log->reason)
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">Reason</h3>
                <p class="text-gray-900 dark:text-white bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-3">{{ $log->reason }}</p>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">Details</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Log ID:</dt>
                            <dd class="text-gray-900 dark:text-white font-mono">#{{ $log->id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Module:</dt>
                            <dd class="text-gray-900 dark:text-white">{{ $log->module_label }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Action:</dt>
                            <dd class="text-gray-900 dark:text-white">{{ $log->action_label }}</dd>
                        </div>
                        @if($log->model_type)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Model:</dt>
                            <dd class="text-gray-900 dark:text-white font-mono text-xs">{{ class_basename($log->model_type) }}</dd>
                        </div>
                        @endif
                        @if($log->model_id)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Record ID:</dt>
                            <dd class="text-gray-900 dark:text-white font-mono">#{{ $log->model_id }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">Session Info</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">User ID:</dt>
                            <dd class="text-gray-900 dark:text-white">{{ $log->user_id ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">IP Address:</dt>
                            <dd class="text-gray-900 dark:text-white font-mono">{{ $log->ip_address ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Timestamp:</dt>
                            <dd class="text-gray-900 dark:text-white">{{ $log->created_at->format('d M Y H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($log->user_agent)
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">User Agent</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 font-mono bg-gray-50 dark:bg-gray-700 rounded-lg p-3 break-all">{{ $log->user_agent }}</p>
            </div>
            @endif

            @if($log->old_values)
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">Previous Values</h3>
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                    <pre class="text-xs text-red-800 dark:text-red-300 overflow-x-auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif

            @if($log->new_values)
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase mb-2">New Values</h3>
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                    <pre class="text-xs text-green-800 dark:text-green-300 overflow-x-auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
