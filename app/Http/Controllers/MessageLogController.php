<?php

namespace App\Http\Controllers;

use App\Models\MessageLog;
use Illuminate\Http\Request;
use App\Helpers\WhatsappHelper;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $query = $this->applyFilters(MessageLog::query(), $request);

        $logs = $query->orderByDesc('sent_at')
                      ->paginate($perPage)
                      ->appends($request->all());

        return view('log', compact('logs', 'perPage'));
    }

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

    public function api(Request $request)
    {
        return MessageLog::orderByDesc('sent_at')->limit(100)->get();
    }

    public function export(Request $request): StreamedResponse
    {
        $filename = 'message_logs_' . now()->format('Ymd_His') . '.csv';

        $logs = $this->applyFilters(MessageLog::query(), $request)
                     ->orderByDesc('sent_at')
                     ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Client', 'Session', 'Phone', 'Message', 'Status', 'Response', 'Sent At']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->client_name,
                    $log->session_name,
                    $log->phone,
                    $log->message,
                    $log->status,
                    $log->response,
                    $log->sent_at?->format('Y-m-d H:i:s') ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * 🔁 Reusable filter
     */
    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('client_name') && $request->client_name !== 'all') {
            $query->where('client_name', $request->client_name);
        }

        if ($request->filled('phone') && $request->phone !== 'all') {
            $normalized = WhatsappHelper::normalizePhoneNumber($request->phone); // ✅ Gunakan helper
            $query->where('phone', 'like', '%' . $normalized . '%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('from') || $request->filled('from_date')) {
            $from = $request->from ?? $request->from_date;
            $query->whereDate('sent_at', '>=', $from);
        }

        if ($request->filled('to') || $request->filled('to_date')) {
            $to = $request->to ?? $request->to_date;
            $query->whereDate('sent_at', '<=', $to);
        }

        return $query;
    }
}
