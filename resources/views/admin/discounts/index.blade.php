@extends('admin.admin')

@section('title', 'Manage Discount')

@section('content')
<style>
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(44,36,32,0.5); backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; transition: opacity 0.25s ease; }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-content { background: #fff; border-radius: var(--radius); width: 100%; max-width: 550px; padding: 24px; border: 1px solid var(--sand2); transform: translateY(16px); transition: transform 0.25s ease; max-height: 90vh; overflow-y: auto; }
    .modal-overlay.show .modal-content { transform: translateY(0); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-title { font-family: 'Lora', serif; font-size: 16px; color: var(--ink); font-weight: 600; }
    .btn-close { background: transparent; border: none; color: var(--ink3); cursor: pointer; font-size: 14px; padding: 4px; transition: color var(--transition); }
    .btn-close:hover { color: var(--clay); }
    .form-group { margin-bottom: 14px; }
    .form-label { display: block; font-size: 11px; font-weight: 600; color: var(--ink2); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.4px; }
    .form-control { width: 100%; padding: 8px 12px; border: 1px solid var(--sand3); border-radius: var(--radius-sm); font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--ink); background: var(--sand); outline: none; transition: border-color var(--transition); }
    select.form-control { cursor: pointer; }
    .form-control:focus { border-color: var(--bark); background: #fff; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--sand2); }
    .code-badge { background: var(--clay); color: white; padding: 3px 8px; border-radius: 4px; font-family: monospace; font-weight: bold; letter-spacing: 1px; font-size: 11px; }
    .auto-badge { background: var(--sand2); color: var(--ink2); padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
</style>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error" style="margin-bottom:16px"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

<div class="section-header">
    <div>
        <div class="section-title">Data Promo & Voucher</div>
        <div class="section-desc">Kelola aturan diskon otomatis dan kode voucher untuk transaksi tamu Hotel Neo.</div>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAdd')">
        <i class="fas fa-plus"></i> Tambah Promo
    </button>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Daftar Promo</div>
        <div class="table-card-actions">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input class="search-input" id="searchInput" placeholder="Cari nama atau kode...">
            </div>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Info Promo</th>
                    <th>Kode / Tipe</th>
                    <th>Potongan</th>
                    <th>Berlaku Untuk</th>
                    <th>Periode Aktif</th>
                    <th>Status</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="discountTableBody">
                @forelse($discounts as $disc)
                <tr>
                    <td>
                        <strong style="color:var(--ink); display:block;">{{ $disc->name }}</strong>
                        <span style="font-size:11px;color:var(--ink3)">Min Trx: Rp {{ number_format($disc->min_transaction_amount, 0, ',', '.') }}</span>
                        @if($disc->is_stackable)
                            <br><span style="font-size:10px; color:#0f5132; background:#d1e7dd; padding:2px 6px; border-radius:3px; margin-top:4px; display:inline-block;"><i class="fas fa-layer-group"></i> Bisa Digabung</span>
                        @endif
                    </td>
                    <td>
                        @if($disc->code)
                            <span class="code-badge"><i class="fas fa-ticket-alt"></i> {{ $disc->code }}</span>
                        @else
                            <span class="auto-badge"><i class="fas fa-magic"></i> OTOMATIS</span>
                        @endif
                    </td>
                    <td style="font-weight:600;color:var(--clay)">
                        {{ $disc->discount_type == 'percentage' ? $disc->discount_value . '%' : 'Rp ' . number_format($disc->discount_value, 0, ',', '.') }}
                    </td>
                    <td>
                        <span class="badge badge-info" style="text-transform:capitalize;">
                            {{ str_replace('_', ' ', $disc->applicable_to) }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--ink3)">
                        {{ \Carbon\Carbon::parse($disc->valid_from)->format('d M') }} - {{ \Carbon\Carbon::parse($disc->valid_until)->format('d M y') }}
                    </td>
                    <td>
                        @if($disc->is_active && $disc->valid_until >= now()->format('Y-m-d'))
                            <span class="badge" style="background:#d1e7dd; color:#0f5132; font-weight:600;">Aktif</span>
                        @else
                            <span class="badge" style="background:#f8d7da; color:#842029; font-weight:600;">Nonaktif / Expired</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            <button class="btn btn-outline btn-sm" onclick="openEditModal({{ json_encode($disc) }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form id="delete-form-{{ $disc->id }}" action="{{ route('admin.discounts.destroy', $disc->id) }}" method="POST" style="display:none">
                                @csrf @method('DELETE')
                            </form>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $disc->id }}', '{{ addslashes($disc->name) }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state" style="text-align: center; padding: 30px;">
                            <i class="fas fa-ticket-alt" style="font-size: 32px; color: var(--sand3); margin-bottom: 10px;"></i>
                            <p>Belum ada data promo/diskon.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($discounts->hasPages())
    <div style="padding:12px 20px;border-top:1px solid var(--sand2)">{{ $discounts->links() }}</div>
    @endif
</div>

{{-- Modal Tambah --}}
<div class="modal-overlay" id="modalAdd">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Tambah Promo / Voucher Baru</div>
            <button class="btn-close" type="button" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.discounts.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Promo</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Diskon Tahun Baru" required>
                </div>
                <div class="form-group">
                    <label class="form-label text-primary"><i class="fas fa-ticket-alt"></i> Kode Voucher (Opsional)</label>
                    <input type="text" name="code" class="form-control" placeholder="Kosongkan jika Otomatis" style="text-transform:uppercase">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tipe Diskon</label>
                    <select name="discount_type" class="form-control" required>
                        <option value="percentage">Persen (%)</option>
                        <option value="fixed_amount">Nominal (Rp)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Besaran Diskon</label>
                    <input type="number" name="discount_value" class="form-control" min="0" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Berlaku Untuk</label>
                    <select name="applicable_to" class="form-control" required>
                        <option value="all">Semua Layanan</option>
                        <option value="bookings">Hanya Kamar</option>
                        <option value="restaurant_orders">Hanya Restoran</option>
                        <option value="package_orders">Hanya Paket Bundling</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label text-success"><i class="fas fa-layer-group"></i> Bisa Digabung?</label>
                    <select name="is_stackable" class="form-control" required>
                        <option value="0">Tidak (Eksklusif)</option>
                        <option value="1">Ya (Bisa ditumpuk promo lain)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Minimal Transaksi (Rp)</label>
                <input type="number" name="min_transaction_amount" class="form-control" value="0" min="0">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Mulai Berlaku</label>
                    <input type="date" name="valid_from" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Berakhir Pada</label>
                    <input type="date" name="valid_until" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-control" required>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAdd')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Edit Promo</div>
            <button class="btn-close" type="button" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditDisc" method="POST" data-base-url="{{ url('admin/discounts') }}">
            @csrf @method('PUT')
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Promo</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label text-primary"><i class="fas fa-ticket-alt"></i> Kode Voucher (Opsional)</label>
                    <input type="text" name="code" id="edit_code" class="form-control" placeholder="Kosongkan jika Otomatis" style="text-transform:uppercase">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tipe Diskon</label>
                    <select name="discount_type" id="edit_type" class="form-control" required>
                        <option value="percentage">Persen (%)</option>
                        <option value="fixed_amount">Nominal (Rp)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Besaran Diskon</label>
                    <input type="number" name="discount_value" id="edit_val" class="form-control" min="0" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Berlaku Untuk</label>
                    <select name="applicable_to" id="edit_app" class="form-control" required>
                        <option value="all">Semua Layanan</option>
                        <option value="bookings">Hanya Kamar</option>
                        <option value="restaurant_orders">Hanya Restoran</option>
                        <option value="package_orders">Hanya Paket Bundling</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label text-success"><i class="fas fa-layer-group"></i> Bisa Digabung?</label>
                    <select name="is_stackable" id="edit_stackable" class="form-control" required>
                        <option value="0">Tidak (Eksklusif)</option>
                        <option value="1">Ya (Bisa ditumpuk promo lain)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Minimal Transaksi (Rp)</label>
                <input type="number" name="min_transaction_amount" id="edit_min" class="form-control" min="0">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Mulai Berlaku</label>
                    <input type="date" name="valid_from" id="edit_start" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Berakhir Pada</label>
                    <input type="date" name="valid_until" id="edit_end" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="is_active" id="edit_active" class="form-control" required>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-rotate-right"></i> Update</button>
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

    function openEditModal(disc) {
        document.getElementById('edit_name').value   = disc.name;
        document.getElementById('edit_code').value   = disc.code || ''; // Set kode jika ada
        document.getElementById('edit_type').value   = disc.discount_type;
        document.getElementById('edit_val').value    = disc.discount_value;
        document.getElementById('edit_app').value    = disc.applicable_to;
        document.getElementById('edit_stackable').value = disc.is_stackable ? '1' : '0'; // Set stackable
        document.getElementById('edit_min').value    = disc.min_transaction_amount || 0;
        document.getElementById('edit_start').value  = disc.valid_from ? disc.valid_from.substring(0,10) : '';
        document.getElementById('edit_end').value    = disc.valid_until ? disc.valid_until.substring(0,10) : '';
        document.getElementById('edit_active').value = disc.is_active ? '1' : '0';

        const baseUrl = document.getElementById('formEditDisc').dataset.baseUrl;
        document.getElementById('formEditDisc').action = baseUrl + '/' + disc.id;
        openModal('modalEdit');
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Promo?',
            text: 'Aturan diskon "' + name + '" akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c07850',
            cancelButtonColor: '#8b7355',
            confirmButtonText: 'Ya, Hapus'
        }).then(r => { if (r.isConfirmed) document.getElementById('delete-form-' + id).submit(); });
    }

    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#discountTableBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush