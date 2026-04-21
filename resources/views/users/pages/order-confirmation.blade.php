@extends('users.layouts.app')

@section('title', 'Order Confirmation – ' . config('hotel.name', 'Hotelier'))

@section('content')

{{-- Page Header --}}
@include('users.components.page-header', [
    'title'      => 'Order Confirmation',
    'breadcrumb' => 'Confirmation',
])

{{-- Confirmation Section --}}
<div class="container-fluid py-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">

                {{-- Success Banner --}}
                <div class="d-flex align-items-center gap-4 rounded p-4 mb-4 wow fadeInDown"
                     data-wow-delay="0.1s"
                     style="background:#d1e7dd;border:1px solid #badbcc">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:56px;height:56px;background:#28a745">
                        <i class="fa fa-check text-white" style="font-size:20px"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1" style="color:#0a3622">Order Placed Successfully!</h5>
                        <p class="mb-0" style="font-size:13px;color:#198754">
                            Your order is being prepared. Estimated delivery in
                            {{ $order->estimated_minutes ?? '15–20' }} minutes.
                        </p>
                    </div>
                </div>

                {{-- Order ID chip --}}
                <div class="d-flex align-items-center gap-2 mb-4">
                    <span class="d-flex align-items-center gap-2 rounded px-3 py-2"
                          style="background:#fff3cd;border:1px solid #ffc107;font-size:12.5px;font-weight:600;color:#856404">
                        <i class="fa fa-hashtag" style="font-size:11px"></i>
                        Order #{{ str_pad($order->id ?? 0, 8, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="text-muted" style="font-size:12px">
                        {{ now()->format('d M Y · H:i') }} WIB
                    </span>
                </div>

                {{-- Order Status Tracker --}}
                <div class="bg-white rounded shadow-sm p-4 mb-4 wow fadeInUp" data-wow-delay="0.15s"
                     style="border:1px solid #e9ecef">
                    <h6 class="fw-bold mb-4" style="font-size:13px;color:#344767;text-transform:uppercase;letter-spacing:.5px">
                        Order Status
                    </h6>

                    @php
                        $status   = $order->status ?? 'preparing';
                        $steps    = [
                            'placed'    => ['icon' => 'fa-check',          'label' => 'Order Placed'],
                            'preparing' => ['icon' => 'fa-fire',           'label' => 'Preparing'],
                            'on_the_way'=> ['icon' => 'fa-running',        'label' => 'On the Way'],
                            'delivered' => ['icon' => 'fa-check-double',   'label' => 'Delivered'],
                        ];
                        $stepKeys = array_keys($steps);
                        $currentIdx = array_search($status, $stepKeys) !== false ? array_search($status, $stepKeys) : 0;
                    @endphp

                    <div class="d-flex align-items-start">
                        @foreach($steps as $key => $step)
                        @php
                            $idx    = array_search($key, $stepKeys);
                            $isDone = $idx < $currentIdx;
                            $isActive = $idx === $currentIdx;
                        @endphp
                        <div class="flex-fill text-center position-relative">
                            {{-- Connector line --}}
                            @if(!$loop->last)
                            <div class="position-absolute top-0 start-50 w-100"
                                 style="height:2px;top:14px;left:50%;background:{{ $isDone ? '#28a745' : '#dee2e6' }};z-index:0"></div>
                            @endif

                            {{-- Step dot --}}
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 position-relative"
                                 style="width:28px;height:28px;z-index:1;
                                 background:{{ $isDone ? '#28a745' : ($isActive ? '#f39c12' : '#dee2e6') }}">
                                <i class="fa {{ $step['icon'] }}"
                                   style="font-size:11px;color:{{ ($isDone || $isActive) ? '#fff' : '#adb5bd' }}"></i>
                            </div>

                            <div style="font-size:11px;font-weight:{{ $isActive ? '700' : '500' }};
                                 color:{{ $isDone ? '#28a745' : ($isActive ? '#f39c12' : '#adb5bd') }}">
                                {{ $step['label'] }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Info Cards --}}
                <div class="row g-3 mb-4 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="col-6">
                        <div class="rounded p-3 h-100" style="background:#fff;border:1px solid #e9ecef">
                            <div style="font-size:10px;font-weight:600;color:#adb5bd;text-transform:uppercase;letter-spacing:.7px;margin-bottom:4px">
                                Deliver to
                            </div>
                            <div style="font-size:14px;font-weight:700;color:#1a1f2e">
                                Room {{ $order->room->room_number ?? '-' }}
                            </div>
                            <div style="font-size:12px;color:#6c757d">
                                {{ config('hotel.name', 'Hotelier') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="rounded p-3 h-100" style="background:#fff;border:1px solid #e9ecef">
                            <div style="font-size:10px;font-weight:600;color:#adb5bd;text-transform:uppercase;letter-spacing:.7px;margin-bottom:4px">
                                Ordered by
                            </div>
                            <div style="font-size:14px;font-weight:700;color:#1a1f2e">
                                {{-- 🟢 Perbaikan relasi menjadi guest --}}
                                {{ $order->guest->name ?? session('guest_name') ?? 'Guest' }}
                            </div>
                            <div style="font-size:12px;color:#6c757d">
                                {{ $order->guest->email ?? '' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="rounded p-3 h-100" style="background:#fff;border:1px solid #e9ecef">
                            <div style="font-size:10px;font-weight:600;color:#adb5bd;text-transform:uppercase;letter-spacing:.7px;margin-bottom:4px">
                                Ordered at
                            </div>
                            <div style="font-size:14px;font-weight:700;color:#1a1f2e">
                                {{ optional($order->created_at)->format('H:i') ?? now()->format('H:i') }} WIB
                            </div>
                            <div style="font-size:12px;color:#6c757d">
                                {{ optional($order->created_at)->format('d M Y') ?? now()->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="rounded p-3 h-100" style="background:#fff3cd;border:1px solid #ffc107">
                            <div style="font-size:10px;font-weight:600;color:#856404;text-transform:uppercase;letter-spacing:.7px;margin-bottom:4px">
                                Est. Delivery
                            </div>
                            <div style="font-size:14px;font-weight:700;color:#856404">
                                ~{{ $order->estimated_minutes ?? '15–20' }} min
                            </div>
                            <div style="font-size:12px;color:#b07c2a">
                                From now
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Receipt --}}
                <div class="rounded overflow-hidden shadow-sm mb-4 wow fadeInUp" data-wow-delay="0.25s"
                     style="border:1px solid #e9ecef">

                    {{-- Receipt Header --}}
                    <div class="d-flex justify-content-between align-items-center px-4 py-3"
                         style="background:#1a1f2e">
                        <div>
                            <div style="font-size:13.5px;font-weight:600;color:#fff">Order Summary</div>
                            <div style="font-size:11px;color:rgba(255,255,255,.45)">
                                #{{ str_pad($order->id ?? 0, 8, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                        <i class="fa fa-receipt text-primary" style="font-size:18px;opacity:.6"></i>
                    </div>

                    {{-- Line items --}}
                    <div class="bg-white px-4 py-2">
                        @php
                            // 🟢 Perbaikan pemanggilan relasi menjadi details
                            $items = $order->details ?? collect([]);
                            $subtotal = 0;
                        @endphp

                        @foreach($items as $item)
                        @php
                            $itemName  = $item->menu->name ?? 'Item';
                            $itemQty   = $item->quantity ?? 1;
                            $itemPrice = ($item->price ?? 0) * $itemQty;
                            $subtotal += $itemPrice;
                        @endphp
                        <div class="d-flex justify-content-between align-items-start py-3"
                             style="border-bottom:1px solid #f1f3f5">
                            <div style="flex:1;min-width:0">
                                <div style="font-size:13.5px;font-weight:600;color:#1a1f2e">
                                    {{ $itemName }}
                                </div>
                                <div style="font-size:12px;color:#adb5bd">
                                    Qty: {{ $itemQty }}
                                </div>
                            </div>
                            <div style="font-size:13.5px;font-weight:600;color:#1a1f2e;white-space:nowrap;padding-left:16px">
                                Rp {{ number_format($itemPrice, 0, ',', '.') }}
                            </div>
                        </div>
                        @endforeach

                        {{-- Service charge --}}
                        @php $serviceCharge = round($subtotal * 0.05); @endphp
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f3f5">
                            <span style="font-size:13px;color:#adb5bd">Service Charge (5%)</span>
                            <span style="font-size:13px;color:#adb5bd">
                                Rp {{ number_format($serviceCharge, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Total --}}
                    @php $total = $order->total_price ?? ($subtotal + $serviceCharge); @endphp
                    <div class="d-flex justify-content-between align-items-center px-4 py-3"
                         style="background:#f8f9fa;border-top:2px solid #dee2e6">
                        <span style="font-size:14px;font-weight:700;color:#1a1f2e">Total</span>
                        <span style="font-size:22px;font-weight:700;color:#f39c12">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>

                </div>

                {{-- Notes --}}
                @if(!empty($order->notes))
                <div class="rounded p-3 mb-4" style="background:#f8f9fa;border:1px solid #e9ecef">
                    <div style="font-size:11px;font-weight:600;color:#adb5bd;text-transform:uppercase;letter-spacing:.7px;margin-bottom:4px">
                        Special Notes
                    </div>
                    <p class="mb-0" style="font-size:13px;color:#344767">{{ $order->notes }}</p>
                </div>
                @endif

                {{-- Action Buttons --}}
                <div class="row g-3 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="col-md-6">
                        <a href="{{ route('menus') }}" class="btn btn-primary w-100 py-3 fw-semibold">
                            <i class="fa fa-utensils me-2"></i>Order More
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 py-3 fw-semibold">
                            <i class="fa fa-home me-2"></i>Back to Home
                        </a>
                    </div>
                </div>

                {{-- Help note --}}
                <div class="text-center mt-4">
                    <p class="text-muted" style="font-size:12.5px">
                        Have a question about your order?
                        <a href="{{ route('contact') }}" class="text-primary text-decoration-none fw-semibold">
                            Contact Us
                        </a>
                        or call
                        <a href="tel:{{ config('hotel.phone', '') }}" class="text-primary text-decoration-none fw-semibold">
                            {{ config('hotel.phone', '+012 345 6789') }}
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    @media print {
        nav, footer, .btn, .wow { display: none !important; }
        .container { max-width: 100% !important; }
    }
</style>
@endpush