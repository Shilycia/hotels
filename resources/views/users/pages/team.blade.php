{{-- TEAM PAGE: resources/views/pages/team.blade.php --}}

@extends('users.layouts.app')

@section('title', 'Our Team - Hotelier')

@section('content')

@include('users.components.page-header', ['title' => 'Our Team', 'breadcrumb' => 'Team'])

<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Our Team</h6>
            <h1 class="mb-5">Explore Our <span class="text-primary text-uppercase">Luxury</span> Staff</h1>
        </div>
        <div class="row g-4">
            @forelse($teamMembers ?? [] as $member)
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="rounded shadow overflow-hidden">
                    <div class="position-relative">
                        <img class="img-fluid" src="{{ asset($member->photo) }}" alt="{{ $member->name }}">
                        <div class="position-absolute start-50 top-100 translate-middle d-flex align-items-center">
                            <a class="btn btn-square btn-primary mx-1" href="{{ $member->facebook ?? '#' }}">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a class="btn btn-square btn-primary mx-1" href="{{ $member->twitter ?? '#' }}">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a class="btn btn-square btn-primary mx-1" href="{{ $member->instagram ?? '#' }}">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                    <div class="text-center p-4 mt-3">
                        <h5 class="fw-bold mb-0">{{ $member->name }}</h5>
                        <small>{{ $member->position }}</small>
                    </div>
                </div>
            </div>
            @empty
            @foreach([
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-1.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-2.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-3.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-4.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-1.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-2.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-3.jpg'],
                ['name' => 'Full Name', 'role' => 'Designation', 'img' => 'img/team-4.jpg'],
            ] as $member)
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="rounded shadow overflow-hidden">
                    <div class="position-relative">
                        <img class="img-fluid" src="{{ asset($member['img']) }}" alt="{{ $member['name'] }}">
                        <div class="position-absolute start-50 top-100 translate-middle d-flex align-items-center">
                            <a class="btn btn-square btn-primary mx-1" href="#"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href="#"><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="text-center p-4 mt-3">
                        <h5 class="fw-bold mb-0">{{ $member['name'] }}</h5>
                        <small>{{ $member['role'] }}</small>
                    </div>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</div>

@endsection