@extends('admin.admin')

@section('title', 'Dashboard')

@section('content')
<div style="padding: 1rem 0;">
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon blue"><i class="fas fa-users"></i></div>
            <div class="stat-label">Total Pengguna</div>
            <div class="stat-value">{{ number_format($widgets['total_users'] ?? 0) }}</div>
            <div class="stat-footer"><span class="stat-neutral">Akun terdaftar</span></div>
        </div>

        <div class="stat-card teal">
            <div class="stat-icon teal"><i class="fas fa-bed"></i></div>
            <div class="stat-label">Kamar Terisi</div>
            <div class="stat-value">{{ $widgets['occupied_rooms'] ?? 0 }}<span style="font-size:1rem;color:var(--text-light)">/{{ $widgets['total_rooms'] ?? 0 }}</span></div>
            <div class="stat-footer"><span class="stat-neutral">Occupancy hari ini</span></div>
        </div>

        <div class="stat-card amber">
            <div class="stat-icon amber"><i class="fas fa-utensils"></i></div>
            <div class="stat-label">Order Restoran</div>
            <div class="stat-value">328</div>
            <div class="stat-footer"><span class="stat-up"><i class="fas fa-arrow-trend-up"></i> +12.5%</span><span class="stat-neutral">bulan ini</span></div>
        </div>

        <div class="stat-card rose">
            <div class="stat-icon rose"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-value" style="font-size:1.35rem">Rp {{ number_format($widgets['total_revenue'] ?? 0, 0, ',', '.') }}</div>
            <div class="stat-footer"><span class="stat-up"><i class="fas fa-arrow-trend-up"></i> Lunas</span></div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <div class="chart-card-title">Tren Pendapatan</div>
                    <div class="chart-card-sub">Booking & Restoran per bulan</div>
                </div>
            </div>
            <canvas id="incomeChart" height="90"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-card-header">
                <div><div class="chart-card-title">Komposisi Pendapatan</div></div>
            </div>
            <canvas id="donutChart" height="170"></canvas>
        </div>
    </div>

    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Booking Terbaru</div>
            <div class="table-card-actions">
                <input class="search-input" placeholder="🔍 Cari...">
                <button class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah</button>
            </div>
        </div>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Tamu</th>
                        <th>Kamar</th>
                        <th>Status Pembayaran</th>
                        <th>Total Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $booking)
                    <tr>
                        <td><strong>#B-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                        
                        <td>{{ $booking->user->name ?? 'Tamu Tidak Diketahui' }}</td>
                        
                        <td>{{ $booking->room->room_number ?? 'Kamar Dihapus' }}</td>
                        
                        <td>
                            @if($booking->payment_status == 'paid')
                                <span class="badge badge-paid">Lunas</span>
                            @elseif($booking->payment_status == 'pending')
                                <span class="badge badge-pending">Pending</span>
                            @else
                                <span class="badge badge-cancelled">Batal</span>
                            @endif
                        </td>
                        
                        <td>Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem;">
                            <div style="color: var(--text-light);">
                                <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                <p>Belum ada data booking terbaru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function initDashboardCharts() {
    const ctxLine = document.getElementById('incomeChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['Nov','Des','Jan','Feb','Mar','Apr'],
            datasets: [{
                label: 'Booking',
                data: [32, 28, 45, 38, 52, 57],
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,.08)',
                tension: .4, fill: true, pointRadius: 4,
                pointBackgroundColor: '#2563eb',
            }]
        },
        options: { responsive: true, plugins: { legend: { display:false } }, scales: { y: { beginAtZero:true } } }
    });

    const ctxDonut = document.getElementById('donutChart').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: ['Booking', 'Restoran'],
            datasets: [{
                data: [68, 32],
                backgroundColor: ['#2563eb', '#d97706'],
                borderWidth: 0, hoverOffset: 6
            }]
        },
        options: { responsive:true, cutout:'72%', plugins: { legend:{display:false} } }
    });
}

// Pastikan DOM sudah diload sebelum merender chart
document.addEventListener("DOMContentLoaded", function() {
    initDashboardCharts();
});
</script>
@endpush