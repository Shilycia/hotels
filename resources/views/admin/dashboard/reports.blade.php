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
    <form action="{{ route('admin.reports.index') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        <div>
            <label class="form-label" style="display: block; font-size: 11px; font-weight: 600; color: var(--ink2); margin-bottom: 5px; text-transform: uppercase;">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" style="width: auto; padding: 8px 12px; border: 1px solid var(--sand3); border-radius: var(--radius-sm); font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--ink); background: var(--sand);">
        </div>
        <div>
            <label class="form-label" style="display: block; font-size: 11px; font-weight: 600; color: var(--ink2); margin-bottom: 5px; text-transform: uppercase;">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" style="width: auto; padding: 8px 12px; border: 1px solid var(--sand3); border-radius: var(--radius-sm); font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--ink); background: var(--sand);">
        </div>
        <button type="submit" class="btn btn-primary" style="padding: 9px 14px; border: none; border-radius: var(--radius-sm); font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; background: var(--bark); color: #fff;">
            <i class="fas fa-filter"></i> Terapkan Filter
        </button>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline" style="padding: 9px 14px; border-radius: var(--radius-sm); font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 500; text-decoration: none; border: 1px solid var(--sand3); color: var(--ink2); display: inline-flex; align-items: center;">
            Reset
        </a>
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
        <div class="table-card-title">Rincian Transaksi Lunas ({{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})</div>
    </div>
    <div style="overflow-x:auto">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--sand);">
                <tr>
                    <th style="padding: 9px 20px; font-size: 10px; font-weight: 500; color: var(--ink3); text-align: left; text-transform: uppercase;">Waktu Lunas</th>
                    <th style="padding: 9px 20px; font-size: 10px; font-weight: 500; color: var(--ink3); text-align: left; text-transform: uppercase;">ID Tagihan</th>
                    <th style="padding: 9px 20px; font-size: 10px; font-weight: 500; color: var(--ink3); text-align: left; text-transform: uppercase;">Kategori Layanan</th>
                    <th style="padding: 9px 20px; font-size: 10px; font-weight: 500; color: var(--ink3); text-align: left; text-transform: uppercase;">Metode Pembayaran</th>
                    <th style="padding: 9px 20px; font-size: 10px; font-weight: 500; color: var(--ink3); text-align: left; text-transform: uppercase;">Total Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr style="border-top: 1px solid var(--sand2);">
                    <td style="padding: 11px 20px; font-size: 12px; color: var(--ink3);">
                        {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') : '-' }}
                    </td>
                    <td style="padding: 11px 20px; font-size: 12.5px; font-weight: 600; color: var(--ink);">
                        #P-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}
                    </td>
                    <td style="padding: 11px 20px; font-size: 12.5px;">
                        @if($payment->booking_id) 
                            <span class="badge" style="background:var(--moss-soft); color:var(--moss); padding: 3px 9px; border-radius: 50px; font-size: 10px; font-weight: 600;">Kamar</span>
                        @elseif($payment->restaurant_order_id) 
                            <span class="badge" style="background:var(--clay-soft); color:var(--clay); padding: 3px 9px; border-radius: 50px; font-size: 10px; font-weight: 600;">Restoran</span>
                        @elseif($payment->package_order_id) 
                            <span class="badge" style="background:#e0f7fa; color:#007b8f; padding: 3px 9px; border-radius: 50px; font-size: 10px; font-weight: 600;">Paket</span>
                        @else
                            <span class="badge" style="background:var(--sand2); color:var(--ink3); padding: 3px 9px; border-radius: 50px; font-size: 10px; font-weight: 600;">Lainnya</span>
                        @endif
                    </td>
                    <td style="padding: 11px 20px; font-size: 12.5px; text-transform: capitalize; color: var(--ink2); font-weight: 500;">
                        {{ str_replace('_', ' ', $payment->payment_method) }}
                    </td>
                    <td style="padding: 11px 20px; font-size: 13px; font-weight: 600; color: var(--bark);">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding: 30px; color:var(--ink3);">
                        <i class="fas fa-file-invoice-dollar" style="font-size: 32px; opacity: 0.5; margin-bottom: 10px; display: block;"></i>
                        Tidak ada data transaksi lunas pada rentang tanggal ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection