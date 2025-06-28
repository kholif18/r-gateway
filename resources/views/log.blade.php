@extends('layouts.app')

@section('title', 'Message Log')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">Message Log</div>
            <div>
                <a href="{{ route('history.export', request()->query()) }}" class="btn">
                    <i class="fas fa-download"></i> Export Data
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" name="client_name" class="form-control" placeholder="Client Name" value="{{ request('client_name') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="phone" class="form-control" placeholder="Phone Number" value="{{ request('phone') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="filter-control">
                        <option value="all">All Status</option>
                        @foreach(['success', 'pending', 'failed', 'error', 'rate_limited'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-12 text-end">
                    <button class="btn btn-outline-primary"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('logs.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i> Reset</a>
                </div>
            </form>
            <table class="log-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Response</th>
                        <th>Sent At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->client_name }}</td>
                            <td>{{ $log->phone }}</td>
                            <td>{{ Str::limit($log->message, 50) }}</td>
                            <td>
                                <span class="badge 
                                    @if($log->status === 'success') bg-success 
                                    @elseif($log->status === 'failed') bg-danger 
                                    @else bg-warning 
                                    @endif">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td>
                                <code style="font-size: 0.85em;">{{ Str::limit($log->response, 50) }}</code>
                            </td>
                            <td>{{ $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <form method="GET" class="d-flex align-items-center" style="gap: 0.5rem;">
                    <label>Show</label>
                    <select name="per_page" onchange="this.form.submit()" class="filter-control">
                        @foreach([20, 50, 100] as $size)
                            <option value="{{ $size }}" {{ request('per_page', 20) == $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span>entries</span>

                    {{-- Kirim filter yang sudah diisi --}}
                    <input type="hidden" name="from" value="{{ request('from') }}">
                    <input type="hidden" name="to" value="{{ request('to') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                </form>

                {{-- Pagination --}}
                <div class="pagination">
                    {{-- Previous --}}
                    @if ($logs->onFirstPage())
                        <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif

                    {{-- Page numbers --}}
                    @foreach ($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                        @if ($page == $logs->currentPage())
                            <span class="page-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <button class="page-btn" disabled><i class="fas fa-chevron-right"></i></button>
                    @endif

                    {{-- Info --}}
                    <span class="page-info">
                        Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection