@extends('users.layouts.app')

@section('title', 'My Profile – ' . config('hotel.name', 'Hotelier'))

@section('content')

{{-- Page Header --}}
@include('users.components.page-header', [
    'title'      => 'My Profile',
    'breadcrumb' => 'Profile',
])

<div class="container-fluid py-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
        @endif

        <div class="row g-5">
            {{-- Left Sidebar: Nav Pills --}}
            <div class="col-lg-3">
                <div class="bg-white rounded shadow-sm border p-4 mb-4 text-center">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width:100px;height:100px;border:2px solid #FEA116">
                        <i class="fa fa-user text-primary" style="font-size:40px"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $guest->name }}</h5>
                    <p class="text-muted small mb-0">{{ $guest->email }}</p>
                </div>

                <div class="nav flex-column nav-pills shadow-sm rounded border overflow-hidden" id="v-pills-tab" role="tablist" aria-orientation="vertical" style="background:#fff">
                    <button class="nav-link active text-start px-4 py-3 rounded-0 border-bottom" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab">
                        <i class="fa fa-id-card me-2 w-20px text-center"></i> Personal Info
                    </button>
                    <button class="nav-link text-start px-4 py-3 rounded-0 border-bottom" id="v-pills-booking-tab" data-bs-toggle="pill" data-bs-target="#v-pills-booking" type="button" role="tab">
                        <i class="fa fa-bed me-2 w-20px text-center"></i> Room Bookings
                        <span class="badge bg-primary float-end">{{ $bookings->count() }}</span>
                    </button>
                    <button class="nav-link text-start px-4 py-3 rounded-0 border-bottom" id="v-pills-restaurant-tab" data-bs-toggle="pill" data-bs-target="#v-pills-restaurant" type="button" role="tab">
                        <i class="fa fa-utensils me-2 w-20px text-center"></i> F&B Orders
                        <span class="badge bg-primary float-end">{{ $restaurantOrders->count() }}</span>
                    </button>
                    {{-- Tombol Logout --}}
                    <form action="{{ route('guest.logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="nav-link text-start px-4 py-3 rounded-0 text-danger w-100 bg-white">
                            <i class="fa fa-sign-out-alt me-2 w-20px text-center"></i> Logout
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
                            <h4 class="fw-bold mb-4 text-dark"><i class="fa fa-user-circle text-primary me-2"></i>Personal Information</h4>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Full Name</label>
                                    <p class="fw-bold text-dark border-bottom pb-2">{{ $guest->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Email Address</label>
                                    <p class="fw-bold text-dark border-bottom pb-2">{{ $guest->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Phone Number</label>
                                    <p class="fw-bold text-dark border-bottom pb-2">{{ $guest->phone ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">Identity Number (KTP/Passport)</label>
                                    <p class="fw-bold text-dark border-bottom pb-2">{{ $guest->identity_number ?? '-' }}</p>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small mb-1">Address</label>
                                    <p class="fw-bold text-dark border-bottom pb-2 mb-0">{{ $guest->address ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: ROOM BOOKINGS --}}
                    <div class="tab-pane fade" id="v-pills-booking" role="tabpanel">
                        <div class="bg-white rounded shadow-sm border p-4">
                            <h4 class="fw-bold mb-4 text-dark"><i class="fa fa-door-closed text-primary me-2"></i>My Bookings</h4>
                            @if($bookings->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fa fa-bed text-muted mb-3" style="font-size:3rem;opacity:0.3"></i>
                                    <p class="text-muted mb-3">You haven't booked any rooms yet.</p>
                                    <a href="{{ route('rooms') }}" class="btn btn-primary px-4">Book a Room</a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Room</th>
                                                <th>Check In/Out</th>
                                                <th>Total Price</th>
                                                <th>Payment</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bookings as $booking)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold d-block" style="font-size:14px">{{ $booking->room->roomType->name ?? 'Room' }}</span>
                                                    <span class="text-muted" style="font-size:12px">Room {{ $booking->room->room_number ?? '-' }}</span>
                                                </td>
                                                <td>
                                                    <span class="d-block" style="font-size:13px"><i class="fa fa-sign-in-alt text-primary me-1"></i> {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}</span>
                                                    <span class="d-block" style="font-size:13px"><i class="fa fa-sign-out-alt text-danger me-1"></i> {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}</span>
                                                </td>
                                                <td class="fw-bold" style="font-size:14px;color:#f39c12">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                                <td>
                                                    @if(optional($booking->payment)->payment_status == 'paid')
                                                        <span class="badge bg-success">Paid</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($booking->status == 'pending') <span class="badge bg-secondary">Pending</span>
                                                    @elseif($booking->status == 'confirmed') <span class="badge bg-primary">Confirmed</span>
                                                    @elseif($booking->status == 'checked_in') <span class="badge bg-info text-dark">Checked In</span>
                                                    @elseif($booking->status == 'checked_out') <span class="badge bg-success">Checked Out</span>
                                                    @else <span class="badge bg-danger">Cancelled</span> @endif
                                                </td>
                                                <td>
                                                    @if(optional($booking->payment)->payment_status != 'paid')
                                                        <a href="{{ route('guest.pay', $booking->payment->id ?? 0) }}" class="btn btn-sm btn-primary" style="font-size:11px">Pay Now</a>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-secondary" style="font-size:11px" disabled>Done</button>
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
                            <h4 class="fw-bold mb-4 text-dark"><i class="fa fa-concierge-bell text-primary me-2"></i>My F&B Orders</h4>
                            @if($restaurantOrders->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fa fa-hamburger text-muted mb-3" style="font-size:3rem;opacity:0.3"></i>
                                    <p class="text-muted mb-3">You haven't ordered any food or drinks yet.</p>
                                    <a href="{{ route('menus') }}" class="btn btn-primary px-4">Order Now</a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Items</th>
                                                <th>Total Price</th>
                                                <th>Status</th>
                                                <th>Payment</th>
                                                <th>Receipt</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($restaurantOrders as $order)
                                            <tr>
                                                <td class="fw-bold text-muted" style="font-size:13px">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}<br><small>{{ $order->created_at->format('d M Y, H:i') }}</small></td>
                                                <td style="font-size:13px">
                                                    @foreach($order->details as $detail)
                                                        <div class="mb-1">· {{ $detail->quantity }}x {{ $detail->menu->name ?? 'Item' }}</div>
                                                    @endforeach
                                                </td>
                                                <td class="fw-bold" style="font-size:14px;color:#f39c12">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                                <td>
                                                    @if($order->status == 'placed') <span class="badge bg-secondary">Placed</span>
                                                    @elseif($order->status == 'preparing') <span class="badge bg-warning text-dark">Preparing</span>
                                                    @elseif($order->status == 'on_the_way') <span class="badge bg-info text-dark">On The Way</span>
                                                    @elseif($order->status == 'delivered') <span class="badge bg-success">Delivered</span>
                                                    @elseif($order->status == 'paid') <span class="badge bg-success">Paid</span>
                                                    @else <span class="badge bg-dark">{{ ucfirst($order->status) }}</span> @endif
                                                </td>
                                                <td>
                                                    @if(optional($order->payment)->payment_status == 'paid')
                                                        <span class="badge bg-success">Paid</span>
                                                    @elseif(optional($order->payment)->payment_method == 'charge_to_room')
                                                        <span class="badge bg-info text-dark">Billed to Room</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('orders.confirmation', $order->id) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px"><i class="fa fa-eye me-1"></i> View</a>
                                                    @if(optional($order->payment)->payment_status != 'paid' && optional($order->payment)->payment_method != 'charge_to_room')
                                                        <a href="{{ route('guest.pay', $order->payment->id ?? 0) }}" class="btn btn-sm btn-primary ms-1" style="font-size:11px">Pay</a>
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

@endsection

@push('styles')
<style>
    .nav-pills .nav-link { color: #495057; font-weight: 500; font-size: 14.5px; transition: all 0.2s ease; }
    .nav-pills .nav-link:hover { color: var(--primary); background: #f8f9fa; }
    .nav-pills .nav-link.active { background-color: var(--primary) !important; color: white !important; font-weight: 600; border-color: var(--primary) !important; }
    .nav-pills .nav-link.active .badge { background-color: white !important; color: var(--primary) !important; }
    .w-20px { width: 20px; display: inline-block; }
</style>
@endpush