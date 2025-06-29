<?php

namespace App\Http\Controllers;

use App\Models\MessageLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $query = $this->applyFilters(MessageLog::query(), $request);

        $histories = $query->orderByDesc('sent_at')
            ->paginate($perPage)
            ->appends($request->all());

        return view('history', compact('histories', 'perPage'));
    }

    public function export(Request $request)
    {
        $query = $this->applyFilters(MessageLog::query(), $request)
            ->orderByDesc('sent_at');

        $histories = $query->get();

        $filename = 'message-history-' . now()->format('Ymd-His') . '.csv';
        $path = storage_path("app/tmp/$filename");

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        $file = fopen($path, 'w');
        fputcsv($file, ['Date & Time', 'Phone', 'Message', 'Status']);

        foreach ($histories as $h) {
            fputcsv($file, [
                $h->sent_at?->format('d M Y, H:i') ?? '-',
                $h->phone,
                $h->message,
                ucfirst($h->status),
            ]);
        }

        fclose($file);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function user()
    {
        return view('user');
    }

    /**
     * 🔁 Filter reusable (tanggal, status, search)
     */
    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('from')) {
            $query->whereDate('sent_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('sent_at', '<=', $request->to);
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        return $query;
    }
}
