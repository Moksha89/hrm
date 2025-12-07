@extends('layouts.app')

@section('title', 'Edit ' . $employee->user->name)

@section('content')
                <div class="max-w-4xl mx-auto">
                    @if($errors->any())
                        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded mb-4">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Employee</h1>
                        <a href="{{ route('employees.show', $employee->id) }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                            Cancel
                        </a>
                    </div>

                    <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                                        <input type="text" name="name" value="{{ old('name', $employee->user->name) }}" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mobile Number *</label>
                                        <input type="text" name="mobile" value="{{ old('mobile', $employee->user->mobile) }}" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
                                        <input type="email" name="email" value="{{ old('email', $employee->user->email) }}" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Team</label>
                                        <select name="team_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                                            <option value="">No Team</option>
                                            @foreach($teams as $team)
                                                <option value="{{ $team->id }}" {{ old('team_id', $employee->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Financial Information</h2>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Salary *</label>
                                        <input type="number" name="salary" value="{{ old('salary', $employee->salary) }}" required step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Loan</label>
                                        <input type="number" name="loan" value="{{ old('loan', $employee->loan) }}" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">EMI</label>
                                        <input type="number" name="emi" value="{{ old('emi', $employee->emi) }}" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Aadhar Number</label>
                                        <input type="text" name="aadhar" value="{{ old('aadhar', $employee->aadhar) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">PAN Number</label>
                                        <input type="text" name="pan" value="{{ old('pan', $employee->pan) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date of Birth</label>
                                        <input type="date" name="dob" value="{{ old('dob', $employee->dob) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date of Joining</label>
                                        <input type="date" name="date_of_joining" value="{{ old('date_of_joining', $employee->date_of_joining) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                </div>
                            </div>

                            @php
                                $bankAccountsData = old('bank_accounts', $employee->bankAccounts->map(function($account) {
                                    return [
                                        'account_holder_name' => $account->account_holder_name,
                                        'account_number' => $account->account_number,
                                        'ifsc_code' => $account->ifsc_code,
                                        'bank_name' => $account->bank_name,
                                        'is_default' => (bool)$account->is_default
                                    ];
                                })->toArray());
                            @endphp

                            <script>
                                document.addEventListener('alpine:init', () => {
                                    Alpine.data('bankAccountManager', () => ({
                                        bankAccounts: @json($bankAccountsData),
                                        addBankAccount() {
                                            this.bankAccounts.push({
                                                account_holder_name: "",
                                                account_number: "",
                                                ifsc_code: "",
                                                bank_name: "",
                                                is_default: this.bankAccounts.length === 0
                                            });
                                        },
                                        removeBankAccount(index) {
                                            this.bankAccounts.splice(index, 1);
                                        },
                                        setDefault(index) {
                                            this.bankAccounts.forEach((account, i) => {
                                                account.is_default = i === index;
                                            });
                                        }
                                    }));
                                });
                            </script>

                            <div x-data="bankAccountManager">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Bank Accounts</h2>
                                    <button type="button" @click="addBankAccount()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition">
                                        Add Bank Account
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <template x-for="(account, index) in bankAccounts" :key="index">
                                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                            <div class="flex justify-between items-center mb-3">
                                                <h3 class="font-medium text-gray-900 dark:text-white">Bank Account <span x-text="index + 1"></span></h3>
                                                <button type="button" @click="removeBankAccount(index)" class="text-red-600 hover:text-red-700">Remove</button>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Holder Name</label>
                                                    <input type="text" x-model="account.account_holder_name" :name="'bank_accounts[' + index + '][account_holder_name]'" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Number</label>
                                                    <input type="text" x-model="account.account_number" :name="'bank_accounts[' + index + '][account_number]'" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">IFSC Code</label>
                                                    <input type="text" x-model="account.ifsc_code" :name="'bank_accounts[' + index + '][ifsc_code]'" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bank Name</label>
                                                    <input type="text" x-model="account.bank_name" :name="'bank_accounts[' + index + '][bank_name]'" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <label class="flex items-center">
                                                    <input type="checkbox" :checked="account.is_default" @change="setDefault(index)" :name="'bank_accounts[' + index + '][is_default]'" value="1" class="rounded border-gray-300 dark:border-gray-600 text-teal-600 focus:ring-teal-500">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Set as default account</span>
                                                </label>
                                            </div>
                                        </div>
                                    </template>

                                    <div x-show="bankAccounts.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        No bank accounts added. Click "Add Bank Account" to add one.
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('employees.show', $employee->id) }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                                    Cancel
                                </a>
                                <button type="submit" class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition">
                                    Update Employee
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
@endsection
