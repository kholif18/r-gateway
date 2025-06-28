<?php

namespace App\Http\Controllers;

use App\Models\MessageLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $query = MessageLog::query();
        
        if ($request->client_name && $request->client_name != 'all') {
            $query->where('client_name', $request->client_name);
        }
        
        if ($request->phone && $request->phone != 'all') {
            $query->where('phone', $request->phone);
        }
        
        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('sent_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('sent_at', '<=', $request->to);
        }
        
        $logs = $query->orderByDesc('sent_at')->paginate($perPage)->appends($request->all());

        return view('log', compact('logs', 'perPage'));
    }

    /**
     * Menyimpan log ke database (jika kamu butuh endpoint manual).
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_name'   => 'required|string',
            'session_name'  => 'required|string',
            'phone'         => 'required|string',
            'message'       => 'required|string',
            'status'        => 'required|in:success,failed,pending',
            'response'      => 'nullable|string',
            'sent_at'       => 'nullable|date',
        ]);

        $log = MessageLog::create($request->all());

        return response()->json(['status' => 'success', 'data' => $log], 201);
    }

    /**
     * API untuk ambil log (opsional).
     */
    public function api(Request $request)
    {
        return MessageLog::orderByDesc('sent_at')->limit(100)->get();
    }

    public function export(Request $request): StreamedResponse
    {
        $filename = 'message_logs_' . now()->format('Ymd_His') . '.csv';

        $query = MessageLog::query();

        if ($request->filled('client_name')) {
            $query->where('client_name', $request->client_name);
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('sent_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('sent_at', '<=', $request->to_date);
        }

        $logs = $query->orderByDesc('sent_at')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            // Header kolom
            fputcsv($file, ['Client', 'Session', 'Phone', 'Message', 'Status', 'Response', 'Sent At']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->client_name,
                    $log->session_name,
                    $log->phone,
                    $log->message,
                    $log->status,
                    $log->response,
                    $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
