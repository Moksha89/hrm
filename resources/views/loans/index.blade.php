@extends('layouts.app')

@section('title', 'Loans')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Loans</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">View and manage employee loans by team</p>
    </div>

    @if($teams->isEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No teams available</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a team first.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($teams as $team)
        @php
            $activeLoansCount = $team->employees->sum(fn($e) => $e->loans->where('status', 'active')->where('remaining_balance', '>', 0)->count());
            $totalLoanAmount = $team->employees->sum(fn($e) => $e->loans->where('status', 'active')->where('remaining_balance', '>', 0)->sum('remaining_balance'));
            $monthlyEmi = $team->employees->sum(fn($e) => $e->getTotalMonthlyEmi());
        @endphp
        <a href="{{ route('loans.team', $team->id) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow duration-200 p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $team->name }}</h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Employees</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $team->employees_count }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Active Loans</span>
                    <span class="text-sm font-medium text-orange-600 dark:text-orange-400">{{ $activeLoansCount }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Amount</span>
                    <span class="text-sm font-medium text-red-600 dark:text-red-400">₹{{ number_format($totalLoanAmount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Monthly EMI</span>
                    <span class="text-sm font-medium text-purple-600 dark:text-purple-400">₹{{ number_format($monthlyEmi, 2) }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection
