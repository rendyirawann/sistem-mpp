@extends('auth.app')
@section('title', 'Login - Sistem MPP Terpadu')
@section('content')

    <div class="mb-12">
        <h2 class="fw-black text-gray-900 fs-1 mb-3" style="letter-spacing: -1px;">Selamat Datang Kembali</h2>
        <p class="text-gray-500 fs-5">Silakan masuk ke akun Anda untuk mengelola sistem.</p>
    </div>

    <form id="kt_sign_in_form" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-6">
            <label class="form-label fw-bold text-gray-700 ms-1">Email atau No. WA</label>
            <div class="position-relative">
                <i class="ph-bold ph-envelope position-absolute top-50 translate-middle-y ms-5 fs-4 text-gray-400"></i>
                <input type="text" placeholder="Masukkan email atau nomor WA" name="email" autocomplete="off" class="form-control custom-input ps-14" />
            </div>
        </div>

        <div class="mb-6">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label fw-bold text-gray-700 ms-1 mb-0">Kata Sandi</label>
                <a href="{{ route('password.request') }}" class="text-accent fw-bold fs-7 text-decoration-none">Lupa Sandi?</a>
            </div>
            <div class="position-relative">
                <i class="ph-bold ph-lock-key position-absolute top-50 translate-middle-y ms-5 fs-4 text-gray-400"></i>
                <input type="password" placeholder="Masukkan kata sandi" name="password" autocomplete="off" class="form-control custom-input ps-14" />
            </div>
        </div>

        <div class="d-grid mt-10">
            <button type="submit" id="kt_sign_in_submit" class="btn-apple primary">
                <span class="indicator-label"><i class="ph-bold ph-sign-in me-2"></i> Masuk Sekarang</span>
                <span class="indicator-progress" style="display: none;">
                    Memproses... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </form>

    <div class="mt-15 text-center">
        <span class="text-gray-400 fw-medium fs-7">&copy; {{ date('Y') }} Sistem MPP Terpadu</span>
    </div>

@endsection

@push('scripts')
    <script>
        document.getElementById('kt_sign_in_form').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitButton = document.getElementById('kt_sign_in_submit');
            submitButton.classList.add("disabled");
            submitButton.querySelector('.indicator-label').style.display = 'none';
            submitButton.querySelector('.indicator-progress').style.display = 'inline-block';

            let formData = new FormData(this);

            fetch("{{ route('login') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: formData
                })
                .then(async response => {
                    let result = await response.json();

                    if (!response.ok) {
                        if (result.errors && result.errors.email && Array.isArray(result.errors.email) && result.errors.email[0].includes("Terlalu banyak percobaan")) {
                            let msg = result.errors.email[0];
                            let seconds = msg.match(/\d+/)[0];
                            showLockoutCountdown(seconds);
                        } else if (result.errors && result.errors.email) {
                            Swal.fire({
                                icon: "error",
                                title: "Login Gagal",
                                html: result.errors.email[0],
                                confirmButtonColor: "#4f46e5",
                                customClass: { confirmButton: 'btn-apple primary' }
                            });
                        }
                        submitButton.classList.remove("disabled");
                        submitButton.querySelector('.indicator-label').style.display = 'block';
                        submitButton.querySelector('.indicator-progress').style.display = 'none';
                        return;
                    }

                    superPremiumThreeDotLoader();
                })
                .catch(err => {
                    console.error(err);
                    submitButton.classList.remove("disabled");
                    submitButton.querySelector('.indicator-label').style.display = 'block';
                    submitButton.querySelector('.indicator-progress').style.display = 'none';
                });
        });

        function showLockoutCountdown(seconds) {
            let originalSeconds = seconds;

            Swal.fire({
                icon: "warning",
                title: "Terlalu Banyak Percobaan!",
                html: `
            Anda telah gagal login 3 kali.<br>
            Coba lagi dalam <b id="countdown">${seconds}</b> detik.
            <br><br>
            <div class="progress bg-light" style="height: 10px; border-radius: 20px;">
                <div id="lock-progress" class="progress-bar bg-danger" style="width: 0%;"></div>
            </div>
        `,
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: seconds * 1000,
                didOpen: () => {
                    let countdownEl = document.getElementById("countdown");
                    let bar = document.getElementById("lock-progress");

                    let interval = setInterval(() => {
                        seconds--;
                        countdownEl.textContent = seconds;
                        let percent = 100 - Math.floor((seconds / originalSeconds) * 100);
                        bar.style.width = percent + "%";

                        if (seconds <= 0) clearInterval(interval);
                    }, 1000);
                }
            });
        }

        function superPremiumThreeDotLoader() {
            let timerInterval;

            Swal.fire({
                icon: "success",
                title: `<span class="fw-bold">Login Berhasil</span>`,
                html: `
            <div class="text-muted mb-3">Menyiapkan ruang kerja Anda...</div>
            <div class="my-10" style="display:flex; justify-content:center; align-items:center; gap:10px;">
                <div class="dot-loader"></div>
                <div class="dot-loader dot-loader--2"></div>
                <div class="dot-loader dot-loader--3"></div>
            </div>
            <div class='progress bg-light mt-3' style='height: 12px; border-radius: 20px; overflow: hidden;'>
                <div id="sa-progress-premium" class='progress-bar' style='background: #4f46e5; width: 0%; border-radius: 20px'></div>
            </div>
            <div id="sa-percent" class="mt-2 fw-bold text-gray-700">0%</div>
        `,
                width: 400,
                padding: "2em",
                color: '#111827',
                timer: 2200,
                showConfirmButton: false,
                didOpen: () => {
                    let bar = document.getElementById("sa-progress-premium");
                    let percentText = document.getElementById("sa-percent");
                    let width = 0;
                    let duration = 2200;
                    let interval = duration / 100;

                    timerInterval = setInterval(() => {
                        width++;
                        bar.style.width = width + "%";
                        percentText.innerHTML = width + "%";
                        if (width >= 100) clearInterval(timerInterval);
                    }, interval);
                },
                willClose: () => clearInterval(timerInterval)
            }).then(() => {
                const container = document.querySelector(".auth-container");
                if (container) {
                    container.style.opacity = 0;
                    container.style.transition = "opacity .5s ease";
                }
                setTimeout(() => window.location.href = "{{ route('dashboard') }}", 500);
            });

            const styleDots = document.createElement('style');
            styleDots.textContent = `
        .dot-loader { width: 12px; height: 12px; background-color: #4f46e5; border-radius: 50%; animation: bounceDot 0.6s infinite alternate; }
        .dot-loader--2 { animation-delay: 0.15s; }
        .dot-loader--3 { animation-delay: 0.3s; }
        @keyframes bounceDot { 0% { transform: translateY(0); opacity: 1; } 100% { transform: translateY(-10px); opacity: 0.4; } }
        .text-accent { color: #4f46e5; }
    `;
            document.head.appendChild(styleDots);
        }
    </script>
@endpush
