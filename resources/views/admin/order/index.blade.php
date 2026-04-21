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
                    <th>Tamu</th>
                    <th>Item Pesanan</th>
                    <th>Total Harga</th>
                    <th>Status & Tagihan</th>
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
                    
                    {{-- 🟢 PERBAIKAN TAMPILAN STATUS: Tambah Indikator Link Payment --}}
                    <td>
                        @if($order->status === 'paid')
                            <span class="badge badge-paid" style="background:#c8d8b8; color:#4a7c59; padding:4px 8px; border-radius:4px;">Lunas (Paid)</span>
                        @else
                            <span class="badge badge-pending" style="background:#fdebd0; color:#c07850; padding:4px 8px; border-radius:4px;">Dipesan (Ordered)</span>
                        @endif
                        
                        {{-- Memanggil relasi payment secara lazy untuk menampilkan ID Tagihan --}}
                        @if($order->payment)
                            <div style="font-size: 10px; margin-top: 6px; color: var(--ink3);">
                                <i class="fas fa-link" style="color: var(--clay);"></i> Tagihan: #P-{{ str_pad($order->payment->id, 4, '0', STR_PAD_LEFT) }}
                            </div>
                        @endif
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

{{-- Modal Tambah Order --}}
<div class="modal-overlay" id="modalAdd">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <div class="modal-title">Tambah Order Restoran</div>
            <button class="btn-close" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.orders.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Tamu (Pemesan)</label>
                <select name="guest_id" class="form-control" required>
                    <option value="">-- Pilih Tamu --</option>
                    @foreach($guests as $guest)
                        <option value="{{ $guest->id }}">{{ $guest->name }}</option>
                    @endforeach
                </select>
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
                    <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()" style="padding: 0 12px;"><i class="fas fa-times"></i></button>
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
            text: 'Data order ini akan dihapus permanen. Tagihan yang terhubung juga akan ikut terhapus.',
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
        // Kloning baris menu pertama
        const firstRow = container.querySelector('.menu-row').cloneNode(true);
        // Reset nilainya
        firstRow.querySelector('select').value = '';
        firstRow.querySelector('input').value = '1';
        // Tambahkan ke container
        container.appendChild(firstRow);
    }

    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#orderTableBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush