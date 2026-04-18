@extends('admin.admin')

@section('title', 'Manage Room')

@section('content')
<style>
    /* CSS Modal (Sama dengan halaman user/role) */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; transition: opacity 0.3s ease; }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-content { background: var(--surface); border-radius: var(--radius-lg); width: 100%; max-width: 500px; padding: 2rem; box-shadow: var(--shadow-lg); transform: translateY(20px); transition: transform 0.3s ease; }
    .modal-overlay.show .modal-content { transform: translateY(0); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .modal-title { font-family: 'Fraunces', serif; font-size: 1.25rem; font-weight: 700; color: var(--text-dark); }
    .btn-close { background: transparent; border: none; font-size: 1.2rem; color: var(--text-light); cursor: pointer; }
    .btn-close:hover { color: var(--rose); }
    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-mid); margin-bottom: 0.4rem; }
    .form-control { width: 100%; padding: 0.65rem 1rem; border: 1px solid var(--border); border-radius: var(--radius-sm); font-family: inherit; font-size: 0.85rem; }
    .form-control:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-soft); }
    .modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.25rem; }
</style>

<div style="padding: 1rem 0;">

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom: 1.5rem;"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1.5rem;"><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan input. Periksa kembali form Anda.</div>
    @endif

    <div class="section-header">
        <div>
            <div class="section-title">Inventaris Kamar</div>
            <div class="section-desc">Kelola ketersediaan, tipe, dan nomor kamar Hotel Neo.</div>
        </div>
        <button class="btn btn-primary" onclick="openModal('modalAdd')"><i class="fas fa-plus"></i> Tambah Kamar</button>
    </div>

    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Daftar Kamar</div>
            <div class="table-card-actions">
                <input type="text" class="search-input" placeholder="🔍 Cari no kamar...">
            </div>
        </div>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>No. Kamar</th>
                        <th>Tipe Kamar</th>
                        <th>Harga / Malam</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--bg); border: 1px solid var(--border); display:flex; align-items:center; justify-content:center; font-weight:700; color:var(--text-dark);">
                                    {{ $room->room_number }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-dark);">
                                {{ $room->roomType->name ?? 'Tipe Dihapus' }}
                            </div>
                        </td>
                        <td>Rp {{ number_format($room->roomType->price ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @if($room->status == 'available')
                                <span class="badge badge-paid"><i class="fas fa-check-circle"></i> Tersedia</span>
                            @elseif($room->status == 'occupied')
                                <span class="badge badge-active"><i class="fas fa-bed"></i> Terisi</span>
                            @else
                                <span class="badge badge-pending"><i class="fas fa-wrench"></i> Maintenance</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                <button class="btn btn-outline btn-sm" title="Edit Kamar" 
                                    onclick="openEditModal('{{ $room->id }}', '{{ $room->room_number }}', '{{ $room->room_type_id }}', '{{ $room->status }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <form id="delete-form-{{ $room->id }}" action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" class="btn btn-danger btn-sm" title="Hapus Kamar" onclick="confirmDelete('{{ $room->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-door-closed"></i>
                                <p>Belum ada data kamar di sistem.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalAdd">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Tambah Kamar Baru</div>
            <button class="btn-close" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.rooms.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nomor Kamar</label>
                <input type="text" name="room_number" class="form-control" placeholder="Contoh: 101, 204A" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tipe Kamar</label>
                <select name="room_type_id" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Tipe Kamar --</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} (Rp {{ number_format($type->price, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status Awal</label>
                <select name="status" class="form-control" required>
                    <option value="available" selected>Tersedia (Available)</option>
                    <option value="occupied">Terisi (Occupied)</option>
                    <option value="maintenance">Perbaikan (Maintenance)</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAdd')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Kamar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modalEdit">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Edit Data Kamar</div>
            <button class="btn-close" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditRoom" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nomor Kamar</label>
                <input type="text" name="room_number" id="edit_room_number" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tipe Kamar</label>
                <select name="room_type_id" id="edit_room_type_id" class="form-control" required>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" id="edit_status" class="form-control" required>
                    <option value="available">Tersedia (Available)</option>
                    <option value="occupied">Terisi (Occupied)</option>
                    <option value="maintenance">Perbaikan (Maintenance)</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Update Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openModal(modalId) { document.getElementById(modalId).classList.add('show'); }
    function closeModal(modalId) { document.getElementById(modalId).classList.remove('show'); }

    // Mengisi form edit secara otomatis
    function openEditModal(id, room_number, room_type_id, status) {
        document.getElementById('edit_room_number').value = room_number;
        document.getElementById('edit_room_type_id').value = room_type_id;
        document.getElementById('edit_status').value = status;
        
        document.getElementById('formEditRoom').action = '/admin/rooms/' + id;
        openModal('modalEdit');
    }

    // Konfirmasi hapus dengan SweetAlert2
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Kamar?',
            text: "Data kamar akan dihapus permanen dari sistem!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#475569',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            backdrop: `rgba(15, 23, 42, 0.6)`
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
        })
    }
</script>
@endpush