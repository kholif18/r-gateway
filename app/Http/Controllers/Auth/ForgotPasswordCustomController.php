<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class ForgotPasswordCustomController extends Controller
{
    public function sendReset(Request $request)
    {
        $request->validate([
            'email_or_wa' => 'required|string',
        ]);

        $input = $request->input('email_or_wa');

        $user = User::where('email', $input)
                    ->orWhere('phone', $input)
                    ->first();

        if (!$user) {
            return back()->withErrors(['email_or_wa' => 'Akun tidak ditemukan.']);
        }

        // Jika WA login, kirim OTP via WA (sederhana, tanpa implementasi OTP penuh)
        if ($user->wa_connected) {
            // Kirim OTP WA di sini
            Log::info("OTP dikirim ke WA: {$user->phone}");

            return back()->with('status', 'OTP dikirim ke WhatsApp Anda.');
        }

        // Jika tidak, kirim link reset password via email
        if ($user->email) {
            $status = Password::sendResetLink(['email' => $user->email]);

            return $status === Password::RESET_LINK_SENT
                ? back()->with('status', __($status))
                : back()->withErrors(['email_or_wa' => __($status)]);
        }

        return back()->withErrors(['email_or_wa' => 'Tidak dapat mengirim reset password.']);
    }
}
