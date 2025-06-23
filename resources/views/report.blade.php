@extends('components.app')

@section('title', 'Report')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">Message Report</div>
            <div>
                <button class="btn">
                    <i class="fas fa-download"></i> Export Data
                </button>
            </div>
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
                        <tr>
                            <td>23 Jun 2025, 14:30</td>
                            <td>8</td>
                        </tr>
                        <tr>
                            <td>23 Jun 2025, 12:15</td>
                            <td>5</td>
                        </tr>
                        <tr>
                            <td>22 Jun 2025, 16:45</td>
                            <td>4</td>
                        </tr>
                        <tr>
                            <td>22 Jun 2025, 09:20</td>
                            <td>9</td>
                        </tr>
                        <tr>
                            <td>21 Jun 2025, 17:30</td>
                            <td>2</td>
                        </tr>
                        <tr>
                            <td>20 Jun 2025, 11:05</td>
                            <td>3</td>
                        </tr>
                        <tr>
                            <td>19 Jun 2025, 15:40</td>
                            <td>4</td>
                        </tr>
                        <tr>
                            <td>18 Jun 2025, 10:15</td>
                            <td>8</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination">
                <button class="page-btn">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn">4</button>
                <span class="page-info">Page 1 of 4</span>
                <button class="page-btn">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            
            const ctx = document.getElementById("chartjs-line").getContext("2d");

            new Chart(ctx, {
                type: "line",
                data: {
                    labels: ["Jan", "Feb", "Mar", "Apr", "Mei"],
                    datasets: [{
                        label: "Contoh Data",
                        data: [10, 20, 15, 11, 14],
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