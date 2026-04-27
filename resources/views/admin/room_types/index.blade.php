@extends('admin.admin')

@section('title', 'Manage Tipe Kamar')

@section('content')
<style>
    /* ... CSS sama persis ... */
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
    textarea.form-control { resize: vertical; min-height: 75px; }
    select.form-control { cursor: pointer; }
    .form-control:focus { border-color: var(--bark); background: #fff; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--sand2); }
    .capacity-badge { font-size: 11px; background: var(--sand); padding: 4px 8px; border-radius: 4px; border: 1px solid var(--sand3); white-space: nowrap; font-weight: 500;}
    
    /* Tambahan untuk Thumbnail Gambar */
    .room-cell { display: flex; align-items: center; gap: 12px; }
    .room-thumb { width: 45px; height: 45px; border-radius: 8px; background: var(--sand2); overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .room-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .room-thumb i { color: var(--ink3); font-size: 18px; }
</style>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error" style="margin-bottom:16px"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-error" style="margin-bottom:16px">
        <div style="font-weight:bold;margin-bottom:5px"><i class="fas fa-exclamation-triangle"></i> Gagal menyimpan data:</div>
        <ul style="margin:0;padding-left:20px;font-size:13px">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="section-header">
    <div>
        <div class="section-title">Kategori Tipe Kamar</div>
        <div class="section-desc">Kelola harga, fasilitas, dan kapasitas untuk setiap tipe kamar di Hotel Neo.</div>
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
                    <th>Fasilitas Dasar</th>
                    <th>Kapasitas</th> 
                    <th style="text-align:center">Total Kamar</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roomTypes as $type)
                <tr>
                    <td>
                        <div class="room-cell">
                            <div class="room-thumb">
                                @if($type->foto)
                                    <img src="{{ asset('storage/' . $type->foto) }}" alt="{{ $type->name }}">
                                @else
                                    <i class="fas fa-bed"></i>
                                @endif
                            </div>
                            <div>
                                <strong style="color:var(--ink); display:block;">{{ $type->name }}</strong>
                                <span style="font-size:11px; color:var(--ink3)">{{ \Illuminate\Support\Str::limit($type->description, 35) ?: 'Tidak ada deskripsi' }}</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-active" style="font-weight: 600;">Rp {{ number_format($type->price, 0, ',', '.') }}</span></td>
                    <td style="color:var(--ink3); font-size:12px;">
                        <i class="fas fa-bed"></i> {{ $type->bed_type ?? '-' }} <br>
                        <i class="fas fa-bath"></i> {{ $type->bath_count ?? 0 }} Kamar Mandi
                    </td>
                    
                    <td>
                        <div style="display:flex; gap:4px; flex-direction:column;">
                            <span class="capacity-badge"><i class="fas fa-user" style="color:#00A5CF; margin-right:4px"></i>{{ $type->adult_capacity ?? 0 }} Dewasa</span>
                            <span class="capacity-badge"><i class="fas fa-child" style="color:#c07850; margin-right:4px"></i>{{ $type->child_capacity ?? 0 }} Anak</span>
                        </div>
                    </td>

                    <td style="color:var(--ink3);text-align:center;font-weight:500;">
                        <span style="background:var(--sand2); padding: 4px 10px; border-radius: 12px; color: var(--ink);">
                            <i class="fas fa-door-closed" style="margin-right:4px; font-size:11px"></i> {{ $type->rooms_count ?? 0 }} Unit
                        </span>
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            {{-- Gunakan JSON Encode agar aman dari error kutip --}}
                            <button class="btn btn-outline btn-sm" onclick="openEditModal({{ json_encode($type) }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form id="delete-form-{{ $type->id }}" action="{{ route('admin.room-types.destroy', $type->id) }}" method="POST" style="display:none">
                                @csrf @method('DELETE')
                            </form>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $type->id }}', '{{ addslashes($type->name) }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state" style="text-align: center; padding: 30px;">
                            <i class="fas fa-tags" style="font-size: 32px; color: var(--sand3); margin-bottom: 10px;"></i>
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
            <button class="btn-close" type="button" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.room-types.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Tipe Kamar</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Deluxe Room" required>
            </div>
            <div class="form-group">
                <label class="form-label">Harga per Malam (Rp)</label>
                <input type="number" name="price" class="form-control" placeholder="Contoh: 750000" min="0" required>
            </div>
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Kapasitas Dewasa</label>
                    <input type="number" name="adult_capacity" class="form-control" value="2" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kapasitas Anak</label>
                    <input type="number" name="child_capacity" class="form-control" value="0" min="0" required>
                </div>
            </div>
            <div class="form-row" style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Tipe Kasur</label>
                    <input type="text" name="bed_type" class="form-control" placeholder="Contoh: King, Queen, Twin" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kamar Mandi</label>
                    <input type="number" name="bath_count" class="form-control" value="1" min="1" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi Fasilitas</label>
                <textarea name="description" class="form-control" placeholder="AC, Wi-Fi, Breakfast..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Foto Kamar (Opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
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
            <button class="btn-close" type="button" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditType" method="POST" enctype="multipart/form-data" data-base-url="{{ url('admin/room-types') }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Tipe Kamar</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Harga per Malam (Rp)</label>
                <input type="number" name="price" id="edit_price" class="form-control" min="0" required>
            </div>
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Kapasitas Dewasa</label>
                    <input type="number" name="adult_capacity" id="edit_adult_capacity" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kapasitas Anak</label>
                    <input type="number" name="child_capacity" id="edit_child_capacity" class="form-control" min="0" required>
                </div>
            </div>
            <div class="form-row" style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Tipe Kasur</label>
                    <input type="text" name="bed_type" id="edit_bed_type" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kamar Mandi</label>
                    <input type="number" name="bath_count" id="edit_bath_count" class="form-control" min="1" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi Fasilitas</label>
                <textarea name="description" id="edit_description" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Foto Kamar <span style="color:var(--ink3);font-weight:400">(kosongkan jika tidak diubah)</span></label>
                <img id="edit_image_preview" src="" alt="Preview"
                     style="display:none;width:100%;max-height:150px;object-fit:cover;border-radius:var(--radius-sm);margin-bottom:8px;border:1px solid var(--sand3);">
                <input type="file" name="foto" class="form-control" accept="image/*">
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

    function openEditModal(roomType) {
        document.getElementById('edit_name').value           = roomType.name;
        document.getElementById('edit_price').value          = roomType.price;
        document.getElementById('edit_adult_capacity').value = roomType.adult_capacity || 0;
        document.getElementById('edit_child_capacity').value = roomType.child_capacity || 0;
        document.getElementById('edit_bed_type').value       = roomType.bed_type || '';
        document.getElementById('edit_bath_count').value     = roomType.bath_count || 0;
        document.getElementById('edit_description').value    = roomType.description || '';

        const baseUrl = document.getElementById('formEditType').dataset.baseUrl;
        document.getElementById('formEditType').action = baseUrl + '/' + roomType.id;

        const preview = document.getElementById('edit_image_preview');
        if (roomType.foto) {
            // Gunakan path /storage/ agar sinkron dengan asset Laravel
            preview.src = '/storage/' + roomType.foto;
            preview.style.display = 'block';
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }

        openModal('modalEdit');
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Tipe Kamar?',
            text: '"' + name + '" akan dihapus. Kamar fisik yang berelasi dengan tipe ini juga bisa terhapus.',
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