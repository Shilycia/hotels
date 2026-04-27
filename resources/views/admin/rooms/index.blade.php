@extends('admin.admin')

@section('title', 'Manage Room')

@section('content')
<style>
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
    
    /* Warna Status Kamar */
    .status-available   { background: #d1e7dd; color: #0f5132; }
    .status-occupied    { background: #f8d7da; color: #842029; }
    .status-cleaning    { background: #cff4fc; color: #055160; } /* Warna Biru Muda */
    .status-maintenance { background: #fff3cd; color: #856404; }
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
        <div class="section-title">Data Kamar Fisik</div>
        <div class="section-desc">Kelola seluruh unit kamar beserta status *real-time* di Hotel Neo.</div>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAdd')">
        <i class="fas fa-plus"></i> Tambah Kamar
    </button>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Daftar Kamar</div>
        <div class="table-card-actions">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input class="search-input" id="searchInput" placeholder="Cari nomor kamar...">
            </div>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>No. Kamar</th>
                    <th>Tipe Kamar</th>
                    <th>Lantai</th>
                    <th>Status Kamar</th>
                    <th>Harga / Malam</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="roomTableBody">
                @forelse($rooms as $room)
                <tr>
                    <td style="font-weight:600;color:var(--ink)">
                        <i class="fas fa-door-closed" style="color:var(--sand3); margin-right:5px;"></i> {{ $room->room_number }}
                    </td>
                    <td>{{ $room->roomType->name ?? '-' }}</td>
                    <td style="color:var(--ink3)">Lantai {{ $room->floor ?? '-' }}</td>
                    <td>
                        @php $s = $room->status ?? 'available'; @endphp
                        <span class="badge status-{{ $s }}" style="font-weight: 600; padding: 4px 8px; border-radius: 4px;">
                            @if($s === 'available') Tersedia
                            @elseif($s === 'occupied') Terisi
                            @elseif($s === 'cleaning') Dibersihkan
                            @else Maintenance
                            @endif
                        </span>
                    </td>
                    <td style="font-weight:500;color:var(--clay)">Rp {{ number_format($room->roomType->price ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            {{-- Menggunakan JSON Encode --}}
                            <button class="btn btn-outline btn-sm" onclick="openEditModal({{ json_encode($room) }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form id="delete-form-{{ $room->id }}" action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST" style="display:none">
                                @csrf @method('DELETE')
                            </form>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $room->id }}', '{{ $room->room_number }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state" style="text-align: center; padding: 30px;">
                            <i class="fas fa-bed" style="font-size: 32px; color: var(--sand3); margin-bottom: 10px;"></i>
                            <p>Belum ada data kamar fisik.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(isset($rooms) && method_exists($rooms, 'links'))
    <div style="padding:12px 20px;border-top:1px solid var(--sand2)">
        {{ $rooms->links() }}
    </div>
    @endif
</div>

{{-- Modal Tambah --}}
<div class="modal-overlay" id="modalAdd">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Tambah Kamar</div>
            <button class="btn-close" type="button" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.rooms.store') }}" method="POST">
            @csrf
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Nomor Kamar</label>
                    <input type="text" name="room_number" class="form-control" placeholder="Contoh: 301" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Lantai</label>
                    <input type="number" name="floor" class="form-control" placeholder="Contoh: 3" min="1" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Tipe Kamar</label>
                <select name="room_type_id" class="form-control" required>
                    <option value="">-- Pilih Tipe --</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} – Rp {{ number_format($type->price, 0, ',', '.') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status Fisik Kamar</label>
                <select name="status" class="form-control" required>
                    <option value="available">Tersedia (Ready)</option>
                    <option value="occupied">Terisi (Tamu Check-in)</option>
                    <option value="cleaning">Dibersihkan (Housekeeping)</option>
                    <option value="maintenance">Perbaikan (Maintenance)</option>
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
            <div class="modal-title">Edit Kamar</div>
            <button class="btn-close" type="button" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditRoom" method="POST" data-base-url="{{ url('admin/rooms') }}">
            @csrf @method('PUT')
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Nomor Kamar</label>
                    <input type="text" name="room_number" id="edit_room_number" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Lantai</label>
                    <input type="number" name="floor" id="edit_floor" class="form-control" min="1" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Tipe Kamar</label>
                <select name="room_type_id" id="edit_room_type_id" class="form-control" required>
                    <option value="">-- Pilih Tipe --</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} – Rp {{ number_format($type->price, 0, ',', '.') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status Fisik Kamar</label>
                <select name="status" id="edit_status" class="form-control" required>
                    <option value="available">Tersedia (Ready)</option>
                    <option value="occupied">Terisi (Tamu Check-in)</option>
                    <option value="cleaning">Dibersihkan (Housekeeping)</option>
                    <option value="maintenance">Perbaikan (Maintenance)</option>
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

    function openEditModal(room) {
        document.getElementById('edit_room_number').value  = room.room_number;
        document.getElementById('edit_room_type_id').value = room.room_type_id;
        document.getElementById('edit_floor').value        = room.floor || '';
        document.getElementById('edit_status').value       = room.status || 'available';

        const baseUrl = document.getElementById('formEditRoom').dataset.baseUrl;
        document.getElementById('formEditRoom').action = baseUrl + '/' + room.id;

        openModal('modalEdit');
    }

    function confirmDelete(id, roomNumber) {
        Swal.fire({
            title: 'Hapus Kamar?',
            text: 'Kamar ' + roomNumber + ' akan dihapus dari sistem. Pastikan tidak ada tamu yang sedang menempati kamar ini.',
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
        document.querySelectorAll('#roomTableBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush