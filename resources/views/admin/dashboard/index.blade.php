@extends('admin.admin')

@section('title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-label">Check-In Hari Ini</div>
            <div class="stat-icon slate"><i class="fas fa-user-check"></i></div>
        </div>
        <div class="stat-value">{{ number_format($checkInsToday) }}</div>
        <div class="stat-footer">
            <span class="stat-neutral">Tamu tiba hari ini</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-label">Kamar Terisi</div>
            <div class="stat-icon moss"><i class="fas fa-bed"></i></div>
        </div>
        <div class="stat-value">
            {{ $occupiedRooms }}
            <sub>/ {{ $totalRooms }}</sub>
        </div>
        <div class="stat-footer">
            <span class="stat-neutral">Occupancy kamar saat ini</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-label">Order Restoran Aktif</div>
            <div class="stat-icon clay"><i class="fas fa-utensils"></i></div>
        </div>
        <div class="stat-value">{{ number_format($activeFnbOrders) }}</div>
        <div class="stat-footer">
            <span class="stat-neutral">Pesanan belum selesai</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-icon bark"><i class="fas fa-money-bill-wave"></i></div>
        </div>
        <div class="stat-value sm">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</div>
        <div class="stat-footer">
            <span class="stat-up"><i class="fas fa-arrow-trend-up"></i> Akumulasi Lunas Bulan Ini</span>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-card">
        <div class="card-header">
            <div>
                <div class="card-title">Tren Pendapatan</div>
                <div class="card-sub">7 Hari Terakhir (Semua Layanan)</div>
            </div>
        </div>
        <canvas id="incomeChart" height="90"></canvas>
    </div>

    <div class="chart-card">
        <div class="card-header">
            <div>
                <div class="card-title">Komposisi Kamar</div>
                <div class="card-sub">Status Real-time Saat Ini</div>
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
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-primary btn-sm" style="text-decoration: none;">
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
                    <th>Status Reservasi</th>
                    <th>Total Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBookings as $booking)
                <tr>
                    <td><strong style="color:var(--ink);font-size:12px;">#B-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                    <td style="font-weight:500;color:var(--ink)">{{ $booking->guest->name ?? 'Tamu Tidak Diketahui' }}</td>
                    <td>{{ $booking->room->room_number ?? 'Menunggu/Dihapus' }}</td>
                    <td>
                        @if(in_array($booking->status, ['confirmed', 'checked_in', 'checked_out']))
                            <span class="badge badge-paid" style="background:#c8d8b8; color:#4a7c59; padding:4px 8px; border-radius:4px; font-size: 12px; font-weight: 500;">Confirmed / In</span>
                        @elseif($booking->status == 'pending')
                            <span class="badge badge-pending" style="background:#fdebd0; color:#c07850; padding:4px 8px; border-radius:4px; font-size: 12px; font-weight: 500;">Pending</span>
                        @else
                            <span class="badge badge-cancelled" style="background:#f5c6cb; color:#721c24; padding:4px 8px; border-radius:4px; font-size: 12px; font-weight: 500;">Cancelled</span>
                        @endif
                    </td>
                    <td style="font-weight:500;color:var(--ink)">Rp {{ number_format($booking->total_amount ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: var(--ink3);">Belum ada data pemesanan kamar terbaru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@php
    // Mengekstrak data untuk dilempar ke JavaScript (Chart.js)
    $chartLabels = array_keys($revenueChart->toArray());
    $chartDataValues = array_values($revenueChart->toArray());
    
    // Data untuk Donut Chart (Tersedia, Terisi, Dibersihkan)
    $donutDataValues = [$availableRooms, $occupiedRooms, $cleaningRooms];
@endphp

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const sandStroke = '#ede8e0';
    const mossFull   = '#4a7c59';
    const mossLight  = '#c8d8b8';
    const clay       = '#c07850';
    const slateLight = '#cff4fc'; 
    const ink3       = '#9e9088';

    // Ambil data dinamis dari Controller yang di-inject via PHP
    const chartLabels = {!! json_encode($chartLabels) !!};
    const chartDataValues = {!! json_encode($chartDataValues) !!};
    const donutDataValues = {!! json_encode($donutDataValues) !!};

    const ctxLine = document.getElementById('incomeChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'bar',
        data: {
            labels: chartLabels, 
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: chartDataValues, 
                backgroundColor: mossFull, 
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': Rp ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
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
            labels: ['Tersedia', 'Terisi', 'Dibersihkan'],
            datasets: [{
                data: donutDataValues, 
                backgroundColor: [mossLight, mossFull, slateLight],
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