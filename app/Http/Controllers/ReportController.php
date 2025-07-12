<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $userId = Auth::id(); // ⬅️ Ambil user yang sedang login

        $startDate = Carbon::now()->subDays(29)->startOfDay();

        // Ambil data pesan per hari khusus user ini
        $rawData = MessageLog::selectRaw("DATE(sent_at) as date, COUNT(*) as total")
            ->where('user_id', $userId) // ⬅️ Filter berdasarkan user
            ->where('sent_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(sent_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // Siapkan data lengkap 30 hari
        $dates = collect();
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays(29 - $i)->toDateString();
            $dates->put($date, 0);
        }

        foreach ($rawData as $row) {
            $dates->put($row->date, $row->total);
        }

        $chartLabels = $dates->keys()->toArray();
        $chartData   = $dates->values()->toArray();

        // Ambil data untuk tabel (juga dibatasi user)
        $reports = MessageLog::selectRaw("DATE(sent_at) as date, COUNT(*) as total")
            ->where('user_id', $userId) // ⬅️ Filter user
            ->where('sent_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(sent_at)'))
            ->orderByDesc('date')
            ->paginate(20);

        return view('report', compact('chartLabels', 'chartData', 'reports'));
    }
}
