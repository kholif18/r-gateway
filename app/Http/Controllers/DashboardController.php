<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MessageLog;
use Illuminate\Support\Facades\Auth;
use App\Helpers\WhatsappHelper\WhatsappHelper;

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

        // ✅ 3. Hitung growth (%)
        if ($sentYesterday > 0) {
            $sentTodayGrowth = round((($sentToday - $sentYesterday) / $sentYesterday) * 100);
        } else {
            $sentTodayGrowth = $sentToday > 0 ? 100 : 0;
        }

        // ✅ 4. Tentukan arah panah growth
        $growthDirection = $sentToday > $sentYesterday ? 'up'
                        : ($sentToday < $sentYesterday ? 'down' : 'right'); // right = stagnan

        // ✅ 5. Tingkat keberhasilan (% pesan sukses)
        $totalMessages = MessageLog::count();
        $successCount = MessageLog::where('status', 'success')->count();
        $successRate = $totalMessages > 0 ? round(($successCount / $totalMessages) * 100) : 0;

        if ($successRate >= 90) {
            $successStatus = 'Stable';
            $successBadge = 'status-badge-connected';       // hijau
            $successIcon = 'fa-chart-line';
        } elseif ($successRate >= 70) {
            $successStatus = 'Warning';
            $successBadge = 'status-badge-warning';         // kuning
            $successIcon = 'fa-exclamation-triangle';
        } else {
            $successStatus = 'Critical';
            $successBadge = 'status-badge-disconnected';    // merah
            $successIcon = 'fa-times-circle';
        }

        // ✅ 6. Ambil pesan terakhir
        $lastMessage = MessageLog::latest('sent_at')->first();

        return view('dashboard', compact(
            'sentToday',
            'sentTodayGrowth',
            'growthDirection',
            'successRate',
            'lastMessage',
            'successStatus',
            'successBadge',
            'successIcon'
        ));
    }

    public function status()
    {
        $session = Auth::user()->username;
        $status = WhatsappHelper::checkGatewayStatus($session);

        return response()->json($status);

    }

}
