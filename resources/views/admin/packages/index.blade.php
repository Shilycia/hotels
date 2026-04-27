@extends('admin.admin')

@section('title', 'Manage Package')

@section('content')
<style>
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(44,36,32,0.5); backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; transition: opacity 0.25s ease; }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-content { background: #fff; border-radius: var(--radius); width: 100%; max-width: 480px; padding: 24px; border: 1px solid var(--sand2); transform: translateY(16px); transition: transform 0.25s ease; max-height: 90vh; overflow-y: auto; }
    .modal-overlay.show .modal-content { transform: translateY(0); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-title { font-family: 'Lora', serif; font-size: 16px; color: var(--ink); font-weight: 600; }
    .btn-close { background: transparent; border: none; color: var(--ink3); cursor: pointer; font-size: 14px; padding: 4px; transition: color var(--transition); }
    .btn-close:hover { color: var(--clay); }
    .form-group { margin-bottom: 14px; }
    .form-label { display: block; font-size: 11px; font-weight: 600; color: var(--ink2); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.4px; }
    .form-control { width: 100%; padding: 8px 12px; border: 1px solid var(--sand3); border-radius: var(--radius-sm); font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--ink); background: var(--sand); outline: none; transition: border-color var(--transition); }
    textarea.form-control { resize: vertical; min-height: 80px; }
    select.form-control { cursor: pointer; }
    .form-control:focus { border-color: var(--bark); background: #fff; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--sand2); }
</style>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="section-header">
    <div>
        <div class="section-title">Katalog Paket Bundling</div>
        <div class="section-desc">Kelola paket penawaran khusus (Kamar + Makan/Aktivitas) untuk tamu.</div>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAdd')">
        <i class="fas fa-plus"></i> Tambah Paket
    </button>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Daftar Paket</div>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Nama Paket</th>
                    <th>Tipe Kamar (Opsional)</th>
                    <th>Harga Paket</th>
                    <th>Status</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="packageTableBody">
                @forelse($packages as $pkg)
                <tr>
                    <td>
                        <strong style="color:var(--ink); display:block;">{{ $pkg->name }}</strong>
                        <span style="font-size:11px;color:var(--ink3)">{{ \Illuminate\Support\Str::limit($pkg->description, 40) }}</span>
                    </td>
                    <td style="color:var(--ink3);font-size:13px;">
                        @if($pkg->roomType)
                            <i class="fas fa-bed" style="margin-right:4px;"></i>{{ $pkg->roomType->name }}
                        @else
                            <span style="font-style:italic">Tanpa Kamar (Hanya Layanan)</span>
                        @endif
                    </td>
                    <td style="font-weight:600;color:var(--clay)">Rp {{ number_format($pkg->total_price, 0, ',', '.') }}</td>
                    <td>
                        @if($pkg->is_active)
                            <span class="badge" style="background:#d1e7dd; color:#0f5132; font-weight:600;">Tersedia</span>
                        @else
                            <span class="badge" style="background:#f8d7da; color:#842029; font-weight:600;">Sembunyi</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            <button class="btn btn-outline btn-sm" onclick="openEditModal({{ json_encode($pkg) }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form id="delete-form-{{ $pkg->id }}" action="{{ route('admin.packages.destroy', $pkg->id) }}" method="POST" style="display:none">
                                @csrf @method('DELETE')
                            </form>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $pkg->id }}', '{{ addslashes($pkg->name) }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state" style="text-align: center; padding: 30px;">
                            <i class="fas fa-box-open" style="font-size: 32px; color: var(--sand3); margin-bottom: 10px;"></i>
                            <p>Belum ada paket bundling yang dibuat.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($packages->hasPages())
    <div style="padding:12px 20px;border-top:1px solid var(--sand2)">{{ $packages->links() }}</div>
    @endif
</div>

{{-- Modal Tambah --}}
<div class="modal-overlay" id="modalAdd">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Tambah Paket Baru</div>
            <button class="btn-close" type="button" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.packages.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Paket Bundling</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Paket Honeymoon" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tertaut dengan Kamar (Opsional)</label>
                <select name="room_type_id" class="form-control">
                    <option value="">-- Tidak Perlu Kamar --</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} (Harga Dasar: Rp {{ number_format($type->price,0,',','.') }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Harga Keseluruhan Paket (Rp)</label>
                <input type="number" name="total_price" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">Status Tayang</label>
                <select name="is_active" class="form-control" required>
                    <option value="1">Tersedia untuk Dibeli</option>
                    <option value="0">Sembunyikan</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi Fasilitas Paket</label>
                <textarea name="description" class="form-control" placeholder="Termasuk 1x Romantic Dinner, Spa, dll..."></textarea>
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
            <div class="modal-title">Edit Paket</div>
            <button class="btn-close" type="button" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditPkg" method="POST" data-base-url="{{ url('admin/packages') }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Paket Bundling</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tertaut dengan Kamar</label>
                <select name="room_type_id" id="edit_room_type_id" class="form-control">
                    <option value="">-- Tidak Perlu Kamar --</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Harga Keseluruhan Paket (Rp)</label>
                <input type="number" name="total_price" id="edit_price" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">Status Tayang</label>
                <select name="is_active" id="edit_active" class="form-control" required>
                    <option value="1">Tersedia untuk Dibeli</option>
                    <option value="0">Sembunyikan</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi Fasilitas Paket</label>
                <textarea name="description" id="edit_desc" class="form-control"></textarea>
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

    function openEditModal(pkg) {
        document.getElementById('edit_name').value         = pkg.name;
        document.getElementById('edit_room_type_id').value = pkg.room_type_id || '';
        document.getElementById('edit_price').value        = pkg.total_price;
        document.getElementById('edit_active').value       = pkg.is_active ? '1' : '0';
        document.getElementById('edit_desc').value         = pkg.description || '';

        const baseUrl = document.getElementById('formEditPkg').dataset.baseUrl;
        document.getElementById('formEditPkg').action = baseUrl + '/' + pkg.id;
        openModal('modalEdit');
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Paket?',
            text: 'Paket "' + name + '" akan dihapus permanen dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c07850',
            cancelButtonColor: '#8b7355',
            confirmButtonText: 'Ya, Hapus'
        }).then(r => { if (r.isConfirmed) document.getElementById('delete-form-' + id).submit(); });
    }
</script>
@endpush