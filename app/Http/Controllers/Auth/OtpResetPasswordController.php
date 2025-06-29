<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class OtpResetPasswordController extends Controller
{
    public function showOtpForm(Request $request)
    {
        if (!$request->session()->has('phone')) {
            return redirect()->route('password.request')->withErrors(['email_or_wa' => 'Nomor tidak ditemukan di sesi.']);
        }

        return view('auth.otp-password');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $phone = $request->session()->get('phone');
        $user = User::where('whatsapp', $phone)->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email_or_wa' => 'Nomor tidak ditemukan.']);
        }

        if (!$user->otp || !$user->otp_expires_at || now()->gt($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa, silakan kirim ulang.']);
        }

        if ($user->otp != $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        // Bersihkan OTP
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // Login otomatis atau redirect ke form ganti password
        session(['reset_password_phone' => $user->whatsapp]);

        return redirect()->route('password.reset.form');
    }

    public function resendOtp(Request $request)
    {
        $phone = $request->query('phone');
        $user = User::where('whatsapp', $phone)->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email_or_wa' => 'Nomor tidak ditemukan.']);
        }

        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(3);
        $user->save();

        $response = Http::withToken(env('WA_GATEWAY_TOKEN'))
            ->post(env('WA_GATEWAY_URL') . '/send-message', [
                'session' => 'default',
                'phone' => $phone,
                'message' => "Kode OTP baru Anda adalah *$otp*.\nBerlaku 3 menit.",
            ]);

        if (!$response->successful()) {
            return back()->withErrors(['otp' => 'Gagal mengirim ulang OTP. Pastikan nomor aktif.']);
        }

        return back()->with('status', 'Kode OTP telah dikirim ulang.');
    }
}
