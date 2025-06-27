<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // ✅ 1. Total pesan hari ini
        $today = Carbon::today();
        $sentToday = MessageLog::whereDate('sent_at', $today)->count();

        // ✅ 2. Total pesan kemarin (untuk growth)
        $yesterday = Carbon::yesterday();
        $sentYesterday = MessageLog::whereDate('sent_at', $yesterday)->count();

        // Hitung growth (jika kemarin = 0, maka default 100%)
        $sentTodayGrowth = $sentYesterday > 0
            ? round((($sentToday - $sentYesterday) / $sentYesterday) * 100)
            : ($sentToday > 0 ? 100 : 0);

        // ✅ 3. Tingkat keberhasilan (% pesan sukses)
        $totalMessages = MessageLog::count();
        $successCount = MessageLog::where('status', 'success')->count();
        $successRate = $totalMessages > 0 ? round(($successCount / $totalMessages) * 100) : 0;

        // ✅ 4. Ambil pesan terakhir
        $lastMessage = MessageLog::latest('sent_at')->first();

        return view('dashboard', compact(
            'sentToday',
            'sentTodayGrowth',
            'successRate',
            'lastMessage'
        ));
    }

    public function status()
    {
        if (!Auth::check() || !Auth::user()->username) {
            return response()->json(['connected' => false, 'error' => 'Unauthorized or username missing'], 403);
        }

        $session = Auth::user()->username;

        try {
            $response = Http::withHeaders([
                'X-API-SECRET' => env('API_SECRET'),
            ])->get(env('WA_BACKEND_URL') . "/session/status?session={$session}");

            Log::debug('Dashboard Session status response:', $response->json());

            return response()->json([
                'connected' => $response->ok() && $response->json('status') === 'CONNECTED'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal cek status gateway: ' . $e->getMessage());
            return response()->json(['connected' => false, 'error' => $e->getMessage()], 500);
        }
    }

}
