<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $input = $request->input('email_or_wa');

        // 📍 1. Jika input adalah email
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $request->merge(['email' => $input]);
            $request->validate(['email' => ['required', 'email']]);

            $status = Password::sendResetLink($request->only('email'));

            return $status === Password::RESET_LINK_SENT
                ? back()->with('status', 'Link reset telah dikirim ke email Anda.')
                : back()->withInput()->withErrors(['email_or_wa' => __($status)]);
        }

        // 📍 2. Jika input adalah nomor WhatsApp
        if (preg_match('/^(\+62|08)[0-9]{9,15}$/', $input)) {
            $phone = $this->normalizeWhatsappNumber($input);
            $user = User::where('phone', $phone)->first();

            if (!$user) {
                return back()->withErrors(['email_or_wa' => 'Nomor WhatsApp tidak terdaftar.']);
            }

            $session = 'pelikik' . $phone;

            // ✅ Cek status session WA di backend
            $sessionStatus = Http::withHeaders([
                'X-API-SECRET' => env('API_SECRET'),
            ])->get(env('WA_BACKEND_URL') . "/session/status/$session");

            if (!$sessionStatus->successful() || !$sessionStatus->json('connected')) {
                // ❌ Belum login WhatsApp
                return back()->withErrors([
                    'email_or_wa' => 'Nomor WhatsApp belum login ke sistem. Silakan scan QR terlebih dahulu atau gunakan opsi reset via email.'
                ]);
            }

            // ✅ Session aktif, lanjut kirim OTP
            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_expires_at = now()->addMinutes(3);
            $user->save();

            $response = Http::withHeaders([
                'X-API-SECRET' => env('API_SECRET'),
            ])->post(env('WA_BACKEND_URL') . '/session/send', [
                'session' => $session,
                'phone' => $phone,
                'message' => "Kode OTP Anda adalah *$otp*\nBerlaku 3 menit.",
            ]);

            if (!$response->successful()) {
                return back()->withErrors(['email_or_wa' => 'Gagal mengirim OTP ke WhatsApp.']);
            }

            return redirect()->route('password.otp')->with('phone', $phone);
        }

        return back()->withErrors(['email_or_wa' => 'Masukkan email atau nomor WhatsApp yang valid.']);
    }

    protected function normalizeWhatsappNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number); // hanya angka
        return Str::startsWith($number, '08') ? '62' . substr($number, 1) : $number;
    }
}
