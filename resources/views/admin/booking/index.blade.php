@extends('admin.admin')

@section('title', 'Manage Booking')

@section('content')
<style>
    /* ... (Gaya CSS tetap sama seperti milikmu) ... */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(44,36,32,0.5); backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; transition: opacity 0.25s ease; }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-content { background: #fff; border-radius: 8px; width: 100%; max-width: 500px; padding: 24px; border: 1px solid var(--sand2); transform: translateY(16px); transition: transform 0.25s ease; max-height: 90vh; overflow-y: auto; }
    .modal-overlay.show .modal-content { transform: translateY(0); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-title { font-family: 'Lora', serif; font-size: 16px; color: var(--ink); font-weight: 600; }
    .btn-close { background: transparent; border: none; color: var(--ink3); cursor: pointer; font-size: 14px; padding: 4px; transition: color 0.3s ease; }
    .btn-close:hover { color: var(--clay); }
    .form-group { margin-bottom: 14px; }
    .form-label { display: block; font-size: 11px; font-weight: 500; color: var(--ink2); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.4px; }
    .form-control { width: 100%; padding: 8px 12px; border: 1px solid var(--sand3); border-radius: 4px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--ink); background: var(--sand); outline: none; transition: border-color 0.3s ease; }
    select.form-control { cursor: pointer; }
    .form-control:focus { border-color: var(--bark); background: #fff; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
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
        <div class="section-title">Data Booking</div>
        <div class="section-desc">Kelola seluruh reservasi kamar tamu Hotel Neo.</div>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAdd')">
        <i class="fas fa-plus"></i> Tambah Booking
    </button>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Daftar Booking</div>
        <div class="table-card-actions">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input class="search-input" id="searchInput" placeholder="Cari tamu atau kamar...">
            </div>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Tamu</th>
                    <th>Kamar</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="bookingTableBody">
                @forelse($bookings as $booking)
                <tr>
                    <td style="font-size:12px;font-weight:500;color:var(--ink)">#B-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td style="font-weight:500;color:var(--ink)">{{ $booking->guest->name ?? 'Tamu Tidak Diketahui' }}</td>
                    <td>{{ $booking->room->room_number ?? '-' }}</td>
                    <td style="font-size:12px;color:var(--ink3)">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</td>
                    <td style="font-size:12px;color:var(--ink3)">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</td>
                    <td>
                        @php $bs = $booking->status ?? 'pending'; @endphp
                        <span class="badge {{ in_array($bs, ['confirmed', 'checked_in']) ? 'badge-active' : ($bs === 'cancelled' ? 'badge-cancelled' : 'badge-pending') }}">
                            {{ ucfirst(str_replace('_', ' ', $bs)) }}
                        </span>
                    </td>
                    <td style="font-weight:500;color:var(--ink)">Rp {{ number_format($booking->total_amount ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            <button class="btn btn-outline btn-sm" onclick="openEditModal({{ json_encode($booking) }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form id="delete-form-{{ $booking->id }}" action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" style="display:none">
                                @csrf @method('DELETE')
                            </form>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $booking->id }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state" style="text-align: center; padding: 30px;">
                            <i class="fas fa-calendar-times" style="font-size: 32px; color: var(--sand3); margin-bottom: 10px;"></i>
                            <p>Belum ada data booking.</p>
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
            <div class="modal-title">Tambah Booking</div>
            <button class="btn-close" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.bookings.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Tamu</label>
                <select name="guest_id" class="form-control" required>
                    <option value="">-- Pilih Tamu --</option>
                    @foreach($guests as $guest)
                        <option value="{{ $guest->id }}">{{ $guest->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Kamar</label>
                <select name="room_id" class="form-control" required>
                    <option value="">-- Pilih Kamar --</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->room_number }} – {{ $room->roomType->name ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Check-in</label>
                    <input type="date" name="check_in_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Check-out</label>
                    <input type="date" name="check_out_date" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status Booking</label>
                <select name="status" class="form-control" required>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="checked_in">Checked In</option>
                    <option value="checked_out">Checked Out</option>
                    <option value="cancelled">Cancelled</option>
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
            <div class="modal-title">Edit Booking</div>
            <button class="btn-close" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditBooking" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Tamu</label>
                <select name="guest_id" id="edit_guest_id" class="form-control" required>
                    <option value="">-- Pilih Tamu --</option>
                    @foreach($guests as $guest)
                        <option value="{{ $guest->id }}">{{ $guest->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Kamar</label>
                <select name="room_id" id="edit_room_id" class="form-control" required>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->room_number }} – {{ $room->roomType->name ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Check-in</label>
                    <input type="date" name="check_in_date" id="edit_check_in_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Check-out</label>
                    <input type="date" name="check_out_date" id="edit_check_out_date" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status Booking</label>
                <select name="status" id="edit_status" class="form-control" required>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="checked_in">Checked In</option>
                    <option value="checked_out">Checked Out</option>
                    <option value="cancelled">Cancelled</option>
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

    function openEditModal(booking) {
        document.getElementById('edit_guest_id').value        = booking.guest_id;
        document.getElementById('edit_room_id').value         = booking.room_id;
        document.getElementById('edit_check_in_date').value   = booking.check_in_date ? booking.check_in_date.substring(0,10) : '';
        document.getElementById('edit_check_out_date').value  = booking.check_out_date ? booking.check_out_date.substring(0,10) : '';
        document.getElementById('edit_status').value          = booking.status ?? 'pending';
        
        document.getElementById('formEditBooking').action     = '/admin/bookings/' + booking.id;
        openModal('modalEdit');
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Booking?',
            text: 'Data booking ini akan dihapus permanen.',
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
        document.querySelectorAll('#bookingTableBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush