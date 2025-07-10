<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\WhatsappHelper;
use App\Services\WhatsAppService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
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
        $phone = session('otp_phone');

        if (!$phone) {
            return redirect()->route('password.request')->withErrors([
                'email_or_wa' => 'Nomor tidak ditemukan. Silakan ulangi proses reset.',
            ]);
        }

        $user = User::where('phone', $phone)->first();

        if (!$user || !$user->otp_expires_at) {
            return redirect()->route('password.request')->withErrors([
                'otp' => 'Tidak dapat menampilkan halaman OTP. Silakan ulangi proses reset.',
            ]);
        }

        return view('auth.otp', [
            'phone' => $phone,
            'expiresAt' => $user->otp_expires_at->timestamp, // <- untuk countdown JS
        ]);
    }


    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        // Ambil nomor dari session, bukan dari request
        $phone = session('otp_phone');

        if (!$phone) {
            return redirect()->route('password.request')->withErrors(['otp' => 'Sesi tidak ditemukan. Silakan mulai ulang.']);
        }

        $phone = session('otp_phone');

        if (!$phone) {
            return redirect()->route('password.request')->withErrors(['otp' => 'Sesi tidak ditemukan. Silakan mulai ulang.']);
        }

        $user = User::where('phone', $phone)->first();


        if (!$user) {
            return redirect()->route('password.request')->withErrors(['otp' => 'Pengguna tidak ditemukan.']);
        }

        // Hitung percobaan
        $attemptsKey = 'otp_attempts_' . $user->id;
        $attempts = session($attemptsKey, 0);

        // OTP kadaluarsa
        if (!$user->otp || !$user->otp_expires_at || now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP kadaluarsa. Silakan kirim ulang OTP.']);
        }

        // OTP salah
        if ($user->otp !== $request->otp) {
            $attempts++;
            session([$attemptsKey => $attempts]);

            if ($attempts >= 3) {
                session()->forget($attemptsKey);
                session()->forget('otp_phone');
                return redirect()->route('password.request')->withErrors(['otp' => 'Terlalu banyak percobaan. Silakan mulai ulang.']);
            }

            return back()->withErrors(['otp' => "Kode OTP salah. Percobaan ke $attempts dari 3."]);
        }

        // OTP benar
        session()->forget($attemptsKey);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        session(['otp_verified_user' => $user->id]);
        session()->forget('otp_phone');

        return redirect()->route('password.wa.form');
    }



    public function resendOtp(Request $request)
    {
        $phone = session('otp_phone');

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

    public function resetForm()
    {
        if (!session('otp_verified_user')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password-wa');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::find(session('otp_verified_user'));

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['phone' => 'Data tidak ditemukan']);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        session()->forget(['otp_verified_user', 'otp_phone']);

        return redirect()->route('login')->with('status', 'Password berhasil diperbarui. Silakan login.');
    }

}
