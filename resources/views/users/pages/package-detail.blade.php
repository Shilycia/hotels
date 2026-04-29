@extends('users/layouts/app')
@section('title', $package->name . ' - Hotel Neo')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('packages.index') }}">Packages</a></li>
                    <li class="breadcrumb-item active">{{ $package->name }}</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-lg">
                <div class="row g-0">
                    <div class="col-md-5">
                        <img src="{{ $package->restaurantMenu->foto_url ?? asset('img/package-placeholder.jpg') }}" 
                             class="img-fluid rounded-start h-100" style="object-fit: cover; height: 500px;" alt="{{ $package->name }}">
                    </div>
                    <div class="col-md-7">
                        <div class="card-body">
                            <h1 class="card-title fw-bold mb-3 text-dark">{{ $package->name }}</h1>
                            <div class="mb-4">
                                <span class="h3 fw-bold text-gold mb-2 d-block">
                                    Rp {{ number_format($package->total_price, 0, ',', '.') }}
                                </span>
                                @if($package->roomType)
                                <span class="badge bg-light text-dark mb-2">
                                    <i class="fas fa-bed me-1"></i>{{ $package->roomType->name }}
                                </span>
                                @endif
                            </div>

                            <div class="mb-4">
                                <h5><i class="fas fa-list text-gold me-2"></i>What's Included</h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Full room access</li>
                                    @if($package->paketItems->isNotEmpty())
                                        @foreach($package->paketItems as $item)
                                        <li class="mb-2">
                                            <i class="fas fa-utensils text-success me-2"></i>
                                            {{ $item->name }} (x{{ $item->pivot->quantity }})
                                        </li>
                                        @endforeach
                                    @else
                                        <li class="mb-2"><i class="fas fa-utensils text-success me-2"></i>Special meals package</li>
                                    @endif
                                </ul>
                            </div>

                            <div class="mb-4">
                                <p class="lead text-dark mb-1">{{ $package->description }}</p>
                            </div>

                            <a href="{{ route('package.customize', $package) }}" class="btn btn-gold btn-lg px-4">
                                <i class="fas fa-pencil-alt me-2"></i>Customize & Book Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title mb-3">Quick Booking</h5>
                    <form action="{{ route('package.store') }}" method="GET" class="needs-validation" novalidate>
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <div class="mb-3">
                            <label class="form-label">Check-in Date</label>
                            <input type="date" class="form-control" name="check_in" required min="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Check-out Date</label>
                            <input type="date" class="form-control" name="check_out" required>
                        </div>
                        <button type="submit" class="btn btn-outline-gold w-100 py-2">
                            <i class="fas fa-calendar-check me-2"></i>Check Availability
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
