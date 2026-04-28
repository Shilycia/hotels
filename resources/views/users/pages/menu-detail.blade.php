@extends('users.layouts.app')

@section('title', ($menu->name ?? 'Menu Detail') . ' – ' . config('hotel.name', 'Hotelier'))

@section('content')

{{-- Page Header --}}
@include('users.components.page-header', [
    'title'      => $menu->name ?? 'Menu Detail',
    'parent'     => 'Menu',
    'parentUrl'  => route('menus'),
    'breadcrumb' => $menu->name ?? 'Detail',
])

{{-- Menu Detail Section --}}
<div class="container-fluid py-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="row g-5 align-items-start">

            {{-- Left: Image --}}
            <div class="col-lg-5 wow fadeInLeft" data-wow-delay="0.1s">
                <div class="position-relative rounded overflow-hidden shadow-sm"
                     style="height:360px;background:#f1f3f5">
                    
                    @if(!empty($menu->foto_url))
                        <img src="{{ asset('storage/' . $menu->foto_url) }}" alt="{{ $menu->name }}"
                                        class="w-100 h-100" style="object-fit:cover">
                    @else
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                            <i class="fa fa-utensils" style="font-size:4rem;color:#dee2e6"></i>
                        </div>
                    @endif

                    {{-- Category badge --}}
                    <span class="badge position-absolute top-0 start-0 m-3"
                          style="font-size:11px;padding:5px 12px;border-radius:50px;
                          @switch($menu->category ?? '')
                              @case('Makanan')  background:#fff3cd;color:#856404; @break
                              @case('Minuman')  background:#cfe2ff;color:#084298; @break
                              @case('Dessert')  background:#f8d7da;color:#842029; @break
                              @case('Snack')    background:#d1e7dd;color:#0a3622; @break
                              @default          background:#e9ecef;color:#495057;
                          @endswitch">
                        {{ $menu->category ?? 'Menu' }}
                    </span>

                    {{-- Availability overlay --}}
                    @if(!($menu->is_available ?? true))
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                         style="background:rgba(0,0,0,.45)">
                        <span class="badge bg-danger fs-6 px-4 py-2">Sold Out</span>
                    </div>
                    @endif
                </div>

                {{-- Related / other items from same category --}}
                @if(isset($relatedMenus) && $relatedMenus->count())
                <div class="mt-4">
                    <h6 class="fw-bold mb-3" style="font-size:13px;color:#344767">
                        More from {{ $menu->category }}
                    </h6>
                    <div class="row g-2">
                        @foreach($relatedMenus->take(3) as $related)
                        <div class="col-4">
                            <a href="{{ route('menu.detail', $related->id) }}"
                               class="text-decoration-none">
                                <div class="rounded overflow-hidden border"
                                     style="height:80px;background:#f1f3f5">
                                    @if(!empty($related->foto_url))
                                        <img src="{{ asset('storage/' . $menu->foto_url) }}" alt="{{ $menu->name }}"
                                        class="w-100 h-100" style="object-fit:cover">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                            <i class="fa fa-utensils text-muted" style="font-size:1.2rem;opacity:.4"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-1" style="font-size:11px;color:#344767;font-weight:500;
                                    white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    {{ $related->name }}
                                </div>
                                <div style="font-size:11px;color:#f39c12;font-weight:600">
                                    Rp {{ number_format($related->price ?? 0, 0, ',', '.') }}
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Right: Detail & Order Form --}}
            <div class="col-lg-7 wow fadeInRight" data-wow-delay="0.2s">

                {{-- Breadcrumb tag --}}
                <div class="mb-2">
                    <span style="font-size:10.5px;font-weight:600;color:#f39c12;text-transform:uppercase;letter-spacing:1.2px">
                        {{ $menu->category ?? 'Menu' }} · Chef's Special
                    </span>
                </div>

                <h1 class="fw-bold mb-2" style="font-size:28px;color:#1a1f2e">
                    {{ $menu->name }}
                </h1>

                {{-- Rating row --}}
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div>
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star {{ $i <= ($menu->rating ?? 5) ? 'text-warning' : 'text-muted' }}"
                               style="font-size:12px"></i>
                        @endfor
                        <span class="text-muted ms-1" style="font-size:12px">
                            {{ number_format($menu->rating ?? 4.8, 1) }}/5
                        </span>
                    </div>
                    <span class="text-muted" style="font-size:12px">|</span>
                    <span class="text-muted" style="font-size:12px">
                        <i class="fa fa-clock me-1"></i>{{ $menu->prep_time ?? '15' }} min prep
                    </span>
                    <span class="text-muted" style="font-size:12px">|</span>
                    <span class="text-muted" style="font-size:12px">
                        <i class="fa fa-fire me-1"></i>{{ $menu->calories ?? '450' }} kcal
                    </span>
                </div>

                <p class="mb-4" style="font-size:14px;color:#6c757d;line-height:1.7">
                    {{ $menu->description ?? 'A delicious dish freshly prepared by our talented kitchen team using only the finest ingredients.' }}
                </p>
                @if($menu->category == 'paket' && $menu->paketItems->count() > 0)
                    <div class="bg-light rounded p-4 mb-4 border" style="border-left: 4px solid #f39c12 !important;">
                        <h6 class="fw-bold mb-3" style="color: #1a1f2e; font-size: 14px;">
                            <i class="fa fa-box-open text-warning me-2"></i>Yang Anda dapatkan dalam paket ini:
                        </h6>
                        <ul class="list-unstyled mb-0">
                            @foreach($menu->paketItems as $item)
                                <li class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : 'mb-0 pb-0' }} d-flex align-items-center">
                                    <div class="rounded border d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 45px; height: 45px; background: #fff; overflow: hidden; flex-shrink: 0;">
                                        @if($item->foto_url)
                                            <img src="{{ asset('storage/' . $item->foto_url) }}" class="w-100 h-100" style="object-fit:cover">
                                        @else
                                            <i class="fa fa-utensils text-muted" style="font-size: 14px;"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark" style="font-size: 13.5px;">{{ $item->name }}</span>
                                        <span class="d-block text-muted" style="font-size: 11.5px;">Termasuk 1 Porsi</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{-- Specs grid --}}
                <div class="row g-2 mb-4">
                    @foreach([
                        ['icon' => 'fa-clock',          'val' => ($menu->prep_time ?? '15') . ' min',        'lbl' => 'Prep Time'],
                        ['icon' => 'fa-fire',           'val' => ($menu->calories ?? '450') . ' kcal',       'lbl' => 'Calories'],
                        ['icon' => 'fa-leaf',           'val' => $menu->allergens ?? 'None',                 'lbl' => 'Allergens'],
                        ['icon' => 'fa-concierge-bell', 'val' => $menu->serving ?? '1 portion',              'lbl' => 'Serving'],
                    ] as $spec)
                    <div class="col-6 col-md-3">
                        <div class="rounded p-3 text-center h-100" style="background:#f8f9fa;border:1px solid #e9ecef">
                            <i class="fa {{ $spec['icon'] }} text-primary mb-1" style="font-size:16px"></i>
                            <div style="font-size:13px;font-weight:700;color:#1a1f2e">{{ $spec['val'] }}</div>
                            <div style="font-size:10.5px;color:#6c757d">{{ $spec['lbl'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Price & availability --}}
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span style="font-size:30px;font-weight:700;color:#f39c12">
                        Rp {{ number_format($menu->price ?? 0, 0, ',', '.') }}
                    </span>
                    @if($menu->is_available ?? true)
                        <span class="d-flex align-items-center gap-2 px-3 py-1 rounded-pill"
                              style="background:#d1e7dd;font-size:12px;font-weight:600;color:#0a3622">
                            <span style="width:7px;height:7px;border-radius:50%;background:#28a745;display:inline-block"></span>
                            Available
                        </span>
                    @else
                        <span class="badge bg-danger rounded-pill px-3 py-2" style="font-size:12px">
                            Sold Out
                        </span>
                    @endif
                </div>

                {{-- Order Form --}}
                @if($menu->is_available ?? true)
                
                    @if(session('guest_id'))
                        {{-- HAPUS PENGECEKAN activeBooking DI SINI, SEMUA GUEST BISA PESAN --}}
                        <form method="POST" action="{{ route('restaurant.cart.add') }}" id="orderForm" class="bg-white rounded p-4 shadow-sm border">
                            @csrf
                            <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                            {{-- Booking ID tidak lagi dikirim dari sini, melainkan nanti di Checkout --}}

                            <div class="mb-3">
                                <label class="fw-semibold d-block mb-2" style="font-size:12px;text-transform:uppercase;color:#344767">
                                    Quantity
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center overflow-hidden rounded" style="border:1px solid #dee2e6">
                                        <button type="button" class="btn btn-light border-0 px-3 py-2" onclick="changeQty(-1)">
                                            <i class="fa fa-minus" style="font-size:11px"></i>
                                        </button>
                                        <input type="number" name="qty" id="qty" class="form-control border-0 text-center fw-bold" style="width:60px;font-size:15px" value="1" min="1" readonly>
                                        <button type="button" class="btn btn-light border-0 px-3 py-2" onclick="changeQty(1)">
                                            <i class="fa fa-plus" style="font-size:11px"></i>
                                        </button>
                                    </div>
                                    <span class="text-muted" style="font-size:13px">
                                        = <span id="totalDisplay" class="fw-bold text-primary fs-6">Rp {{ number_format($menu->price ?? 0, 0, ',', '.') }}</span>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="fw-semibold d-block mb-2" style="font-size:12px;text-transform:uppercase;color:#344767">
                                    Special Notes <span class="text-muted fw-normal">(optional)</span>
                                </label>
                                <textarea name="notes" class="form-control" rows="2" style="font-size:13.5px;resize:none" placeholder="E.g. no spicy, extra egg..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold" id="orderBtn">
                                <span id="orderLabel"><i class="fa fa-cart-plus me-2"></i>Add to Cart</span>
                            </button>
                        </form>
                    @else
                        {{-- Jika belum login --}}
                        <div class="rounded p-4 text-center" style="background:#f8f9fa;border:1px dashed #dee2e6">
                            <i class="fa fa-user-lock text-muted mb-2" style="font-size:2rem;opacity:.4"></i>
                            <p class="text-muted mb-3" style="font-size:13px">Please sign in as a guest to place an order.</p>
                            <a href="{{ route('guest.login') }}" class="btn btn-primary px-5">Sign In</a>
                        </div>
                    @endif
                    
                @else
                    {{-- Jika barang Sold Out --}}
                    <div class="rounded p-4 text-center" style="background:#f8f9fa;border:1px dashed #dee2e6">
                        <i class="fa fa-ban text-danger mb-2" style="font-size:2rem;opacity:.5"></i>
                        <p class="text-muted mb-3" style="font-size:13px">This item is currently not available.</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

{{-- CTA --}}
<div class="container-fluid bg-dark py-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container text-center">
        <h6 class="section-title text-center text-primary text-uppercase mb-3">Explore More</h6>
        <h2 class="text-white mb-3">Discover Our Full Menu Collection</h2>
        <a href="{{ route('menus') }}" class="btn btn-primary py-3 px-5">
            <i class="fa fa-utensils me-2"></i>View All Menu
        </a>
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

@push('scripts')
<script>
    const unitPrice = {{ $menu->price ?? 0 }};

    function changeQty(d) {
        const input = document.getElementById('qty');
        let val = parseInt(input.value) + d;
        val = Math.min(Math.max(val, 1), 10);
        input.value = val;
        const total = val * unitPrice;
        document.getElementById('totalDisplay').textContent =
            'Rp ' + total.toLocaleString('id-ID');
    }

    document.getElementById('orderForm')?.addEventListener('submit', function () {
        document.getElementById('orderBtn').disabled = true;
        document.getElementById('orderLabel').innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Adding...';
    });
</script>
@endpush