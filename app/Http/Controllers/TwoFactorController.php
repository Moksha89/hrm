<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('auth.two-factor', [
            'enabled' => !empty($user->two_factor_secret),
        ]);
    }

    public function enable(Request $request)
    {
        $user = Auth::user();
        
        // Only allow admins and managers to enable 2FA
        if (!$user->isAdmin() && !$user->isManager()) {
            return back()->with('error', 'Two-factor authentication is only available for administrators and managers.');
        }

        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();

        // Store temporarily in session until verified
        session(['2fa_secret' => $secret]);

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email ?? $user->mobile,
            $secret
        );

        // Generate QR code SVG
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('auth.two-factor-setup', [
            'qrCodeSvg' => $qrCodeSvg,
            'secret' => $secret,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $secret = session('2fa_secret');

        if (!$secret) {
            return back()->with('error', 'No 2FA setup in progress.');
        }

        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($secret, $request->code);

        if ($valid) {
            $user->two_factor_secret = encrypt($secret);
            $user->two_factor_enabled_at = now();
            $user->save();

            session()->forget('2fa_secret');

            return redirect()->route('two-factor.show')->with('success', 'Two-factor authentication enabled successfully!');
        }

        return back()->with('error', 'Invalid verification code. Please try again.');
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!Auth::validate(['mobile' => $user->mobile, 'password' => $request->password])) {
            return back()->with('error', 'Invalid password.');
        }

        $user->two_factor_secret = null;
        $user->two_factor_enabled_at = null;
        $user->save();

        return redirect()->route('two-factor.show')->with('success', 'Two-factor authentication disabled.');
    }

    public function challenge()
    {
        return view('auth.two-factor-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        
        if (!$user->two_factor_secret) {
            return redirect()->route('dashboard');
        }

        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey(decrypt($user->two_factor_secret), $request->code);

        if ($valid) {
            session(['2fa_verified' => true]);
            return redirect()->intended('/dashboard');
        }

        return back()->with('error', 'Invalid verification code.');
    }
}
