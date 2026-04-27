@extends('admin.admin')

@section('title', 'Manage Payment')

@section('content')
<style>
    /* ... CSS sama seperti sebelumnya ... */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(44,36,32,0.5); backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; transition: opacity 0.25s ease; }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-content { background: #fff; border-radius: var(--radius); width: 100%; max-width: 460px; padding: 24px; border: 1px solid var(--sand2); transform: translateY(16px); transition: transform 0.25s ease; }
    .modal-overlay.show .modal-content { transform: translateY(0); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-title { font-family: 'Lora', serif; font-size: 16px; color: var(--ink); font-weight: 600; }
    .btn-close { background: transparent; border: none; color: var(--ink3); cursor: pointer; font-size: 14px; padding: 4px; }
    .btn-close:hover { color: var(--clay); }
    .form-group { margin-bottom: 14px; }
    .form-label { display: block; font-size: 11px; font-weight: 600; color: var(--ink2); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.4px; }
    .form-control { width: 100%; padding: 8px 12px; border: 1px solid var(--sand3); border-radius: var(--radius-sm); font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--ink); background: var(--sand); outline: none; transition: border-color var(--transition); }
    select.form-control { cursor: pointer; }
    .form-control:focus { border-color: var(--bark); background: #fff; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--sand2); }
    .payment-summary { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 18px; }
    .pay-stat { background: #fff; border: 1px solid var(--sand2); border-radius: var(--radius); padding: 14px 16px; }
    .pay-stat-label { font-size: 10.5px; color: var(--ink3); text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px; font-weight: 600;}
    .pay-stat-val { font-family: 'Lora', serif; font-size: 20px; color: var(--ink); font-weight: 600; }
</style>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error" style="margin-bottom:16px"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

<div class="section-header">
    <div>
        <div class="section-title">Data Pembayaran</div>
        <div class="section-desc">Pantau dan kelola seluruh transaksi pembayaran Hotel Neo.</div>
    </div>
</div>

<div class="payment-summary">
    <div class="pay-stat">
        <div class="pay-stat-label">Total Lunas</div>
        <div class="pay-stat-val" style="color:var(--moss)">
            {{ $payments->where('payment_status','paid')->count() }}
        </div>
    </div>
    <div class="pay-stat">
        <div class="pay-stat-label">Menunggu</div>
        <div class="pay-stat-val" style="color:var(--bark)">
            {{ $payments->where('payment_status','pending')->count() }}
        </div>
    </div>
    <div class="pay-stat">
        <div class="pay-stat-label">Total Pendapatan</div>
        <div class="pay-stat-val" style="font-size:15px">
            Rp {{ number_format($payments->where('payment_status','paid')->sum('amount'), 0, ',', '.') }}
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Riwayat Pembayaran</div>
        <div class="table-card-actions">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input class="search-input" id="searchInput" placeholder="Cari nama tamu...">
            </div>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>#ID Tagihan</th>
                    <th>Sumber Tagihan</th>
                    <th>Nama Tamu</th>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="paymentTableBody">
                @forelse($payments as $payment)
                <tr>
                    <td style="font-size:12px;font-weight:500;color:var(--ink)">#P-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</td>
                    
                    <td style="font-size:12px;color:var(--ink3); font-weight:500;">
                        @if($payment->booking_id)
                            <i class="fas fa-bed" style="color:var(--moss)"></i> Kamar (#B-{{ str_pad($payment->booking_id, 4, '0', STR_PAD_LEFT) }})
                        @elseif($payment->restaurant_order_id)
                            <i class="fas fa-utensils" style="color:var(--clay)"></i> Resto (#O-{{ str_pad($payment->restaurant_order_id, 4, '0', STR_PAD_LEFT) }})
                        @elseif($payment->package_order_id)
                            <i class="fas fa-box" style="color:#00A5CF"></i> Paket (#PK-{{ str_pad($payment->package_order_id, 4, '0', STR_PAD_LEFT) }})
                        @else
                            Lainnya
                        @endif
                    </td>

                    <td style="font-weight:600;color:var(--ink)">
                        @if($payment->booking)
                            {{ $payment->booking->guest->name ?? '-' }}
                        @elseif($payment->restaurantOrder)
                            {{ $payment->restaurantOrder->guest->name ?? '-' }}
                        @elseif($payment->packageOrder)
                            {{ $payment->packageOrder->guest->name ?? '-' }}
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        <span class="badge" style="background:#e9ecef; color:#495057; font-weight:600;">
                            {{ strtoupper(str_replace('_', ' ', $payment->payment_method ?? 'CASH')) }}
                        </span>
                    </td>
                    <td style="font-weight:600;color:var(--ink)">Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}</td>
                    <td>
                        @if($payment->payment_status === 'paid')
                            <span class="badge badge-paid" style="background:#d1e7dd; color:#0f5132; font-weight:600;">Lunas</span>
                        @elseif($payment->payment_status === 'failed' || $payment->payment_status === 'cancelled')
                            <span class="badge badge-cancelled" style="background:#f8d7da; color:#842029; font-weight:600;">Gagal</span>
                        @else
                            <span class="badge badge-pending" style="background:#fff3cd; color:#856404; font-weight:600;">Pending</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--ink3)">{{ $payment->created_at->format('d M Y, H:i') }}</td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            
                            <button class="btn btn-outline btn-sm" title="Verifikasi Manual"
                                onclick="openEditModal('{{ $payment->id }}','{{ $payment->payment_status }}','{{ $payment->payment_method ?? `cash` }}')">
                                <i class="fas fa-pen"></i> Update
                            </button>

                            <form id="delete-form-{{ $payment->id }}" action="{{ route('admin.payments.destroy', $payment->id) }}" method="POST" style="display:none">
                                @csrf @method('DELETE')
                            </form>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $payment->id }}')" title="Hapus Data">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state" style="text-align: center; padding: 30px;">
                            <i class="fas fa-credit-card" style="font-size: 32px; color: var(--sand3); margin-bottom: 10px;"></i>
                            <p>Belum ada riwayat tagihan atau pembayaran.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Update Status Manual --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Update Pembayaran Manual</div>
            <button class="btn-close" type="button" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditPayment" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Status Pembayaran</label>
                <select name="payment_status" id="edit_status" class="form-control" required>
                    <option value="pending">Pending (Menunggu)</option>
                    <option value="paid">Paid (Lunas)</option>
                    <option value="failed">Failed/Cancelled (Batal)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Metode Pembayaran (Jika Lunas)</label>
                <select name="payment_method" id="edit_method" class="form-control" required>
                    <option value="cash">Tunai (Cash di Kasir)</option>
                    <option value="transfer">Transfer Bank Manual</option>
                    <option value="credit_card">Kartu Kredit/Debit (EDC)</option>
                    <option value="e_wallet">QRIS / E-Wallet</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openModal(id)  { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }

    function openEditModal(id, status, method) {
        document.getElementById('edit_status').value      = status;
        document.getElementById('edit_method').value      = method;
        document.getElementById('formEditPayment').action = '/admin/payments/' + id;
        openModal('modalEdit');
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Data Pembayaran?',
            text: 'Tindakan ini akan menghapus tagihan secara permanen dari sistem keuangan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c07850',
            cancelButtonColor: '#8b7355',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            backdrop: 'rgba(44,36,32,0.5)'
        }).then(r => { if (r.isConfirmed) document.getElementById('delete-form-' + id).submit(); });
    }

    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#paymentTableBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush