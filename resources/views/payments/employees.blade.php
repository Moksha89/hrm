@extends('layouts.app')

@section('title', 'Employees - Payments')

@section('content')
                <div class="max-w-7xl mx-auto">
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex space-x-8">
                            <a href="{{ route('payments.salaries') }}" class="px-3 py-2 border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300">
                                Salaries
                            </a>
                            <a href="{{ route('payments.index') }}" class="px-3 py-2 border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300">
                                Loans
                            </a>
                            <a href="{{ route('payments.transactions') }}" class="px-3 py-2 border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300">
                                Transactions
                            </a>
                            <a href="{{ route('payments.employees') }}" class="px-3 py-2 border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-medium">
                                Employees
                            </a>
                        </nav>
                    </div>

                    @if($employees->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($employees as $employee)
                        <a href="{{ route('payments.employee.detail', $employee->id) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400">
                            <div class="p-6">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full text-white font-semibold text-lg md:text-xl">
                                        {{ strtoupper(substr($employee->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">{{ $employee->user->name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $employee->user->mobile }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Team:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            {{ $employee->team ? $employee->team->name : 'No Team' }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                        @if($employee->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                            Active
                                        </span>
                                        @elseif($employee->status === 'inactive')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-300">
                                            Inactive
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">
                                            Resigned
                                        </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Salary:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            ₹{{ number_format($employee->salary ?? 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No employees found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add employees to get started.</p>
                    </div>
                    @endif
                </div>
@endsection
