<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MessageLog;
use App\Helpers\WhatsappHelper;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $sentToday = MessageLog::whereDate('sent_at', $today)->count();
        $sentYesterday = MessageLog::whereDate('sent_at', $yesterday)->count();

        $sentTodayGrowth = $sentYesterday > 0
            ? round((($sentToday - $sentYesterday) / $sentYesterday) * 100)
            : ($sentToday > 0 ? 100 : 0);

        $growthDirection = $sentToday > $sentYesterday ? 'up'
                          : ($sentToday < $sentYesterday ? 'down' : 'right');

        $totalMessages = MessageLog::count();
        $successCount = MessageLog::where('status', 'success')->count();
        $successRate = $totalMessages > 0
            ? round(($successCount / $totalMessages) * 100)
            : 0;

        ['status' => $successStatus, 'badge' => $successBadge, 'icon' => $successIcon] =
            $this->determineSuccessIndicator($successRate);

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

    /**
     * Menentukan status indikator tingkat keberhasilan pengiriman pesan
     */
    private function determineSuccessIndicator(int $rate): array
    {
        if ($rate >= 90) {
            return [
                'status' => 'Stable',
                'badge' => 'status-badge-connected',
                'icon' => 'fa-chart-line',
            ];
        } elseif ($rate >= 70) {
            return [
                'status' => 'Warning',
                'badge' => 'status-badge-warning',
                'icon' => 'fa-exclamation-triangle',
            ];
        }

        return [
            'status' => 'Critical',
            'badge' => 'status-badge-disconnected',
            'icon' => 'fa-times-circle',
        ];
    }
}
