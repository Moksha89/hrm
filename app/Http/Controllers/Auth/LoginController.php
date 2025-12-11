<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        // Rate limiting: 5 attempts per minute per IP + mobile combination
        $throttleKey = 'login:' . $request->ip() . ':' . $request->input('mobile');
        
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'mobile' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('mobile');
        }

        $credentials = $request->only('mobile', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Clear rate limiter on successful login
            RateLimiter::clear($throttleKey);
            
            // Single session login: Invalidate all other sessions for this user
            $this->invalidateOtherSessions($request);
            
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Increment rate limiter on failed attempt
        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'mobile' => 'The provided credentials do not match our records.',
        ])->onlyInput('mobile');
    }

    /**
     * Invalidate all other sessions for the authenticated user.
     * This ensures only one active session per user (single session login).
     */
    protected function invalidateOtherSessions($request): void
    {
        $userId = Auth::id();
        $currentSessionId = $request->session()->getId();
        
        // Delete all other sessions for this user except the current one
        DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        return redirect('/login');
    }
}
