{{-- Footer --}}
<div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">

            {{-- Company Links --}}
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Company</h4>
                <a class="btn btn-link" href="{{ route('about') }}">About Us</a>
                <a class="btn btn-link" href="{{ route('contact') }}">Contact Us</a>
                <a class="btn btn-link" href="{{ route('services') }}">Our Services</a>
                <a class="btn btn-link" href="#">Privacy Policy</a>
                <a class="btn btn-link" href="#">Terms & Conditions</a>
            </div>

            {{-- Contact Info --}}
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Contact</h4>
                <p class="mb-2">
                    <i class="fa fa-map-marker-alt me-3"></i>{{ config('hotel.address', '123 Street, Bogor, West Java') }}
                </p>
                <p class="mb-2">
                    <i class="fa fa-phone-alt me-3"></i>{{ config('hotel.phone', '+012 345 6789') }}
                </p>
                <p class="mb-2">
                    <i class="fa fa-envelope me-3"></i>{{ config('hotel.email', 'info@hotelier.com') }}
                </p>
                <div class="d-flex pt-2">
                    <a class="btn btn-outline-light btn-social" href="{{ config('hotel.twitter', '#') }}" target="_blank" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a class="btn btn-outline-light btn-social" href="{{ config('hotel.facebook', '#') }}" target="_blank" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a class="btn btn-outline-light btn-social" href="{{ config('hotel.youtube', '#') }}" target="_blank" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a class="btn btn-outline-light btn-social" href="{{ config('hotel.linkedin', '#') }}" target="_blank" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            {{-- Opening Hours --}}
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Opening Hours</h4>
                <h6 class="text-light">Monday – Friday</h6>
                <p class="mb-4">09.00 AM – 09.00 PM</p>
                <h6 class="text-light">Saturday – Sunday</h6>
                <p class="mb-0">09.00 AM – 12.00 PM</p>
            </div>

            {{-- Newsletter --}}
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Newsletter</h4>
                <p>Subscribe to get the latest offers and hotel news delivered to your inbox.</p>
                <div class="position-relative mx-auto" style="max-width: 400px;">
                    <input class="form-control border-primary w-100 py-3 ps-4 pe-5"
                        type="email"
                        id="newsletter-email"
                        placeholder="Your email address"
                        aria-label="Email address for newsletter">
                    <button type="button"
                        class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2"
                        onclick="subscribeNewsletter()">
                        Sign Up
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- Copyright --}}
    <div class="container">
        <div class="copyright">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    &copy; <a class="border-bottom" href="#">{{ config('hotel.name', 'Hotelier') }}</a>,
                    All Rights Reserved.
                    Designed By <a class="border-bottom" href="https://htmlcodex.com" target="_blank">HTML Codex</a>
                    Distributed By <a class="border-bottom" href="https://themewagon.com" target="_blank">ThemeWagon</a>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="{{ route('home') }}">Home</a>
                        <a href="#">Cookies</a>
                        <a href="#">Help</a>
                        <a href="#">FAQs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function subscribeNewsletter() {
    const emailInput = document.getElementById('newsletter-email');
    const email = emailInput.value.trim();

    if (!email) {
        alert('Please enter your email address.');
        emailInput.focus();
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        emailInput.focus();
        return;
    }

    fetch('{{ route("newsletter.subscribe") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ email })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) emailInput.value = '';
    })
    .catch(() => alert('An error occurred. Please try again later.'));
}
</script>
@endpush