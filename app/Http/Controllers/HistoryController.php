<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MessageHistory;
use Illuminate\Support\Facades\Response;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = MessageHistory::query();

        // Filter tanggal
        if ($request->filled('from')) {
            $query->whereDate('sent_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('sent_at', '<=', $request->to);
        }

        // Filter status
        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Pencarian
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $histories = $query->orderByDesc('sent_at')->paginate($perPage)->appends($request->all());

        return view('history', compact('histories', 'perPage'));
    }

    public function export(Request $request)
    {
        $query = MessageHistory::query();

        // Filter seperti di index()
        if ($request->filled('from')) {
            $query->whereDate('sent_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('sent_at', '<=', $request->to);
        }
        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->search . '%')
                ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $histories = $query->orderByDesc('sent_at')->get();

        // Format CSV
        $filename = 'message-history-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($histories) {
            $file = fopen('php://output', 'w');
            // Header
            fputcsv($file, ['Date & Time', 'Phone', 'Message', 'Status']);

            foreach ($histories as $h) {
                fputcsv($file, [
                    $h->sent_at ? $h->sent_at->format('d M Y, H:i') : '-',
                    $h->phone,
                    $h->message,
                    ucfirst($h->status),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function user()
    {
        return view('user');
    }

    
}
