<?php

namespace App\Http\Controllers;

use App\Rules\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', new StrongPassword()],
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->password_changed_at = now();
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password changed successfully!');
    }
}
