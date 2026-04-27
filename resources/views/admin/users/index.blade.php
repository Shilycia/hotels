@extends('admin.admin')

@section('title', 'Manage User')

@section('content')
<style>
    /* ... CSS Sama Persis ... */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(44,36,32,0.5); backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; transition: opacity 0.25s ease; }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-content { background: #fff; border-radius: var(--radius); width: 100%; max-width: 460px; padding: 24px; border: 1px solid var(--sand2); transform: translateY(16px); transition: transform 0.25s ease; max-height: 90vh; overflow-y: auto; }
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
    .modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--sand2); }
    .user-initials { width: 32px; height: 32px; border-radius: 50%; background: var(--sand2); color: var(--bark); display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; flex-shrink: 0; border: 1px solid var(--sand3); }
    .user-cell { display: flex; align-items: center; gap: 10px; }
    .user-photo { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 1px solid var(--sand3); }
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
        <div class="section-title">Data Pengguna (Staf & Admin)</div>
        <div class="section-desc">Kelola akun dan hak akses internal sistem Hotel Neo.</div>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAdd')">
        <i class="fas fa-plus"></i> Tambah User
    </button>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Daftar User</div>
        <div class="table-card-actions">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input class="search-input" id="searchInput" placeholder="Cari nama atau email...">
            </div>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th>Role (Jabatan)</th>
                    <th>Bergabung</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                @forelse($users as $user)
                <tr>
                    <td style="font-size:12px;font-weight:500;color:var(--ink)">#U-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div class="user-cell">
                            @if($user->foto)
                                {{-- Pastikan merujuk ke folder storage --}}
                                <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto {{ $user->name }}" class="user-photo">
                            @else
                                <div class="user-initials">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            @endif
                            <span style="font-weight:600;color:var(--ink)">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="color:var(--ink3)">{{ $user->email }}</td>
                    <td>
                        <span class="badge badge-info" style="font-weight: 600;">{{ ucfirst($user->role->name ?? 'Tanpa Role') }}</span>
                    </td>
                    <td style="font-size:12px;color:var(--ink3)">{{ $user->created_at->format('d M Y') }}</td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            {{-- Lempar object $user utuh --}}
                            <button class="btn btn-outline btn-sm" title="Edit User" onclick="openEditModal({{ json_encode($user) }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            
                            {{-- Proteksi UI: Jangan tampilkan tombol hapus jika ini akun sendiri --}}
                            @if(auth()->id() !== $user->id)
                                <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:none">
                                    @csrf @method('DELETE')
                                </form>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $user->id }}','{{ addslashes($user->name) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state" style="text-align: center; padding: 30px;">
                            <i class="fas fa-users" style="font-size: 32px; color: var(--sand3); margin-bottom: 10px;"></i>
                            <p>Belum ada pengguna terdaftar.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($users) && method_exists($users, 'links'))
    <div style="padding:12px 20px;border-top:1px solid var(--sand2)">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- Modal Tambah --}}
<div class="modal-overlay" id="modalAdd">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Tambah User Baru</div>
            <button class="btn-close" type="button" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group text-center">
                <i class="fas fa-user-circle" style="font-size: 40px; color: var(--sand3); margin-bottom: 10px;"></i>
            </div>
            <div class="form-group">
                <label class="form-label">Foto Profil (Opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" placeholder="Nama lengkap" required>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" class="form-control" placeholder="email@hotelneo.com" required>
            </div>
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Role (Jabatan)</label>
                <select name="role_id" class="form-control" required>
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
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
            <div class="modal-title">Edit User</div>
            <button class="btn-close" type="button" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditUser" method="POST" enctype="multipart/form-data" data-base-url="{{ url('admin/users') }}">
            @csrf @method('PUT')
            <div class="form-group text-center">
                {{-- Preview Foto Lama --}}
                <img id="edit_foto_preview" src="" alt="Preview"
                     style="width:80px;height:80px;border-radius:50%;object-fit:cover;display:none;margin:0 auto 10px auto;border:1px solid var(--sand3);">
            </div>
            <div class="form-group">
                <label class="form-label">Foto Profil <span style="color:var(--ink3);font-weight:400">(kosongkan jika tidak diubah)</span></label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Password Baru <span style="color:var(--ink3);font-weight:400">(Opsional)</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Isi jika ingin ganti">
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Role (Jabatan)</label>
                <select name="role_id" id="edit_role_id" class="form-control" required>
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
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

    function openEditModal(user) {
        document.getElementById('edit_name').value  = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role_id').value = user.role_id || '';

        const baseUrl = document.getElementById('formEditUser').dataset.baseUrl;
        document.getElementById('formEditUser').action = baseUrl + '/' + user.id;

        const preview = document.getElementById('edit_foto_preview');
        if (user.foto) {
            // Gunakan path /storage/ untuk mengambil foto yang diupload
            preview.src = '/storage/' + user.foto;
            preview.style.display = 'block';
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }

        openModal('modalEdit');
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Akses User?',
            text: 'Akun "' + name + '" akan dicabut dari sistem dan tidak dapat login lagi.',
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
        document.querySelectorAll('#userTableBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush