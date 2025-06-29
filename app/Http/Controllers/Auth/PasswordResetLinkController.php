<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use Illuminate\View\View;
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
        $input = $request->input('email_or_wa'); // field tunggal

        // Deteksi apakah input email atau no wa
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            // Kirim link reset via email
            $request->merge(['email' => $input]);

            $request->validate([
                'email' => ['required', 'email'],
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                ? back()->with('status', __($status))
                : back()->withInput()
                        ->withErrors(['email_or_wa' => __($status)]);

        } elseif (preg_match('/^(\+62|08)[0-9]{9,15}$/', $input)) {
            // Validasi WA
            $normalizedWa = $this->normalizeWhatsappNumber($input);
            $user = \App\Models\User::where('whatsapp', $normalizedWa)->first();

            if (!$user) {
                return back()->withErrors(['email_or_wa' => 'Nomor WhatsApp tidak terdaftar.']);
            }

            // Generate OTP
            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_expires_at = now()->addMinutes(5);
            $user->save();

            // Kirim OTP via WhatsApp (panggil API R-Gateway)
            $response = Http::withToken(env('WA_GATEWAY_TOKEN'))
                ->post(env('WA_GATEWAY_URL') . '/send-message', [
                    'session' => 'default', // atau sesi sesuai yang aktif
                    'phone' => $normalizedWa,
                    'message' => "Kode OTP untuk reset password Anda adalah: *$otp*.\nBerlaku 5 menit.",
                ]);

            if ($response->successful()) {
                return redirect()->route('password.otp')->with('phone', $normalizedWa);
            } else {
                return back()->withErrors(['email_or_wa' => 'Gagal mengirim OTP ke WhatsApp. Pastikan nomor sudah login WA.']);
            }

        } else {
            return back()->withErrors(['email_or_wa' => 'Masukkan email atau nomor WhatsApp yang valid.']);
        }
    }

    protected function normalizeWhatsappNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number); // hanya angka
        if (Str::startsWith($number, '08')) {
            $number = '62' . substr($number, 1);
        }
        return $number;
    }
}
