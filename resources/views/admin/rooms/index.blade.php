@extends('admin.admin')
@section('title', 'Manage Room')

@section('content')
<div style="padding: 1rem 0">
    <div class="section-header">
        <div>
            <div class="section-title">Daftar Kamar</div>
            <div class="section-desc">Total {{ $rooms->count() }} kamar tersedia di sistem.</div>
        </div>
        <button class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Kamar</button>
    </div>

    <div class="table-card">
        <div class="table-card-header">
            <input class="search-input" placeholder="🔍 Cari kamar...">
        </div>
        <table>
            <thead>
                <tr>
                    <th>No. Kamar</th>
                    <th>Tipe</th>
                    <th>Harga/Malam</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                <tr>
                    <td><strong>{{ $room->room_number }}</strong></td>
                    <td>{{ $room->type }}</td>
                    <td>Rp {{ number_format($room->price, 0, ',', '.') }}</td>
                    <td>
                        @if($room->status == 'available')
                            <span class="badge badge-paid">Tersedia</span>
                        @else
                            <span class="badge badge-occupied">Terisi</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-outline btn-sm"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection