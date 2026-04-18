@extends('admin.admin')

@section('title', 'Manage User')

@section('content')
<style>
    /* CSS Tambahan Khusus untuk Modal & Form di Halaman Ini */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
        display: none; align-items: center; justify-content: center; z-index: 1000;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-content {
        background: var(--surface); border-radius: var(--radius-lg);
        width: 100%; max-width: 500px; padding: 2rem;
        box-shadow: var(--shadow-lg); transform: translateY(20px);
        transition: transform 0.3s ease; max-height: 90vh; overflow-y: auto;
    }
    .modal-overlay.show .modal-content { transform: translateY(0); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .modal-title { font-family: 'Fraunces', serif; font-size: 1.25rem; font-weight: 700; color: var(--text-dark); }
    .btn-close { background: transparent; border: none; font-size: 1.2rem; color: var(--text-light); cursor: pointer; transition: color var(--transition); }
    .btn-close:hover { color: var(--rose); }
    
    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-mid); margin-bottom: 0.4rem; }
    .form-control { 
        width: 100%; padding: 0.65rem 1rem; border: 1px solid var(--border); 
        border-radius: var(--radius-sm); font-family: inherit; font-size: 0.85rem; color: var(--text-dark); 
        transition: all var(--transition);
    }
    .form-control:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-soft); }
    select.form-control { cursor: pointer; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.25rem; }
</style>

<div style="padding: 1rem 0;">

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-circle"></i> Terjadi kesalahan input. Periksa kembali form Anda.
        </div>
    @endif

    <div class="section-header">
        <div>
            <div class="section-title">Data Pengguna</div>
            <div class="section-desc">Kelola akun administrator, staf, dan akses sistem lainnya.</div>
        </div>
        <button class="btn btn-primary" onclick="openModal('modalAdd')">
            <i class="fas fa-plus"></i> Tambah User Baru
        </button>
    </div>

    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Daftar Pengguna Sistem</div>
            <div class="table-card-actions">
                <input type="text" class="search-input" placeholder="🔍 Cari nama atau email...">
            </div>
        </div>
        
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Pengguna</th>
                        <th>Email Address</th>
                        <th>Role / Akses</th>
                        <th>Terdaftar Pada</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><strong>#USR-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.85rem;">
                                <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div><div style="font-weight: 600; color: var(--text-dark);">{{ $user->name }}</div></div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->roles->count() > 0)
                                @foreach($user->roles as $role)
                                    @php
                                        $badgeClass = 'badge-pending'; 
                                        if($role->slug == 'admin') $badgeClass = 'badge-active'; 
                                        if($role->slug == 'staff' || $role->slug == 'manager') $badgeClass = 'badge-paid'; 
                                    @endphp
                                    <span class="badge {{ $badgeClass }}" style="margin-right: 3px;">
                                        {{ strtoupper($role->name) }}
                                    </span>
                                @endforeach
                            @else
                                <span class="badge badge-cancelled">TIDAK ADA ROLE</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                <button class="btn btn-outline btn-sm" title="Edit User" 
                                    onclick="openEditModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}', '{{ $user->roles->first()->id ?? null }}')">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" class="btn btn-danger btn-sm" title="Hapus User" 
                                    onclick="confirmDelete('{{ $user->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>Belum ada data pengguna yang terdaftar di sistem.</p>
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
            <div class="modal-title">Tambah User Baru</div>
            <button class="btn-close" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" placeholder="Masukkan nama" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="email@hotelneo.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang password" required minlength="8">
            </div>
            <div class="form-group">
                <label class="form-label">Pilih Akses / Role</label>
                <select name="role_id" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ strtoupper($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAdd')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan User</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modalEdit">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Edit Data User</div>
            <button class="btn-close" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditUser" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Pilih Akses / Role</label>
                <select name="role_id" id="edit_role" class="form-control" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ strtoupper($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            
            <hr style="border: 0; border-top: 1px dashed var(--border); margin: 1.5rem 0;">
            <div class="form-label" style="color: var(--amber);">* Kosongkan password jika tidak ingin mengubahnya</div>
            
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control" placeholder="Opsional">
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Opsional">
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
    // ─── LOGIKA MODAL (POP-UP) ──────────────────────────────────
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('show');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }

    // Fungsi khusus untuk membuka modal edit dan mengisi datanya otomatis
    function openEditModal(id, name, email, role_id) {
        // Isi input field
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        
        if(role_id !== null) {
            document.getElementById('edit_role').value = role_id;
        }

        // Ubah action URL pada form menuju route update (PUT)
        document.getElementById('formEditUser').action = '/admin/users/' + id;

        // Buka modal
        openModal('modalEdit');
    }

    // ─── LOGIKA SWEETALERT UNTUK HAPUS ──────────────────────────
    function confirmDelete(userId) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data user ini akan dihapus secara permanen dari sistem!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48', // var(--rose)
            cancelButtonColor: '#475569',  // var(--text-mid)
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            backdrop: `rgba(15, 23, 42, 0.6)`
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika user klik "Ya", jalankan form delete yang disembunyikan
                document.getElementById('delete-form-' + userId).submit();
            }
        })
    }
</script>
@endpush