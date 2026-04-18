@extends('admin.admin')

@section('title', 'Manage Tipe Kamar')

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
        background: #fff; border-radius: var(--radius); width: 100%; max-width: 460px;
        padding: 24px; border: 1px solid var(--sand2);
        transform: translateY(16px); transition: transform 0.25s ease;
    }
    .modal-overlay.show .modal-content { transform: translateY(0); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-title { font-family: 'Lora', serif; font-size: 16px; color: var(--ink); font-weight: 400; }
    .btn-close { background: transparent; border: none; color: var(--ink3); cursor: pointer; font-size: 14px; padding: 4px; transition: color var(--transition); }
    .btn-close:hover { color: var(--clay); }
    .form-group { margin-bottom: 14px; }
    .form-label { display: block; font-size: 11px; font-weight: 500; color: var(--ink2); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.4px; }
    .form-control {
        width: 100%; padding: 8px 12px; border: 1px solid var(--sand3);
        border-radius: var(--radius-sm); font-family: 'DM Sans', sans-serif;
        font-size: 13px; color: var(--ink); background: var(--sand);
        outline: none; transition: border-color var(--transition);
    }
    textarea.form-control { resize: vertical; min-height: 75px; }
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
    <div class="alert alert-error" style="margin-bottom:16px"><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan input form.</div>
@endif

<div class="section-header">
    <div>
        <div class="section-title">Kategori Tipe Kamar</div>
        <div class="section-desc">Kelola harga dan deskripsi untuk setiap tipe kamar di Hotel Neo.</div>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAdd')">
        <i class="fas fa-plus"></i> Tambah Tipe
    </button>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Daftar Tipe Kamar</div>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Tipe Kamar</th>
                    <th>Harga / Malam</th>
                    <th>Deskripsi</th>
                    <th>Total Kamar</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roomTypes as $type)
                <tr>
                    <td style="font-weight:500;color:var(--ink)">{{ $type->name }}</td>
                    <td><span class="badge badge-active">Rp {{ number_format($type->price, 0, ',', '.') }}</span></td>
                    <td style="color:var(--ink3);font-size:12px">{{ \Illuminate\Support\Str::limit($type->description, 45) ?? '-' }}</td>
                    <td style="color:var(--ink3)"><i class="fas fa-bed" style="margin-right:5px;font-size:11px"></i>{{ $type->rooms_count }} Unit</td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            <button class="btn btn-outline btn-sm"
                                onclick="openEditModal('{{ $type->id }}','{{ addslashes($type->name) }}','{{ $type->price }}','{{ addslashes($type->description) }}')">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form id="delete-form-{{ $type->id }}" action="{{ route('admin.room-types.destroy', $type->id) }}" method="POST" style="display:none">
                                @csrf @method('DELETE')
                            </form>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $type->id }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-tags"></i>
                            <p>Belum ada tipe kamar terdaftar.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal-overlay" id="modalAdd">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Tambah Tipe Kamar</div>
            <button class="btn-close" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.room-types.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Tipe Kamar</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Deluxe Room" required>
            </div>
            <div class="form-group">
                <label class="form-label">Harga per Malam (Rp)</label>
                <input type="number" name="price" class="form-control" placeholder="Contoh: 750000" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi Fasilitas</label>
                <textarea name="description" class="form-control" placeholder="AC, Wi-Fi, Breakfast..."></textarea>
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
            <div class="modal-title">Edit Tipe Kamar</div>
            <button class="btn-close" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditType" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Tipe Kamar</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Harga per Malam (Rp)</label>
                <input type="number" name="price" id="edit_price" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi Fasilitas</label>
                <textarea name="description" id="edit_description" class="form-control"></textarea>
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

    function openEditModal(id, name, price, description) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_description').value = description;
        document.getElementById('formEditType').action = '/admin/room-types/' + id;
        openModal('modalEdit');
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Tipe Kamar?',
            text: 'Tipe kamar yang sedang digunakan tidak dapat dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c07850',
            cancelButtonColor: '#8b7355',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            backdrop: 'rgba(44,36,32,0.5)'
        }).then(r => { if (r.isConfirmed) document.getElementById('delete-form-' + id).submit(); });
    }
</script>
@endpush