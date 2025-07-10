<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\WhatsappHelper;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    protected $wa;

    public function __construct()
    {
        $this->wa = new WhatsAppService();
    }

    /**
     * Tampilkan halaman form reset password.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses permintaan reset password (via email atau WhatsApp).
     */
    public function store(Request $request): RedirectResponse
    {
        $input = $request->input('email_or_wa');

        // ✅ Jika Email
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $request->merge(['email' => $input]);
            $request->validate(['email' => ['required', 'email']]);

            $status = Password::sendResetLink($request->only('email'));

            return $status === Password::RESET_LINK_SENT
                ? back()->with('status', 'Link reset telah dikirim ke email Anda.')
                : back()->withInput()->withErrors(['email_or_wa' => __($status)]);
        }

        // ✅ Coba normalisasi & cari user via WhatsApp
        $phone = WhatsappHelper::normalizePhoneNumber($input);
        $user = User::where('phone', $phone)->first();

        if ($user) {
            $session = $user->username;

            // 🔍 Cek status via helper
            $statusData = WhatsappHelper::checkGatewayStatus($session);

            Log::debug("OTP Reset: Status session", [
                'session' => $session,
                'statusData' => $statusData,
            ]);

            if (!$statusData['connected']) {
                return back()->withErrors([
                    'email_or_wa' => 'Nomor WhatsApp belum login ke sistem. Silakan scan QR terlebih dahulu atau gunakan opsi reset via email.'
                ]);
            }

            // ✅ Kirim OTP
            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_expires_at = now()->addMinutes(3);
            $user->save();

            $sendStatus = $this->wa->sendMessageToSession($session, $phone, 
                "Kode OTP Anda adalah *$otp*.\n\n" .
                "🕒 Kode ini hanya berlaku selama 3 menit.\n\n" .
                "⚠️ *Jangan berikan kode ini kepada siapa pun*, termasuk pihak yang mengaku dari R Gateway.\n\n" .
                "Jika Anda tidak meminta kode ini, abaikan pesan ini."
            );


            if (!$sendStatus) {
                return back()->withErrors(['email_or_wa' => 'Gagal mengirim OTP ke WhatsApp.']);
            }

            session(['otp_phone' => $phone]);
            return redirect()->route('password.otp')->with('status', 'Kode OTP telah dikirim ke WhatsApp Anda.');
        }

        // ❌ Gagal validasi semua
        return back()->withErrors(['email_or_wa' => 'Masukkan email atau nomor WhatsApp yang valid.']);
    }
}
