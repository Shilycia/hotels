@extends('admin.admin')

@section('title', 'Manage Order')

@section('content')
<style>
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(44,36,32,0.5); backdrop-filter: blur(3px);
        display: none; align-items: center; justify-content: center;
        z-index: 1000; opacity: 0; transition: opacity 0.25s ease;
    }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-content {
        background: #fff; border-radius: var(--radius); width: 100%; max-width: 480px;
        padding: 24px; border: 1px solid var(--sand2);
        transform: translateY(16px); transition: transform 0.25s ease;
    }
    .modal-overlay.show .modal-content { transform: translateY(0); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-title { font-family: 'Lora', serif; font-size: 16px; color: var(--ink); font-weight: 400; }
    .btn-close { background: transparent; border: none; color: var(--ink3); cursor: pointer; font-size: 14px; padding: 4px; }
    .btn-close:hover { color: var(--clay); }
    .form-group { margin-bottom: 14px; }
    .form-label { display: block; font-size: 11px; font-weight: 500; color: var(--ink2); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.4px; }
    .form-control {
        width: 100%; padding: 8px 12px; border: 1px solid var(--sand3);
        border-radius: var(--radius-sm); font-family: 'DM Sans', sans-serif;
        font-size: 13px; color: var(--ink); background: var(--sand);
        outline: none; transition: border-color var(--transition);
    }
    select.form-control { cursor: pointer; }
    .form-control:focus { border-color: var(--bark); background: #fff; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--sand2); }
</style>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error" style="margin-bottom:16px"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-error" style="margin-bottom:16px">
        <div style="font-weight: bold; margin-bottom: 5px;"><i class="fas fa-exclamation-triangle"></i> Gagal menyimpan data:</div>
        <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="section-header">
    <div>
        <div class="section-title">Data Order Restoran</div>
        <div class="section-desc">Kelola pesanan makanan dan minuman dari tamu Hotel Neo.</div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Daftar Order</div>
        <div class="table-card-actions">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input class="search-input" id="searchInput" placeholder="Cari tamu atau menu...">
            </div>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Tamu</th>
                    <th>Item Pesanan</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Waktu Order</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="orderTableBody">
                @forelse($orders as $order)
                <tr>
                    <td style="font-size:12px;font-weight:500;color:var(--ink)">#O-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td style="font-weight:500;color:var(--ink)">{{ $order->guest->name ?? 'Tamu Dihapus' }}</td>
                    <td style="font-size:12px;color:var(--ink3)">
                        @if($order->details && $order->details->count())
                            <ul style="margin:0; padding-left:15px">
                                @foreach($order->details as $detail)
                                    <li>{{ $detail->menu->name ?? 'Menu Dihapus' }} (x{{ $detail->quantity }})</li>
                                @endforeach
                            </ul>
                        @else
                            -
                        @endif
                    </td>
                    <td style="font-weight:500;color:var(--ink)">Rp {{ number_format($order->total_price ?? 0, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $order->status === 'paid' ? 'badge-active' : 'badge-pending' }}">
                            {{ $order->status === 'paid' ? 'Lunas (Paid)' : 'Dipesan (Ordered)' }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--ink3)">{{ $order->created_at->format('d M Y, H:i') }}</td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            <button class="btn btn-outline btn-sm"
                                onclick="openEditModal('{{ $order->id }}','{{ $order->status }}')">
                                <i class="fas fa-pen"></i> Update
                            </button>
                            <form id="delete-form-{{ $order->id }}" action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" style="display:none">
                                @csrf @method('DELETE')
                            </form>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $order->id }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-receipt"></i>
                            <p>Belum ada data order restoran.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Render pagination jika menggunakan paginate() --}}
    @if(isset($orders) && method_exists($orders, 'links'))
    <div style="padding:12px 20px;border-top:1px solid var(--sand2)">
        {{ $orders->links() }}
    </div>
    @endif
</div>

{{-- Modal Update Status --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Update Status Order</div>
            <button class="btn-close" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditOrder" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Status Pesanan</label>
                <select name="status" id="edit_status" class="form-control" required>
                    <option value="ordered">Dipesan (Ordered)</option>
                    <option value="paid">Lunas (Paid)</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
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

    function openEditModal(id, status) {
        document.getElementById('edit_status').value  = status;
        document.getElementById('formEditOrder').action   = '/admin/orders/' + id;
        openModal('modalEdit');
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Order?',
            text: 'Data order ini akan dihapus permanen.',
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
        document.querySelectorAll('#orderTableBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush