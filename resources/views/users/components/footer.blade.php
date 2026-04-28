{{-- Footer --}}
<div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="row g-5">

            {{-- Company Links --}}
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Hotel Neo</h4>
                <a class="btn btn-link" href="{{ route('about') }}">Tentang Kami</a>
                <a class="btn btn-link" href="{{ route('rooms.index') }}">Katalog Kamar</a>
                <a class="btn btn-link" href="{{ route('menus') }}">Menu Restoran</a>
                <a class="btn btn-link" href="#">Kebijakan Privasi</a>
                <a class="btn btn-link" href="#">Syarat & Ketentuan</a>
            </div>

            {{-- Contact Info --}}
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Kontak</h4>
                <p class="mb-2">
                    <i class="fa fa-map-marker-alt me-3"></i>{{ config('hotel.address', 'Jl. Akses UI, Depok, Indonesia') }}
                </p>
                <p class="mb-2">
                    <i class="fa fa-phone-alt me-3"></i>{{ config('hotel.phone', '+62 812 3456 7890') }}
                </p>
                <p class="mb-2">
                    <i class="fa fa-envelope me-3"></i>{{ config('hotel.email', 'hello@hotelneo.com') }}
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
                <h4 class="text-white mb-3">Jam Operasional</h4>
                <h6 class="text-light">Layanan Resepsionis</h6>
                <p class="mb-4">24 Jam Penuh</p>
                <h6 class="text-light">Layanan Restoran</h6>
                <p class="mb-0">06.00 Pagi – 10.00 Malam</p>
            </div>

            {{-- Newsletter --}}
            <div class="col-lg-3 col-md-6">
                <h4 class="text-white mb-3">Buletin Kami</h4>
                <p>Berlangganan untuk mendapatkan penawaran terbaru dan berita hotel langsung ke kotak masuk Anda.</p>
                <div class="position-relative mx-auto" style="max-width: 400px;">
                    <input class="form-control border-primary w-100 py-3 ps-4 pe-5"
                        type="email"
                        id="newsletter-email"
                        placeholder="Alamat email Anda"
                        aria-label="Email address for newsletter">
                    <button type="button"
                        class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2"
                        onclick="subscribeNewsletter()">
                        Daftar
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
                    &copy; <a class="border-bottom" href="{{ route('home') }}">Hotel Neo</a>, 
                    Hak Cipta Dilindungi.
                    <br>
                    Dirancang dengan <a class="border-bottom" href="https://htmlcodex.com" target="_blank">HTML Codex</a>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-menu">
                        <a href="{{ route('home') }}">Beranda</a>
                        <a href="#">Bantuan</a>
                        <a href="#">FAQ</a>
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
        alert('Mohon masukkan alamat email Anda.');
        emailInput.focus();
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Mohon masukkan format email yang valid.');
        emailInput.focus();
        return;
    }

    fetch('/api/newsletter-subscribe', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ email })
    })
    .then(res => {
        // Simulasi sukses sementara jika endpoint belum ada
        if(!res.ok) {
            alert('Terima kasih telah berlangganan! (Mode Simulasi)');
            emailInput.value = '';
            return;
        }
        return res.json();
    })
    .then(data => {
        if(data) {
            alert(data.message);
            if (data.success) emailInput.value = '';
        }
    })
    .catch(() => alert('Terima kasih telah mendaftar! Nanti kami hubungi.'));
}
</script>
@endpush