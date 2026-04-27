@extends('admin.admin')

@section('title', 'Manage Order')

@section('content')
<style>
    /* Style tetap sama persis seperti milikmu */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(44,36,32,0.5); backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; transition: opacity 0.25s ease; }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-content { background: #fff; border-radius: var(--radius); width: 100%; max-width: 550px; padding: 24px; border: 1px solid var(--sand2); transform: translateY(16px); transition: transform 0.25s ease; max-height: 90vh; overflow-y: auto;}
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
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
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
    <button class="btn btn-primary" onclick="openModal('modalAdd')">
        <i class="fas fa-plus"></i> Tambah Order
    </button>
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
                    <th>#ID Order</th>
                    <th>Tamu & Tipe</th>
                    <th>Item Pesanan</th>
                    <th>Total Harga</th>
                    <th>Status Dapur</th>
                    <th>Status Bayar</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="orderTableBody">
                @forelse($orders as $order)
                <tr>
                    <td style="font-size:12px;font-weight:500;color:var(--ink)">#O-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <strong style="color:var(--ink); display:block;">{{ $order->guest->name ?? 'Tamu Dihapus' }}</strong>
                        <span style="font-size:11px;color:var(--ink3);text-transform:capitalize;">
                            <i class="fas fa-concierge-bell"></i> {{ str_replace('_', ' ', $order->order_type) }} 
                            {{ $order->table_or_room ? '('.$order->table_or_room.')' : '' }}
                        </span>
                    </td>
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
                    <td style="font-weight:600;color:var(--clay)">Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}</td>
                    
                    <td>
                        {{-- Menggunakan Enum Dapur: pending, preparing, served, completed --}}
                        @php
                            $badgeColor = '#fdebd0'; $textColor = '#c07850';
                            if($order->status == 'preparing') { $badgeColor = '#fff3cd'; $textColor = '#856404'; }
                            if($order->status == 'served') { $badgeColor = '#d1e7dd'; $textColor = '#0f5132'; }
                            if($order->status == 'completed') { $badgeColor = '#c8d8b8'; $textColor = '#4a7c59'; }
                        @endphp
                        <span class="badge" style="background:{{$badgeColor}}; color:{{$textColor}}; padding:4px 8px; border-radius:4px; font-weight:600;">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    
                    <td>
                        @if($order->payment && $order->payment->payment_status == 'paid')
                            <span class="badge" style="background:#d1e7dd; color:#0f5132; padding:4px 8px; border-radius:4px; font-weight:600;">Lunas</span>
                        @else
                            <span class="badge" style="background:#f8d7da; color:#842029; padding:4px 8px; border-radius:4px; font-weight:600;">Belum Lunas</span>
                        @endif
                    </td>

                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            <button class="btn btn-outline btn-sm" onclick="openEditModal('{{ $order->id }}','{{ $order->status }}')">
                                <i class="fas fa-clipboard-check"></i> Proses
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
                        <div class="empty-state" style="text-align: center; padding: 30px;">
                            <i class="fas fa-receipt" style="font-size: 32px; color: var(--sand3); margin-bottom: 10px;"></i>
                            <p>Belum ada data order restoran.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Update Status Dapur --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Update Status Dapur</div>
            <button class="btn-close" type="button" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditOrder" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Status Pesanan</label>
                <select name="status" id="edit_status" class="form-control" required>
                    <option value="pending">Menunggu (Pending)</option>
                    <option value="preparing">Dimasak (Preparing)</option>
                    <option value="served">Dihidangkan (Served)</option>
                    <option value="completed">Selesai (Completed)</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Status</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Order --}}
<div class="modal-overlay" id="modalAdd">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <div class="modal-title">Tambah Order Restoran</div>
            <button class="btn-close" type="button" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.orders.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tamu (Pemesan)</label>
                    <select name="guest_id" class="form-control" required>
                        <option value="">-- Pilih Tamu --</option>
                        @foreach($guests as $guest)
                            <option value="{{ $guest->id }}">{{ $guest->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Layanan</label>
                    <select name="order_type" class="form-control" required>
                        <option value="dine_in">Dine In (Makan di Tempat)</option>
                        <option value="in_room">In-Room (Antar ke Kamar)</option>
                        <option value="takeaway">Takeaway (Bungkus)</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nomor Meja / Kamar (Opsional)</label>
                    <input type="text" name="table_or_room" class="form-control" placeholder="Cth: Meja 04 atau Kamar 102">
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" class="form-control" placeholder="Cth: Jangan terlalu pedas">
                </div>
            </div>
            
            <hr style="border-color: var(--sand2); margin: 20px 0;">
            <div class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                <span>Item Pesanan</span>
                <button type="button" class="btn btn-sm btn-outline" onclick="addMenuRow()" style="padding: 2px 8px; font-size: 11px;">+ Tambah Menu</button>
            </div>

            <div id="menu-container">
                <div class="menu-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <div style="flex: 3;">
                        <select name="menu_id[]" class="form-control" required>
                            <option value="">-- Pilih Menu --</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}">{{ $menu->name }} (Rp {{ number_format($menu->price, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" value="1" required>
                    </div>
                    <button type="button" class="btn btn-danger" onclick="removeRow(this)" style="padding: 0 12px;"><i class="fas fa-times"></i></button>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAdd')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Buat Pesanan</button>
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
            text: 'Data order dan detailnya akan dihapus permanen. Tagihan yang terhubung juga akan ikut terhapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c07850',
            cancelButtonColor: '#8b7355',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            backdrop: 'rgba(44,36,32,0.5)'
        }).then(r => { if (r.isConfirmed) document.getElementById('delete-form-' + id).submit(); });
    }

    function addMenuRow() {
        const container = document.getElementById('menu-container');
        const firstRow = container.querySelector('.menu-row').cloneNode(true);
        firstRow.querySelector('select').value = '';
        firstRow.querySelector('input').value = '1';
        container.appendChild(firstRow);
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.menu-row');
        if(rows.length > 1) {
            btn.parentElement.remove();
        } else {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Pesanan minimal harus memiliki 1 menu!' });
        }
    }

    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#orderTableBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush