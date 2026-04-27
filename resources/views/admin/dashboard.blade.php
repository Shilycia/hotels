@extends('admin.admin')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="section-header">
    <div>
        <div class="section-title">Laporan Pendapatan Hotel</div>
        <div class="section-desc">Ringkasan tagihan lunas berdasarkan rentang waktu tertentu.</div>
    </div>
</div>

{{-- Form Filter Tanggal --}}
<div class="table-card" style="padding: 20px; margin-bottom: 20px; background: var(--surface);">
    <form action="{{ route('admin.reports.index') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
        <div>
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" style="width: auto;">
        </div>
        <div>
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" style="width: auto;">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Terapkan Filter</button>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline">Reset</a>
    </form>
</div>

{{-- Kartu Ringkasan --}}
<div class="stats-grid">
    <div class="stat-card" style="border-left: 4px solid var(--moss);">
        <div class="stat-top">
            <div class="stat-label">Pendapatan Kamar</div>
        </div>
        <div class="stat-value sm">Rp {{ number_format($roomRevenue, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid var(--clay);">
        <div class="stat-top">
            <div class="stat-label">Pendapatan Restoran</div>
        </div>
        <div class="stat-value sm">Rp {{ number_format($fnbRevenue, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #00A5CF;">
        <div class="stat-top">
            <div class="stat-label">Pendapatan Paket</div>
        </div>
        <div class="stat-value sm">Rp {{ number_format($packageRevenue, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="background: var(--sand2);">
        <div class="stat-top">
            <div class="stat-label">Total Keseluruhan</div>
        </div>
        <div class="stat-value" style="color: var(--bark);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
    </div>
</div>

{{-- Tabel Data Transaksi Lunas --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Rincian Transaksi ({{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})</div>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Waktu Lunas</th>
                    <th>ID Tagihan</th>
                    <th>Kategori Layanan</th>
                    <th>Metode Pembayaran</th>
                    <th>Total Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td style="font-size:12px;color:var(--ink3)">{{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') }}</td>
                    <td style="font-weight:600;color:var(--ink)">#P-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        @if($payment->booking_id) <span class="badge" style="background:var(--moss-soft); color:var(--moss);">Kamar</span>
                        @elseif($payment->restaurant_order_id) <span class="badge" style="background:var(--clay-soft); color:var(--clay);">Restoran</span>
                        @elseif($payment->package_order_id) <span class="badge" style="background:#e0f7fa; color:#007b8f;">Paket</span>
                        @endif
                    </td>
                    <td style="text-transform: capitalize; color:var(--ink2);">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                    <td style="font-weight:600;color:var(--bark)">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding: 20px; color:var(--ink3);">Tidak ada data transaksi lunas pada rentang tanggal ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection