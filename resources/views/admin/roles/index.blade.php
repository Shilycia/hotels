@extends('admin.admin')

@section('title', 'Manage Role')

@section('content')
<style>
    /* Menggunakan style modal yang sama persis dengan halaman user */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; transition: opacity 0.3s ease; }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-content { background: var(--surface); border-radius: var(--radius-lg); width: 100%; max-width: 450px; padding: 2rem; box-shadow: var(--shadow-lg); transform: translateY(20px); transition: transform 0.3s ease; }
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
            <div class="section-title">Data Role Akses</div>
            <div class="section-desc">Kelola jenis peran dan hak akses pengguna di sistem Hotel Neo.</div>
        </div>
        <button class="btn btn-primary" onclick="openModal('modalAdd')"><i class="fas fa-plus"></i> Tambah Role Baru</button>
    </div>

    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Daftar Role</div>
        </div>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Nama Role</th>
                        <th>Slug Sistem</th>
                        <th>Jumlah User</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td><strong>#R-{{ str_pad($role->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-dark);">{{ strtoupper($role->name) }}</div>
                        </td>
                        <td><span class="badge badge-pending">{{ $role->slug }}</span></td>
                        <td>
                            <i class="fas fa-users text-light" style="margin-right: 5px;"></i> 
                            {{ $role->users_count }} Pengguna
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                <button class="btn btn-outline btn-sm" title="Edit Role" 
                                    onclick="openEditModal('{{ $role->id }}', '{{ $role->name }}', '{{ $role->slug }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <form id="delete-form-{{ $role->id }}" action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" class="btn btn-danger btn-sm" title="Hapus Role" onclick="confirmDelete('{{ $role->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5"><div class="empty-state"><i class="fas fa-user-shield"></i><p>Belum ada role terdaftar.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalAdd">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Tambah Role Baru</div>
            <button class="btn-close" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Role</label>
                <input type="text" name="name" id="add_name" class="form-control" placeholder="Contoh: Resepsionis" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Slug Sistem</label>
                <input type="text" name="slug" id="add_slug" class="form-control" placeholder="contoh: resepsionis" required>
                <small style="color: var(--text-light); font-size: 0.7rem;">Gunakan huruf kecil dan strip (-). Contoh: super-admin</small>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAdd')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modalEdit">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Edit Role</div>
            <button class="btn-close" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditRole" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Role</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Slug Sistem</label>
                <input type="text" name="slug" id="edit_slug" class="form-control" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Buka Tutup Modal
    function openModal(modalId) { document.getElementById(modalId).classList.add('show'); }
    function closeModal(modalId) { document.getElementById(modalId).classList.remove('show'); }

    // Tambahkan parameter slug di fungsi ini
    function openEditModal(id, name, slug) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_slug').value = slug; // Isi slug secara dinamis
        document.getElementById('formEditRole').action = '/admin/roles/' + id;
        openModal('modalEdit');
    }

    // Fungsi Hapus
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Role?',
            text: "Role yang sedang digunakan oleh user tidak akan bisa dihapus.",
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

    // ─── FITUR AUTO-GENERATE SLUG ───────────────────────────────
    // Konversi teks biasa menjadi slug (huruf kecil, ganti spasi jadi strip)
    function generateSlug(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Ganti spasi dengan -
            .replace(/[^\w\-]+/g, '')       // Hapus karakter non-word
            .replace(/\-\-+/g, '-')         // Ganti multiple - dengan single -
            .replace(/^-+/, '')             // Hapus - di awal
            .replace(/-+$/, '');            // Hapus - di akhir
    }

    // Auto-fill saat menambah role
    document.getElementById('add_name').addEventListener('input', function() {
        document.getElementById('add_slug').value = generateSlug(this.value);
    });

    // Auto-fill saat mengedit role
    document.getElementById('edit_name').addEventListener('input', function() {
        document.getElementById('edit_slug').value = generateSlug(this.value);
    });
</script>
@endpush