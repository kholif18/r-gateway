<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class CustomPasswordResetController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
            'otp' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Contoh: cek OTP di database OTP
        $otpRecord = DB::table('wa_otps')->where([
            ['phone', $request->phone],
            ['otp', $request->otp],
        ])->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau kedaluwarsa']);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'User tidak ditemukan']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        return redirect()->route('login')->with('status', 'Password berhasil direset.');
    }
}
