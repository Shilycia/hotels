@extends('admin.admin')

@section('title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-label">Total Pengguna</div>
            <div class="stat-icon slate"><i class="fas fa-user"></i></div>
        </div>
        <div class="stat-value">{{ number_format($widgets['total_users'] ?? 0) }}</div>
        <div class="stat-footer">
            <span class="stat-neutral">Akun terdaftar</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-label">Kamar Terisi</div>
            <div class="stat-icon moss"><i class="fas fa-bed"></i></div>
        </div>
        <div class="stat-value">
            {{ $widgets['occupied_rooms'] ?? 0 }}
            <sub>/ {{ $widgets['total_rooms'] ?? 0 }}</sub>
        </div>
        <div class="stat-footer">
            <span class="stat-neutral">Occupancy hari ini</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-label">Order Restoran</div>
            <div class="stat-icon clay"><i class="fas fa-utensils"></i></div>
        </div>
        <div class="stat-value">328</div>
        <div class="stat-footer">
            <span class="stat-up"><i class="fas fa-arrow-trend-up"></i> +12.5%</span>
            <span class="stat-neutral">bulan ini</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-icon bark"><i class="fas fa-money-bill-wave"></i></div>
        </div>
        <div class="stat-value sm">Rp {{ number_format($widgets['total_revenue'] ?? 0, 0, ',', '.') }}</div>
        <div class="stat-footer">
            <span class="stat-up"><i class="fas fa-arrow-trend-up"></i> Lunas</span>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-card">
        <div class="card-header">
            <div>
                <div class="card-title">Tren Pendapatan</div>
                <div class="card-sub">Booking per bulan</div>
            </div>
        </div>
        <canvas id="incomeChart" height="90"></canvas>
    </div>

    <div class="chart-card">
        <div class="card-header">
            <div>
                <div class="card-title">Komposisi Pendapatan</div>
                <div class="card-sub">Sumber pendapatan</div>
            </div>
        </div>
        <canvas id="donutChart" height="160"></canvas>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Booking Terbaru</div>
        <div class="table-card-actions">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input class="search-input" placeholder="Cari tamu atau kamar...">
            </div>
            <a href="{{ route('admin.bookings') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-list"></i> Lihat Semua
            </a>
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
                    <td><strong style="color:var(--ink);font-size:12px;">#B-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                    <td style="font-weight:500;color:var(--ink)">{{ $booking->user->name ?? 'Tamu Tidak Diketahui' }}</td>
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
                    <td style="font-weight:500;color:var(--ink)">Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>Belum ada data booking terbaru.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const sandStroke = '#ede8e0';
    const mossFull   = '#4a7c59';
    const mossLight  = '#c8d8b8';
    const clay       = '#c07850';
    const ink3       = '#9e9088';

    const ctxLine = document.getElementById('incomeChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'bar',
        data: {
            labels: ['Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr'],
            datasets: [{
                label: 'Booking',
                data: [32, 28, 45, 38, 52, 57],
                backgroundColor: ['#c8d8b8','#c8d8b8','#b5cba3','#c8d8b8','#8ab87a', mossFull],
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: sandStroke }, ticks: { color: ink3, font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { color: ink3, font: { size: 10 } } }
            }
        }
    });

    const ctxDonut = document.getElementById('donutChart').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: ['Booking', 'Restoran'],
            datasets: [{
                data: [68, 32],
                backgroundColor: [mossFull, clay],
                borderWidth: 0,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            cutout: '72%',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { color: ink3, font: { size: 11 }, boxWidth: 10, padding: 14 }
                }
            }
        }
    });
});
</script>
@endpush