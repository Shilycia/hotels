@extends('users/layouts/app')
@section('title', 'Packages - Hotel Neo')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="fas fa-gift text-gold me-2"></i>
                Special Packages
            </h2>
        </div>
    </div>

    <div class="row g-4">
        @forelse($packages as $package)
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm border-0 package-card">
                <div class="position-relative">
                    <img src="{{ $package->restaurantMenu->foto_url ?? asset('img/package-placeholder.jpg') }}" 
                         class="card-img-top" alt="{{ $package->name }}" style="height: 220px; object-fit: cover;">
                    @if($package->restaurantMenu && $package->restaurantMenu->can_bundle_with_room)
                    <span class="badge bg-gold position-absolute top-0 end-0 m-2">Bundle Room</span>
                    @endif
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold text-dark mb-2">{{ $package->name }}</h5>
                    <p class="card-text text-muted flex-grow-1">{{ Str::limit($package->description, 100) }}</p>
                    @if($package->roomType)
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-bed me-1"></i>
                            {{ $package->roomType->name }}
                        </small>
                    </div>
                    @endif
                    @if($package->paketItems->isNotEmpty())
                    <div class="mb-3">
                        <small class="text-success fw-medium">
                            <i class="fas fa-utensils me-1"></i>
                            Includes {{ $package->paketItems->count() }} menu items
                        </small>
                    </div>
                    @endif
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-end">
                            <span class="h4 mb-0 fw-bold text-gold">
                                Rp {{ number_format($package->total_price, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('packages.show', $package) }}" class="btn btn-gold btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No packages available at the moment</h4>
                <p class="text-muted">Check back later for special offers!</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
