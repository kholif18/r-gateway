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
        // Ambil 30 hari terakhir
        $startDate = Carbon::now()->subDays(29)->startOfDay();

        // Ambil data pesan per hari
        $rawData = MessageLog::selectRaw("DATE(sent_at) as date, COUNT(*) as total")
            ->where('sent_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(sent_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // Siapkan array lengkap untuk 30 hari (agar hari kosong tetap muncul)
        $dates = collect();
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays(29 - $i)->toDateString();
            $dates->put($date, 0);
        }

        // Isi data dari hasil query
        foreach ($rawData as $row) {
            $dates->put($row->date, $row->total);
        }

        // Untuk chart
        $chartLabels = $dates->keys()->toArray();
        $chartData = $dates->values()->toArray();

        // Untuk tabel laporan (dari hasil asli)
        $reports = MessageLog::selectRaw("DATE(sent_at) as date, COUNT(*) as total")
            ->where('sent_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(sent_at)'))
            ->orderByDesc('date')
            ->paginate(20);

        return view('report', compact('chartLabels', 'chartData', 'reports'));
    }
}
