@extends('layouts.app')

@section('title', 'Setup Two-Factor Authentication')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Setup Two-Factor Authentication</h1>
        
        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <div class="mb-6">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Scan the QR code below with your authenticator app (Google Authenticator, Authy, etc.), then enter the 6-digit code to verify.
            </p>
            
            <div class="flex justify-center mb-4 p-4 bg-white rounded-lg">
                {!! $qrCodeSvg !!}
            </div>
            
            <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Manual entry key:</p>
                <code class="text-sm font-mono text-gray-900 dark:text-white">{{ $secret }}</code>
            </div>
        </div>

        <form method="POST" action="{{ route('two-factor.verify') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Enter 6-digit code from your app</label>
                <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" required autofocus class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white text-center tracking-widest font-mono text-lg">
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('two-factor.show') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 text-sm rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition-colors">
                    Verify and Enable
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
