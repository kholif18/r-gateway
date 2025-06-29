<?php

namespace App\Http\Controllers;

use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // Ambil data 30 hari terakhir
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $baseQuery = MessageLog::select(
                DB::raw("DATE_FORMAT(sent_at, '%d %b %Y') as date"),
                DB::raw("COUNT(*) as total")
            )
            ->where('sent_at', '>=', $thirtyDaysAgo)
            ->groupBy('date')
            ->orderByDesc('date');

        // 📊 Data untuk chart (tanpa pagination)
        $chartData = $baseQuery->get();
        $chartLabels = $chartData->pluck('date');
        $chartCounts = $chartData->pluck('total');

        // 📄 Data untuk tabel (dengan pagination)
        $reports = (clone $baseQuery)->paginate(20);

        return view('report', compact('reports', 'chartLabels', 'chartCounts', 'chartData'));
    }
}
