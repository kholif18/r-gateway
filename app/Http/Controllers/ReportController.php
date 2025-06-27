<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Query utama untuk laporan (pakai pagination)
        $reports = MessageLog::select(
                DB::raw("DATE_FORMAT(sent_at, '%d %b %Y') as date"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('date')
            ->orderByDesc('date')
            ->paginate(20); // <== Tambahkan pagination di sini

        // Query ulang (tanpa pagination) untuk chart
        $chartQuery = MessageLog::select(
                DB::raw("DATE_FORMAT(sent_at, '%d %b %Y') as date"),
                DB::raw('COUNT(*) as total')
            )
            ->where('sent_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();

        $chartLabels = $chartQuery->pluck('date');
        $chartData = $chartQuery->pluck('total');

        return view('report', compact('reports', 'chartLabels', 'chartData'));
    }
}
