<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $gatewayStatus = true; // atau hasil pengecekan status gateway
        $sentToday = Report::whereDate('sent_at', today())->count();
        $sentTodayGrowth = 12; // hitung perubahan dibanding kemarin, jika ada
        $successRate = 98; // hitung dari data terkirim vs total
        $lastMessage = Report::latest('sent_at')->first();

        return view('dashboard', compact(
            'gatewayStatus', 'sentToday', 'sentTodayGrowth', 'successRate', 'lastMessage'
        ));
    }

    public function status()
    {
        $sessionId = 'user_23';
        $response = Http::get("http://localhost:3000/status", [
            'sessionId' => $sessionId
        ]);

        return $response->json();
    }
}
