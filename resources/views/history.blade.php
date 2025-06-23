@extends('components.app')

@section('title', 'History')

@section('content')
    <!-- History Page -->
    <div class="page-content active" id="history">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Message History</div>
                <div>
                    <button class="btn">
                        <i class="fas fa-download"></i> Export Data
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3 mt-3">
                    <div class="col-2">
                        <label for="date-from">From Date</label>
                        <input type="date" id="date-from" class="filter-control" value="2025-06-01">
                    </div>
                    
                    <div class="col-2">
                        <label for="date-to">To Date</label>
                        <input type="date" id="date-to" class="filter-control" value="2025-06-23">
                    </div>
                    
                    <div class="col-2">
                        <label for="status-filter">Status</label>
                        <select id="status-filter" class="filter-control">
                            <option value="all">All Status</option>
                            <option value="delivered">Delivered</option>
                            <option value="read">Read</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    
                    <div class="col-3">
                        <label for="search">Search</label>
                        <input type="text" id="search" class="filter-control" placeholder="Search recipient or message...">
                    </div>
                    
                    <div class="col-3 mt-4">
                        <button class="btn">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-outline">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Recipient</th>
                                <th>Message</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>23 Jun 2025, 14:30</td>
                                <td>John Doe (+628123456789)</td>
                                <td class="message-preview">Halo John, terima kasih sudah menghubungi kami. Berikut detail pesanan Anda...</td>
                                <td><span class="status-badge status-read">Read</span></td>
                            </tr>
                            <tr>
                                <td>23 Jun 2025, 12:15</td>
                                <td>Jane Smith (+628987654321)</td>
                                <td class="message-preview">Promo spesial bulan ini! Diskon 30% untuk semua produk...</td>
                                <td><span class="status-badge status-delivered">Delivered</span></td>
                            </tr>
                            <tr>
                                <td>22 Jun 2025, 16:45</td>
                                <td>Marketing Team (Group)</td>
                                <td class="message-preview">Meeting besok pukul 10:00 WIB di kantor. Mohon konfirmasi kehadiran...</td>
                                <td><span class="status-badge status-read">Read</span></td>
                            </tr>
                            <tr>
                                <td>22 Jun 2025, 09:20</td>
                                <td>Robert Johnson (+628112233445)</td>
                                <td class="message-preview">Invoice pembayaran Anda terlampir. Mohon segera melakukan pembayaran...</td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                            </tr>
                            <tr>
                                <td>21 Jun 2025, 17:30</td>
                                <td>Sarah Williams (+628556677889)</td>
                                <td class="message-preview">Terima kasih telah melakukan pembelian! Berikut adalah kode tracking pengiriman...</td>
                                <td><span class="status-badge status-failed">Failed</span></td>
                            </tr>
                            <tr>
                                <td>20 Jun 2025, 11:05</td>
                                <td>Michael Brown (+628990011223)</td>
                                <td class="message-preview">Pengingat: Janji temu Anda besok pukul 14:00 dengan Dr. Andi...</td>
                                <td><span class="status-badge status-delivered">Delivered</span></td>
                                </td>
                            </tr>
                            <tr>
                                <td>19 Jun 2025, 15:40</td>
                                <td>Emily Davis (+628334455667)</td>
                                <td class="message-preview">Selamat ulang tahun! Kami memberikan voucher diskon 20% sebagai hadiah...</td>
                                <td><span class="status-badge status-read">Read</span></td>
                            </tr>
                            <tr>
                                <td>18 Jun 2025, 10:15</td>
                                <td>Customer Support (Group)</td>
                                <td class="message-preview">Update terbaru mengenai maintenance sistem pada hari Sabtu...</td>
                                <td><span class="status-badge status-delivered">Delivered</span></td>
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
    </div>
@endsection