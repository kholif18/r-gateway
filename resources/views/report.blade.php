@extends('layouts.app')

@section('title', 'Report')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">Message Report</div>
        </div>
        <div class="card-body">            
            <div class="chart-container mb-4">
                <canvas id="waChart"></canvas>
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
        </div>
        <div class="pagination overflow-x-auto whitespace-nowrap mb-4">
            <x-pagination :paginator="$reports" />
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById("waChart").getContext("2d");

            new Chart(ctx, {
                type: "line",
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: "WA Message",
                        data: {!! json_encode($chartData) !!},
                        borderColor: "#4a6bdf",
                        backgroundColor: "rgba(74, 107, 223, 0.1)",
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: "#fff",
                        pointBorderColor: "#4a6bdf",
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: "rgba(0,0,0,0.05)"
                            }
                        }
                    }
                }
            });
        });
    </script>


@endsection