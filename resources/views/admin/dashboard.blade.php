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
            <span class="stat-neutral">Akun Staff & Admin</span>
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
            <span class="stat-neutral">Occupancy kamar saat ini</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-label">Order Restoran</div>
            <div class="stat-icon clay"><i class="fas fa-utensils"></i></div>
        </div>
        <div class="stat-value">{{ number_format($widgets['total_resto_orders']) }}</div>
        <div class="stat-footer">
            <span class="stat-neutral">Total order tercatat</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-icon bark"><i class="fas fa-money-bill-wave"></i></div>
        </div>
        <div class="stat-value sm">Rp {{ number_format($widgets['total_revenue'] ?? 0, 0, ',', '.') }}</div>
        <div class="stat-footer">
            <span class="stat-up"><i class="fas fa-arrow-trend-up"></i> Akumulasi Transaksi Lunas</span>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-card">
        <div class="card-header">
            <div>
                <div class="card-title">Tren Pendapatan</div>
                <div class="card-sub">Booking 6 Bulan Terakhir</div>
            </div>
        </div>
        <canvas id="incomeChart" height="90"></canvas>
    </div>

    <div class="chart-card">
        <div class="card-header">
            <div>
                <div class="card-title">Komposisi Pendapatan</div>
                <div class="card-sub">Booking vs Restoran</div>
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
                    <td style="font-weight:500;color:var(--ink)">{{ $booking->guest->name ?? 'Tamu Tidak Diketahui' }}</td>
                    <td>{{ $booking->room->room_number ?? 'Kamar Dihapus' }}</td>
                    <td>
                        {{-- Mengikuti status enum database yang kita pakai sekarang --}}
                        @if(in_array($booking->status, ['confirmed', 'checked_in', 'checked_out']))
                            <span class="badge badge-paid" style="background:#c8d8b8; color:#4a7c59; padding:4px 8px; border-radius:4px;">Confirmed/Paid</span>
                        @elseif($booking->status == 'pending')
                            <span class="badge badge-pending" style="background:#fdebd0; color:#c07850; padding:4px 8px; border-radius:4px;">Waiting Payment</span>
                        @else
                            <span class="badge badge-cancelled" style="background:#f5c6cb; color:#721c24; padding:4px 8px; border-radius:4px;">Batal/Gagal</span>
                        @endif
                    </td>
                    <td style="font-weight:500;color:var(--ink)">Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                {{-- Empty state tetap sama --}}
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

    // Ambil data dinamis dari Controller yang di-inject via PHP
    const chartLabels = {!! json_encode($chartLabels) !!};
    const chartDataValues = {!! json_encode($chartData) !!};
    const donutDataValues = {!! json_encode($donutData) !!};

    const ctxLine = document.getElementById('incomeChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'bar',
        data: {
            labels: chartLabels, // <-- Data dinamis
            datasets: [{
                label: 'Jumlah Booking',
                data: chartDataValues, // <-- Data dinamis
                backgroundColor: ['#c8d8b8','#c8d8b8','#b5cba3','#c8d8b8','#8ab87a', mossFull],
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: sandStroke }, ticks: { color: ink3, font: { size: 10 }, precision: 0 } },
                x: { grid: { display: false }, ticks: { color: ink3, font: { size: 10 } } }
            }
        }
    });

    const ctxDonut = document.getElementById('donutChart').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: ['Booking Kamar', 'Order Restoran'],
            datasets: [{
                data: donutDataValues, // <-- Data dinamis
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