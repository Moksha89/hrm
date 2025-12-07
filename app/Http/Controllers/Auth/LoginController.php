<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('mobile', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Single session login: Invalidate all other sessions for this user
            $this->invalidateOtherSessions($request);
            
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

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
