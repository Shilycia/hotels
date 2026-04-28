@extends('users.layouts.app')

@section('title', 'Restaurant Menu – ' . config('hotel.name', 'Hotelier'))

@section('content')

@php
    $loggedInGuest = null;
    if(session('guest_id')) {
        $loggedInGuest = \App\Models\Guest::find(session('guest_id'));
    }
@endphp

{{-- Page Header --}}
@include('users.components.page-header', [
    'title'      => 'Restaurant Menu',
    'breadcrumb' => 'Menu',
])

{{-- Menu Section --}}
<div class="container-fluid py-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container">

        {{-- Section Heading --}}
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Our Restaurant</h6>
            <h1>Explore Our <span class="text-primary text-uppercase">Delicious</span> Menu</h1>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 wow fadeInUp" role="alert">
                <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mb-4 wow fadeInUp">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Filter Bar --}}
        <div class="d-flex align-items-center flex-wrap gap-2 mb-4 wow fadeInUp" data-wow-delay="0.2s">
            {{-- Category Filter Pills --}}
            <a href="{{ route('menus') }}"
               class="btn rounded-pill px-4 py-2 {{ !request('category') ? 'btn-primary' : 'btn-outline-secondary' }}"
               style="font-size:13px">
                All
            </a>
            @foreach(['food', 'drink', 'dessert', 'snack'] as $cat)
            <a href="{{ route('menus', ['category' => $cat]) }}"
               class="btn rounded-pill px-4 py-2 {{ request('category') === $cat ? 'btn-primary' : 'btn-outline-secondary' }}"
               style="font-size:13px; text-transform: capitalize;">
                {{ $cat }}
            </a>
            @endforeach

            {{-- Search --}}
            <div class="ms-auto">
                <form method="GET" action="{{ route('menus') }}" class="d-flex align-items-center">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="input-group" style="width:220px">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fa fa-search text-primary" style="font-size:12px"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="Search menu..."
                            value="{{ request('search') }}"
                            style="font-size:13px">
                    </div>
                </form>
            </div>
        </div>

        {{-- No Results --}}
        @if(isset($menus) && $menus->isEmpty())
        <div class="text-center py-5 wow fadeIn">
            <i class="fa fa-utensils text-muted mb-3" style="font-size:3rem;opacity:.3"></i>
            <h5 class="text-muted">No menu items found.</h5>
            <p class="text-muted small">Try a different category or search term.</p>
            <a href="{{ route('menus') }}" class="btn btn-primary mt-2">View All Menu</a>
        </div>
        @else

        {{-- Dynamic: render from DB grouped by category --}}
        @php
            $grouped = $menus->groupBy('category');
        @endphp

        @foreach($grouped as $category => $items)
        <div class="mb-5 wow fadeInUp" data-wow-delay="0.1s">

            {{-- Category Divider --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <h5 class="mb-0 fw-bold text-dark" style="white-space:nowrap; text-transform: capitalize;">
                    @switch($category)
                        @case('food')     <i class="fa fa-utensils text-primary me-2"></i>  @break
                        @case('drink')    <i class="fa fa-glass-cheers text-primary me-2"></i> @break
                        @case('dessert')  <i class="fa fa-ice-cream text-primary me-2"></i> @break
                        @case('snack')    <i class="fa fa-cookie-bite text-primary me-2"></i> @break
                        @default          <i class="fa fa-concierge-bell text-primary me-2"></i>
                    @endswitch
                    {{ $category }}
                </h5>
                <hr class="flex-grow-1 m-0" style="border-color:#dee2e6">
                <span class="badge bg-light text-muted border" style="font-size:11px">
                    {{ $items->count() }} items
                </span>
            </div>

            {{-- Menu Grid --}}
            <div class="row g-4">
                @foreach($items as $menu)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border shadow-sm menu-card position-relative" style="border-radius:10px;overflow:hidden;transition:transform .2s">

                        <a href="{{ route('menu.detail', $menu->id) }}" class="text-decoration-none d-block">

                            {{-- Image --}}
                            <div class="position-relative" style="height:180px;overflow:hidden;background:#f1f3f5">
                                @if(!empty($menu->foto_url))
                                    {{-- TAMBAHKAN 'storage/' DI SINI AGAR GAMBAR MUNCUL --}}
                                    <img src="{{ asset('storage/' . $menu->foto_url) }}" alt="{{ $menu->name }}"
                                        class="w-100 h-100" style="object-fit:cover">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                        <i class="fa fa-utensils" style="font-size:2.5rem;color:#dee2e6"></i>
                                    </div>
                                @endif

                                {{-- Availability Badge --}}
                                @if(!($menu->is_available ?? true))
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-danger" style="font-size:10px;padding:4px 10px;border-radius:50px">Sold Out</span>
                                </div>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-1" style="color:#1a1f2e;font-size:14px">{{ $menu->name }}</h6>
                                <p class="text-muted mb-3" style="font-size:12.5px;line-height:1.55; display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                                    {{ $menu->description ?? 'Delicious menu item prepared fresh by our chef.' }}
                                </p>
                                @if($menu->category == 'paket' && $menu->paketItems->count() > 0)
                                    <div class="mb-3">
                                        <span class="d-inline-block mb-1" style="font-size: 10px; font-weight: 700; color: #856404; background: #fff3cd; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            <i class="fa fa-box-open me-1"></i> Isi Paket:
                                        </span>
                                        <div class="text-muted fw-semibold" style="font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $menu->paketItems->pluck('name')->join(' + ') }}
                                        </div>
                                    </div>
                                @endif
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fw-bold text-primary" style="font-size:16px">
                                        Rp {{ number_format($menu->price ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        
                        </a> 

                        {{-- Footer / Order Button --}}
                        <div class="card-footer bg-white border-top-0 p-3 pt-0 position-relative" style="z-index: 2;">
                            @if(!($menu->is_available ?? true))
                                {{-- Jika makanan habis --}}
                                <button class="btn btn-outline-secondary w-100 py-2 disabled"
                                    style="font-size:13px;border-radius:8px" disabled>
                                    Not Available
                                </button>
                            @elseif(!session('guest_id'))
                                {{-- Jika tamu belum login --}}
                                <a href="{{ route('guest.login') }}" class="btn btn-outline-primary w-100 py-2"
                                    style="font-size:13px;border-radius:8px">
                                    <i class="fa fa-sign-in-alt me-2"></i>Login to Order
                                </a>
                            @else
                                {{-- BEBAS PESAN! Tidak ada lagi syarat harus punya kamar (activeBooking) --}}
                                <button type="button" 
                                        onclick="openOrderModal('{{ $menu->id }}', '{{ addslashes($menu->name) }}', '{{ $menu->price }}')"
                                        class="btn btn-primary w-100 py-2"
                                        style="font-size:13px;border-radius:8px">
                                    <i class="fa fa-cart-plus me-2"></i>Add to Cart
                                </button>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

        </div>
        @endforeach

        @endif

    </div>
</div>

<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="orderModalLabel"><i class="fa fa-utensils me-2"></i>Add to Cart</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            @if(session('guest_id'))
                <form action="{{ route('restaurant.cart.add') }}" method="POST" id="orderForm">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-light border mb-4">
                            <h6 class="fw-bold mb-1" id="modal_menu_name">Menu Name</h6>
                            <span class="text-primary fw-bold" id="modal_menu_price">Rp 0</span>
                        </div>

                        <input type="hidden" name="menu_id" id="modal_menu_id" required>
                        <input type="hidden" id="modal_menu_raw_price">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small text-muted fw-bold text-uppercase">Portion (Qty)</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center overflow-hidden rounded" style="border:1px solid #dee2e6">
                                        <button type="button" class="btn btn-light border-0 px-3 py-2" onclick="changeQty(-1)">
                                            <i class="fa fa-minus" style="font-size:11px"></i>
                                        </button>
                                        <input type="number" name="qty" id="modal_qty" class="form-control border-0 text-center fw-bold" style="width:60px;font-size:15px" value="1" min="1" required readonly>
                                        <button type="button" class="btn btn-light border-0 px-3 py-2" onclick="changeQty(1)">
                                            <i class="fa fa-plus" style="font-size:11px"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <label class="form-label small text-muted fw-bold text-uppercase">Special Notes (Optional)</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="E.g. less spicy, no peanuts..."></textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold">Estimated Total:</span>
                            <h4 class="text-primary mb-0 fw-bold" id="modal_total_price">Rp 0</h4>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" id="orderBtn">
                            <span id="orderLabel"><i class="fa fa-cart-plus me-2"></i>Add to Cart</span>
                        </button>
                    </div>
                </form>
            @else
                <div class="modal-body p-5 text-center">
                    <i class="fa fa-user-lock text-muted mb-3" style="font-size:3rem; opacity:0.5"></i>
                    <h5 class="fw-bold">Authentication Required</h5>
                    <p class="text-muted mb-4" style="font-size:14px">
                        Please sign in to place an order.
                    </p>
                    <a href="{{ route('guest.login') }}" class="btn btn-primary px-5 py-2">Sign In</a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- FLOATING CART BUTTON --}}
@php
    $cart = session()->get('restaurant_cart', []);
    $cartTotalItems = 0;
    $cartTotalPrice = 0;
    foreach($cart as $item) {
        $cartTotalItems += $item['qty'];
        $cartTotalPrice += ($item['price'] * $item['qty']);
    }
@endphp

@if($cartTotalItems > 0)
<div class="position-fixed bottom-0 end-0 p-4" style="z-index: 1050; animation: bounceIn 0.5s;">
    <a href="{{ route('checkout.restaurant') }}" class="btn btn-primary shadow-lg rounded-pill d-flex align-items-center py-2 px-4 text-decoration-none" style="border: 3px solid white;">
        <div class="position-relative me-3">
            <i class="fa fa-shopping-cart" style="font-size: 1.5rem;"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.7rem; padding: 0.25em 0.5em;">
                {{ $cartTotalItems }}
            </span>
        </div>
        <div class="text-start border-start border-white ps-3 ms-1 border-opacity-50">
            <span class="d-block small fw-bold" style="line-height: 1;">Checkout Now</span>
            <span class="d-block fw-bold" style="font-size: 1.1rem; line-height: 1.2;">Rp {{ number_format($cartTotalPrice, 0, ',', '.') }}</span>
        </div>
        <i class="fa fa-chevron-right ms-3"></i>
    </a>
</div>

<style>
    @keyframes bounceIn {
        0% { transform: scale(0.1); opacity: 0; }
        60% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(1); }
    }
</style>
@endif

@endsection

@push('styles')
<style>
    .menu-card:hover { transform: translateY(-4px); }
    .menu-card { box-shadow: 0 2px 12px rgba(0,0,0,.06) !important; }
    .menu-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.1) !important; }
</style>
@endpush

@push('scripts')
<script>
    function openOrderModal(id, name, price) {
        document.getElementById('modal_menu_id').value = id;
        document.getElementById('modal_menu_name').innerText = name;
        
        let formattedPrice = new Intl.NumberFormat('id-ID').format(price);
        document.getElementById('modal_menu_price').innerText = 'Rp ' + formattedPrice;
        
        document.getElementById('modal_menu_raw_price').value = price;
        document.getElementById('modal_qty').value = 1;
        
        calculateTotal();

        var orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
        orderModal.show();
    }

    function calculateTotal() {
        let price = document.getElementById('modal_menu_raw_price').value;
        let qty = document.getElementById('modal_qty').value;
        
        if(!qty || qty < 1) qty = 1;
        
        let total = price * qty;
        let formattedTotal = new Intl.NumberFormat('id-ID').format(total);
        
        document.getElementById('modal_total_price').innerText = 'Rp ' + formattedTotal;
    }
</script>
@endpush