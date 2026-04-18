@extends('layouts.app')

@section('title', 'Book A Room - Hotelier')

@section('content')

@include('components.page-header', ['title' => 'Book A Room', 'breadcrumb' => 'Booking'])

<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title text-center text-primary text-uppercase">Reservation</h6>
            <h1 class="mb-5">Book A <span class="text-primary text-uppercase">Luxurious</span> Room</h1>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('booking.store') }}" method="POST">
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
                <div class="col-md-6">
                    <div class="form-floating date" id="date1" data-target-input="nearest">
                        <input type="text"
                               name="check_in"
                               class="form-control datetimepicker-input @error('check_in') is-invalid @enderror"
                               id="checkin"
                               placeholder="Check In"
                               data-target="#date1"
                               data-toggle="datetimepicker"
                               value="{{ old('check_in', request('check_in')) }}">
                        <label for="checkin">Check In</label>
                        @error('check_in')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating date" id="date2" data-target-input="nearest">
                        <input type="text"
                               name="check_out"
                               class="form-control datetimepicker-input @error('check_out') is-invalid @enderror"
                               id="checkout"
                               placeholder="Check Out"
                               data-target="#date2"
                               data-toggle="datetimepicker"
                               value="{{ old('check_out', request('check_out')) }}">
                        <label for="checkout">Check Out</label>
                        @error('check_out')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="adult" name="adult">
                            <option value="1" {{ old('adult') == 1 ? 'selected' : '' }}>Adult 1</option>
                            <option value="2" {{ old('adult') == 2 ? 'selected' : '' }}>Adult 2</option>
                            <option value="3" {{ old('adult') == 3 ? 'selected' : '' }}>Adult 3</option>
                        </select>
                        <label for="adult">Adult</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="child" name="child">
                            <option value="0" {{ old('child') == 0 ? 'selected' : '' }}>Child 0</option>
                            <option value="1" {{ old('child') == 1 ? 'selected' : '' }}>Child 1</option>
                            <option value="2" {{ old('child') == 2 ? 'selected' : '' }}>Child 2</option>
                        </select>
                        <label for="child">Child</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="room" name="room_id">
                            <option value="">-- Select Room --</option>
                            @foreach($rooms ?? [] as $room)
                            <option value="{{ $room->id }}"
                                {{ (old('room_id', request('room')) == $room->id) ? 'selected' : '' }}>
                                {{ $room->name }} - Rp {{ number_format($room->price) }}/Night
                            </option>
                            @endforeach
                        </select>
                        <label for="room">Room Type</label>
                        @error('room_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating">
                        <textarea class="form-control @error('special_request') is-invalid @enderror"
                                  placeholder="Special Request"
                                  id="message"
                                  name="special_request"
                                  style="height: 100px">{{ old('special_request') }}</textarea>
                        <label for="message">Special Request</label>
                        @error('special_request')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary w-100 py-3" type="submit">Book Now</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection