<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\WhatsappHelper;
use App\Services\WhatsAppService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class OtpController extends Controller
{
    protected $wa;

    public function __construct()
    {
        $this->wa = new WhatsAppService();
    }

    public function show(Request $request)
    {
        $phone = session('phone');

        if (!$phone) {
            return redirect()->route('password.request')->withErrors([
                'email_or_wa' => 'Nomor tidak ditemukan. Silakan ulangi proses reset.',
            ]);
        }

        return view('auth.otp', compact('phone'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
            'phone' => 'required',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'Nomor tidak ditemukan.']);
        }

        if (
            !$user->otp ||
            $user->otp !== $request->otp ||
            !$user->otp_expires_at ||
            now()->greaterThan($user->otp_expires_at)
        ) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kadaluarsa.']);
        }

        // ✅ OTP valid: kosongkan OTP dan arahkan ke reset password
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // Simpan ke session agar bisa lanjut ke form reset password
        session(['otp_verified_user' => $user->id]);

        return redirect()->route('password.reset.form');
    }

    public function resendOtp(Request $request)
    {
        $phone = session('phone');

        if (!$phone) {
            return redirect()->route('password.request')->withErrors(['email_or_wa' => 'Nomor tidak ditemukan dalam sesi.']);
        }

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email_or_wa' => 'Pengguna tidak ditemukan.']);
        }

        if ($user->otp_expires_at && now()->diffInSeconds($user->otp_expires_at, false) > -60) {
            return back()->withErrors(['otp' => 'Tunggu sebentar sebelum meminta OTP lagi.']);
        }

        $session = $user->username;

        // Cek koneksi session
        $status = WhatsappHelper::checkGatewayStatus($session);
        if (!$status['connected']) {
            return back()->withErrors(['otp' => 'WhatsApp tidak terhubung.']);
        }

        // Kirim OTP baru
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(3);
        $user->save();

        $sent = $this->wa->sendMessageToSession($session, $phone, "Kode OTP baru Anda adalah *$otp*\nBerlaku 3 menit.");
        if (!$sent) {
            return back()->withErrors(['otp' => 'Gagal mengirim ulang OTP.']);
        }

        return back()->with('status', 'OTP baru telah dikirim ke WhatsApp Anda.');
    }
}
