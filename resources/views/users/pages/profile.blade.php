@extends('users.layouts.app')

@section('title', 'Profil Saya – Hotel Neo')

@section('content')

{{-- Page Header --}}
<div class="container-fluid page-header mb-5 p-0" style="background-image: url({{ asset('img/carousel-1.jpg') }}); background-position: center; background-size: cover;">
    <div class="container-fluid page-header-inner py-5" style="background: rgba(15, 15, 17, 0.7);">
        <div class="container text-center pb-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Profil Saya</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">Profil</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid py-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-5">
            {{-- Left Sidebar: Nav Pills --}}
            <div class="col-lg-3">
                <div class="bg-white rounded shadow-sm border p-4 mb-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        {{-- Menggunakan photo_url yang sudah disesuaikan dengan database --}}
                        <img src="{{ $guest->photo_url ? asset('storage/' . $guest->photo_url) : asset('img/default-user.png') }}" 
                            class="rounded-circle border bg-light" 
                            style="width:120px; height:120px; object-fit:cover; border: 3px solid #FEA116 !important;">
                        
                        <button type="button" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 rounded-circle" 
                                data-bs-toggle="modal" data-bs-target="#editProfileModal" title="Ganti Foto Profil">
                            <i class="fa fa-camera"></i>
                        </button>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $guest->name }}</h5>
                    <p class="text-muted small mb-0">{{ $guest->email }}</p>
                </div>

                <div class="nav flex-column nav-pills shadow-sm rounded border overflow-hidden" id="v-pills-tab" role="tablist" aria-orientation="vertical" style="background:#fff">
                    <button class="nav-link active text-start px-4 py-3 rounded-0 border-bottom" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab">
                        <i class="fa fa-id-card me-2 w-20px text-center"></i> Data Pribadi
                    </button>
                    <button class="nav-link text-start px-4 py-3 rounded-0 border-bottom" id="v-pills-booking-tab" data-bs-toggle="pill" data-bs-target="#v-pills-booking" type="button" role="tab">
                        <i class="fa fa-bed me-2 w-20px text-center"></i> Riwayat Kamar
                        <span class="badge bg-primary float-end">{{ $bookings->count() }}</span>
                    </button>
                    <button class="nav-link text-start px-4 py-3 rounded-0 border-bottom" id="v-pills-restaurant-tab" data-bs-toggle="pill" data-bs-target="#v-pills-restaurant" type="button" role="tab">
                        <i class="fa fa-utensils me-2 w-20px text-center"></i> Pesanan Restoran
                        <span class="badge bg-primary float-end">{{ $restaurantOrders->count() }}</span>
                    </button>
                    {{-- Tombol Logout --}}
                    <form action="{{ route('guest.logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="nav-link text-start px-4 py-3 rounded-0 text-danger w-100 bg-white" style="border: none; text-align: left;">
                            <i class="fa fa-sign-out-alt me-2 w-20px text-center"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>

            {{-- Right Content: Tab Content --}}
            <div class="col-lg-9">
                <div class="tab-content" id="v-pills-tabContent">
                    
                    {{-- TAB 1: PROFILE INFO --}}
                    <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel">
                        <div class="bg-white rounded shadow-sm border p-4 p-md-5">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="fw-bold text-dark mb-0"><i class="fa fa-user-circle text-primary me-2"></i>Informasi Pribadi</h4>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="fa fa-pen me-1"></i> Edit Profil
                                </button>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Nama Lengkap</label>
                                    <p class="fw-bold text-dark border-bottom pb-2">{{ $guest->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Alamat Email</label>
                                    <p class="fw-bold text-dark border-bottom pb-2">{{ $guest->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Nomor Telepon</label>
                                    <p class="fw-bold text-dark border-bottom pb-2">{{ $guest->phone ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Nomor Identitas (KTP/Paspor)</label>
                                    <p class="fw-bold text-dark border-bottom pb-2">{{ $guest->identity_number ?? 'Belum diisi' }}</p>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small mb-1">Alamat Lengkap</label>
                                    <p class="fw-bold text-dark border-bottom pb-2 mb-0">{{ $guest->address ?? 'Belum diisi' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: ROOM BOOKINGS --}}
                    <div class="tab-pane fade" id="v-pills-booking" role="tabpanel">
                        <div class="bg-white rounded shadow-sm border p-4">
                            <h4 class="fw-bold mb-4 text-dark"><i class="fa fa-door-closed text-primary me-2"></i>Reservasi Kamar Saya</h4>
                            @if($bookings->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fa fa-bed text-muted mb-3" style="font-size:3rem;opacity:0.3"></i>
                                    <p class="text-muted mb-3">Anda belum memiliki riwayat pemesanan kamar.</p>
                                    <a href="{{ route('rooms.index') }}" class="btn btn-primary px-4">Pesan Kamar</a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tipe Kamar</th>
                                                <th>Check In/Out</th>
                                                <th>Total Tagihan</th>
                                                <th>Pembayaran</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bookings as $booking)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold d-block" style="font-size:14px">{{ $booking->room->roomType->name ?? 'Menunggu Alokasi' }}</span>
                                                    <span class="text-muted" style="font-size:12px">No. Kamar: {{ $booking->room->room_number ?? '-' }}</span>
                                                </td>
                                                <td>
                                                {{-- Pakai Carbon::parse untuk mengubah string jadi objek tanggal --}}
                                                <span class="d-block" style="font-size:13px">
                                                    <i class="fa fa-sign-in-alt text-primary me-1"></i> 
                                                    {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}
                                                </span>
                                                
                                                <span class="d-block" style="font-size:13px">
                                                    <i class="fa fa-sign-out-alt text-danger me-1"></i> 
                                                    {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}
                                                </span>
                                            </td>
                                                <td class="fw-bold" style="font-size:14px;color:var(--primary)">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                                                <td>
                                                    @if(optional($booking->payment)->payment_status == 'paid')
                                                        <span class="badge bg-success">Lunas</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Belum Lunas</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($booking->status == 'pending') <span class="badge bg-secondary">Menunggu</span>
                                                    @elseif($booking->status == 'confirmed') <span class="badge bg-primary">Dikonfirmasi</span>
                                                    @elseif($booking->status == 'checked_in') <span class="badge bg-info text-dark">Check-in</span>
                                                    @elseif($booking->status == 'checked_out') <span class="badge bg-success">Selesai</span>
                                                    @else <span class="badge bg-danger">Dibatalkan</span> @endif
                                                </td>
                                                <td>
                                                    @if(optional($booking->payment)->payment_status != 'paid' && $booking->status != 'cancelled')
                                                        <a href="#" class="btn btn-sm btn-primary" style="font-size:11px">Bayar</a>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-secondary" style="font-size:11px" disabled>Selesai</button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- TAB 3: RESTAURANT ORDERS --}}
                    <div class="tab-pane fade" id="v-pills-restaurant" role="tabpanel">
                        <div class="bg-white rounded shadow-sm border p-4">
                            <h4 class="fw-bold mb-4 text-dark"><i class="fa fa-concierge-bell text-primary me-2"></i>Pesanan Makanan & Minuman</h4>
                            @if($restaurantOrders->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fa fa-hamburger text-muted mb-3" style="font-size:3rem;opacity:0.3"></i>
                                    <p class="text-muted mb-3">Anda belum pernah memesan makanan dari restoran kami.</p>
                                    <a href="{{ route('menus') }}" class="btn btn-primary px-4">Pesan Sekarang</a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID Pesanan</th>
                                                <th>Menu</th>
                                                <th>Total Harga</th>
                                                <th>Status Pesanan</th>
                                                <th>Status Bayar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($restaurantOrders as $order)
                                            <tr>
                                                <td class="fw-bold text-muted" style="font-size:13px">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}<br><small>{{ $order->created_at->format('d M Y, H:i') }}</small></td>
                                                <td style="font-size:13px; max-width: 220px; line-height: 1.6;">
                                                    {{ $order->details->map(function($detail) {
                                                        return $detail->quantity . 'x ' . ($detail->menu->name ?? 'Item');
                                                    })->implode(', ') }}
                                                </td>
                                                <td class="fw-bold" style="font-size:14px;color:var(--primary)">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                                <td>
                                                    @if($order->status == 'placed') <span class="badge bg-secondary">Diterima</span>
                                                    @elseif($order->status == 'preparing') <span class="badge bg-warning text-dark">Disiapkan</span>
                                                    @elseif($order->status == 'on_the_way') <span class="badge bg-info text-dark">Diantar</span>
                                                    @elseif($order->status == 'delivered' || $order->status == 'completed') <span class="badge bg-success">Selesai</span>
                                                    @else <span class="badge bg-dark">{{ ucfirst($order->status) }}</span> @endif
                                                </td>
                                                <td>
                                                    @if(optional($order->payment)->payment_status == 'paid')
                                                        <span class="badge bg-success">Lunas</span>
                                                    @elseif(optional($order->payment)->payment_method == 'charge_to_room')
                                                        <span class="badge bg-info text-dark">Tagih ke Kamar</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Belum Lunas</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Profil --}}
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="editProfileModalLabel"><i class="fa fa-user-edit text-primary me-2"></i>Edit Profil Saya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- PERHATIKAN: enctype="multipart/form-data" sangat penting untuk upload file --}}
            <form action="{{ route('guest.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-4 text-center">
                        <label class="form-label d-block text-muted small fw-bold uppercase">Foto Profil Baru (Opsional)</label>
                        <input type="file" name="foto" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg">
                        <div class="form-text mt-1" style="font-size: 11px;">Format yang didukung: JPG, PNG. Maksimal 2MB.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold uppercase">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ $guest->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold uppercase">Alamat Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $guest->email }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold uppercase">Nomor Telepon</label>
                            <input type="tel" name="phone" class="form-control" value="{{ $guest->phone }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold uppercase">No. Identitas (KTP/Paspor)</label>
                            <input type="text" name="identity_number" class="form-control" value="{{ $guest->identity_number }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold uppercase">Alamat Lengkap</label>
                        <textarea name="address" class="form-control" rows="3">{{ $guest->address }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .nav-pills .nav-link { color: #495057; font-weight: 500; font-size: 14.5px; transition: all 0.2s ease; }
    .nav-pills .nav-link:hover { color: var(--primary); background: #f8f9fa; }
    .nav-pills .nav-link.active { background-color: var(--primary) !important; color: white !important; font-weight: 600; border-color: var(--primary) !important; }
    .nav-pills .nav-link.active .badge { background-color: white !important; color: var(--primary) !important; }
    .w-20px { width: 20px; display: inline-block; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
</style>
@endpush