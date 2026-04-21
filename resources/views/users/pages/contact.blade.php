@extends('users.layouts.app')

@section('title', 'Contact Us - Hotelier')

@section('content')

@include('users.components.page-header', ['title' => 'Contact Us', 'breadcrumb' => 'Contact'])

<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Contact Us</h6>
            <h1 class="mb-5"><span class="text-primary text-uppercase">Contact</span> For Any Query</h1>
        </div>
        <div class="row g-4">
            <div class="col-12">
                <div class="row gy-4">
                    <div class="col-md-4">
                        <div class="h-100 bg-light rounded d-flex align-items-center p-4">
                            <div class="btn-lg-square bg-primary flex-shrink-0 me-4">
                                <i class="fa fa-map-marker-alt text-white"></i>
                            </div>
                            <div>
                                <p class="mb-2 text-primary fw-bold">Address</p>
                                <p class="mb-0">{{ config('hotel.address', '123 Street, Bogor, West Java 16000') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="h-100 bg-light rounded d-flex align-items-center p-4">
                            <div class="btn-lg-square bg-primary flex-shrink-0 me-4">
                                <i class="fa fa-phone-alt text-white"></i>
                            </div>
                            <div>
                                <p class="mb-2 text-primary fw-bold">Phone</p>
                                <p class="mb-0">{{ config('hotel.phone', '+012 345 6789') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="h-100 bg-light rounded d-flex align-items-center p-4">
                            <div class="btn-lg-square bg-primary flex-shrink-0 me-4">
                                <i class="fa fa-envelope-open text-white"></i>
                            </div>
                            <div>
                                <p class="mb-2 text-primary fw-bold">Email</p>
                                <p class="mb-0">{{ config('hotel.email', 'info@hotelier.com') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 wow fadeIn" data-wow-delay="0.1s">
                <iframe class="position-relative rounded w-100 h-100"
                        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d19882.60136778944!2d-0.1308784!3d51.5228264!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xb78f2474b9a45aa9!2sGoogle!5e0!3m2!1sen!2sbd!4v1637747884403!5m2!1sen!2sbd"
                        frameborder="0"
                        style="min-height: 400px; border:0;"
                        allowfullscreen=""
                        aria-hidden="false"
                        tabindex="0"></iframe>
            </div>
            <div class="col-md-6">
                <div class="wow fadeInUp" data-wow-delay="0.2s">
                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text"
                                           name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           placeholder="Your Name"
                                           value="{{ old('name') }}">
                                    <label for="name">Your Name</label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email"
                                           name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           placeholder="Your Email"
                                           value="{{ old('email') }}">
                                    <label for="email">Your Email</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text"
                                           name="subject"
                                           class="form-control @error('subject') is-invalid @enderror"
                                           id="subject"
                                           placeholder="Subject"
                                           value="{{ old('subject') }}">
                                    <label for="subject">Subject</label>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('message') is-invalid @enderror"
                                              placeholder="Leave a message here"
                                              id="message"
                                              name="message"
                                              style="height: 150px">{{ old('message') }}</textarea>
                                    <label for="message">Message</label>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection