@extends('layouts.app')

@section('title', 'Report')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">Message Report</div>
        </div>
        <div class="card-body">            
            <div class="chart-container mb-4">
                <canvas id="chartjs-line"></canvas>
            </div>
            <div class="table-container">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ $report->date }}</td>
                                <td>{{ $report->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="pagination">
                {{-- Previous --}}
                @if ($reports->onFirstPage())
                    <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                @else
                    <a href="{{ $reports->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                @endif

                {{-- Page numbers --}}
                @foreach ($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)
                    @if ($page == $reports->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($reports->hasMorePages())
                    <a href="{{ $reports->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                @else
                    <button class="page-btn" disabled><i class="fas fa-chevron-right"></i></button>
                @endif

                {{-- Info --}}
                <span class="page-info">
                    Page {{ $reports->currentPage() }} of {{ $reports->lastPage() }}
                </span>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            
            const ctx = document.getElementById("chartjs-line").getContext("2d");

            new Chart(ctx, {
                type: "line",
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: "WA Message",
                        data: {!! json_encode($chartData) !!},
                        borderColor: "#4a6bdf",
                        tension: 0.4
                    }]
                },
                    options: {
                    responsive: true,
                    maintainAspectRatio: false // WAJIB agar height dari CSS digunakan
                }
            });
        });
    </script>


@endsection