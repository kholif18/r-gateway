<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MessageLog;
use App\Helpers\WhatsappHelper;
use Illuminate\Support\Facades\Auth;
use App\Services\UpdateChecker;

class DashboardController extends Controller
{
    
    public function index(UpdateChecker $checker)
    {
        $userId = Auth::id();
        $clientNames = \App\Models\ApiClient::where('user_id', $userId)
            ->pluck('client_name')
            ->toArray();

        $update = $checker->check();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $sentToday = MessageLog::where('user_id', $userId)
            ->whereDate('sent_at', $today)
            ->count();

        $sentYesterday = MessageLog::where('user_id', $userId)
            ->whereDate('sent_at', $yesterday)
            ->count();

        $sentTodayGrowth = $sentYesterday > 0
            ? round((($sentToday - $sentYesterday) / $sentYesterday) * 100)
            : ($sentToday > 0 ? 100 : 0);

        $growthDirection = $sentToday > $sentYesterday ? 'up'
                        : ($sentToday < $sentYesterday ? 'down' : 'right');

        $totalMessages = MessageLog::where('user_id', $userId)->count();
        $successCount = MessageLog::where('user_id', $userId)
            ->where('status', 'success')
            ->count();

        $successRate = $totalMessages > 0
            ? round(($successCount / $totalMessages) * 100)
            : 0;

        [
            'status' => $successStatus,
            'badge' => $successBadge,
            'icon' => $successIcon,
            'bgGradient' => $successBackground,
        ] = $this->determineSuccessIndicator($successRate);

        $lastMessage = MessageLog::where('user_id', $userId)
            ->whereNotNull('sent_at')
            ->latest('sent_at')
            ->first();


        return view('dashboard', compact(
            'sentToday',
            'sentTodayGrowth',
            'growthDirection',
            'update',
            'successRate',
            'lastMessage',
            'successStatus',
            'successBadge',
            'successIcon',
            'successBackground'
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
                'bgGradient' => 'linear-gradient(135deg, #28a745, #054a43)',
            ];
        } elseif ($rate >= 70) {
            return [
                'status' => 'Warning',
                'badge' => 'status-badge-warning',
                'icon' => 'fa-exclamation-triangle',
                'bgGradient' => 'linear-gradient(135deg, #ffc107, #856404)',
            ];
        }

        return [
            'status' => 'Critical',
            'badge' => 'status-badge-disconnected',
            'icon' => 'fa-times-circle',
            'bgGradient' => 'linear-gradient(135deg, #dc3545, #721c24)',
        ];
    }
}
