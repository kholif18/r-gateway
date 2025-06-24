<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Query utama untuk laporan (pakai pagination)
        $reports = Report::select(
                DB::raw("DATE_FORMAT(sent_at, '%d %b %Y') as date"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('date')
            ->orderByDesc('date')
            ->paginate(20); // <== Tambahkan pagination di sini

        // Query ulang (tanpa pagination) untuk chart
        $chartQuery = Report::select(
                DB::raw("DATE_FORMAT(sent_at, '%d %b %Y') as date"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        $chartLabels = $chartQuery->pluck('date')->reverse();
        $chartData = $chartQuery->pluck('total')->reverse();

        return view('report', compact('reports', 'chartLabels', 'chartData'));
    }
}
