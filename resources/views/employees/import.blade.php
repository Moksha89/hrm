@extends('layouts.app')

@section('title', 'Import Employees')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('employees.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Employees
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Import Employees</h1>
        
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
            <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-300 mb-2">File Format Requirements</h3>
            <p class="text-xs text-amber-700 dark:text-amber-400 mb-2">
                Upload an Excel (.xlsx) or CSV file with the following columns:
            </p>
            <ul class="text-xs text-amber-700 dark:text-amber-400 list-disc list-inside space-y-1">
                <li><strong>Name</strong> (required)</li>
                <li><strong>Mobile</strong> (required, unique)</li>
                <li><strong>Email</strong> (optional)</li>
                <li><strong>Salary</strong> (optional, default: 0)</li>
                <li><strong>Team</strong> (optional, team name)</li>
                <li><strong>Bank Name</strong> (optional)</li>
                <li><strong>Account Number</strong> (optional)</li>
                <li><strong>IFSC Code</strong> (optional)</li>
            </ul>
            <a href="{{ route('import.template') }}" class="inline-flex items-center mt-3 text-xs text-amber-600 dark:text-amber-400 hover:underline">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download Template
            </a>
        </div>

        <form method="POST" action="{{ route('import.employees') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Upload File</label>
                <input type="file" name="file" accept=".xlsx,.csv" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-amber-900/30 dark:file:text-amber-400">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Max file size: 10MB</p>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Default Team (Optional)</label>
                <select name="default_team_id" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                    <option value="">No default team</option>
                    @foreach($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Used when team column is empty in the file</p>
            </div>

            <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    <strong>Note:</strong> All imported employees will be assigned the default password <code class="bg-gray-200 dark:bg-gray-600 px-1 rounded">Password@123</code>. They should change it on first login.
                </p>
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                Import Employees
            </button>
        </form>
    </div>
</div>
@endsection
