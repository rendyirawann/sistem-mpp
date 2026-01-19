@extends('auth.app')
@section('title', 'Login')
@section('content')
    <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">

        <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10 shadow-lg">

            <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">

                <div class="d-flex flex-center flex-column flex-column-fluid mb-2">
                    <img alt="Logo" class="theme-light-show h-40px h-lg-150px"
                        src="{{ asset('assets/media/logos/mpp_login.png') }}" />
                    <img alt="Logo" class="theme-dark-show h-40px h-lg-150px"
                        src="{{ asset('assets/media/logos/mpp_login.png') }}" />
                </div>

                <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20 my-12">



                    <form class="form w-100" id="kt_sign_in_form" method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="fv-row mb-8">
                            <input type="text" placeholder="Email atau No WA" name="email" autocomplete="off"
                                class="form-control bg-transparent" />
                        </div>

                        <div class="fv-row mb-3">
                            <input type="password" placeholder="Password" name="password" autocomplete="off"
                                class="form-control bg-transparent" />
                        </div>

                        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                            <div></div>
                            <a href="{{ route('password.request') }}" class="link-primary">Forgot Password ?</a>
                        </div>

                        <div class="d-grid mb-10">
                            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                <span class="indicator-label">Sign In</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>

                    </form>

                </div>

                <div class="d-flex flex-stack">
                    <div class="me-10">
                        <span class="text-muted fw-semibold me-1">{{ date('Y') }}</span>
                        <a class="text-gray-800 text-hover-primary">&copy; MPP Deli Serdang</a>
                    </div>
                    <div class="d-flex fw-semibold text-muted fs-base gap-5">
                        <span class="px-2">No version available</span>
                    </div>
                </div>

            </div>
        </div>
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
                    console.log("RESULT:", result);

                    if (!response.ok) {

                        // === Jika Lockout 3x ===
                        if (
                            result.errors &&
                            result.errors.email &&
                            Array.isArray(result.errors.email) &&
                            result.errors.email[0].includes("Terlalu banyak percobaan")
                        ) {

                            let msg = result.errors.email[0];
                            let seconds = msg.match(/\d+/)[0];

                            showLockoutCountdown(seconds);

                            submitButton.classList.remove("disabled");
                            submitButton.querySelector('.indicator-label').style.display = 'block';
                            submitButton.querySelector('.indicator-progress').style.display = 'none';
                            return;
                        }

                        // === Semua error login lain: SALAH PASSWORD / AKUN DI-BAN / DLL ===
                        if (result.errors && result.errors.email) {
                            Swal.fire({
                                icon: "error",
                                title: "Login Gagal!",
                                html: result.errors.email[0], // backend HTML preserved
                                confirmButtonColor: "#d33"
                            });

                            submitButton.classList.remove("disabled");
                            submitButton.querySelector('.indicator-label').style.display = 'block';
                            submitButton.querySelector('.indicator-progress').style.display = 'none';
                            return;
                        }
                    }



                    // SUCCESS → tampilkan 3 DOT LOADER
                    superPremiumThreeDotLoader();
                })
                .catch(err => console.error(err));
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
            <div class="progress bg-secondary" style="height: 10px; border-radius: 20px;">
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

                        // PROGRESS BAR (reverse)
                        let percent = 100 - Math.floor((seconds / originalSeconds) * 100);
                        bar.style.width = percent + "%";

                        if (seconds <= 0) {
                            clearInterval(interval);
                        }
                    }, 1000);
                }
            });
        }





        // =============================
        // SUPER PREMIUM 3 DOT LOADER
        // =============================
        function superPremiumThreeDotLoader() {
            let timerInterval;

            Swal.fire({
                icon: "success",
                title: `<span class="fw-bold">Login Berhasil</span>`,
                html: `
            <div class="text-muted mb-3">Menyiapkan aplikasi untuk Anda...</div>

            <!-- Triple Dot Loader -->
            <div class="my-12" style="display:flex; justify-content:center; align-items:center; gap:10px; margin-bottom:22px;">
                <div class="dot-loader"></div>
                <div class="dot-loader dot-loader--2"></div>
                <div class="dot-loader dot-loader--3"></div>
            </div>

            <!-- Progress Bar -->
            <div class='progress bg-secondary mt-3'
                 style='height: 12px; border-radius: 20px; width: 100%; overflow: hidden;'>
                <div id="sa-progress-premium"
                     class='progress-bar bg-success'
                     style='width: 0%; border-radius: 20px'></div>
            </div>

            <!-- Percentage -->
            <div id="sa-percent" class="mt-2 fw-bold text-gray-700">0%</div>
        `,
                width: 400,
                padding: "2em",
                // background: '#ffffff',
                color: '#000',
                timer: 2200,
                showConfirmButton: false,

                didOpen: () => {
                    // Blur background
                    //document.body.style.filter = "blur(2px)";
                    document.body.style.transition = "filter .4s";

                    // Animate progress
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

                willClose: () => {
                    clearInterval(timerInterval);
                    document.body.style.filter = "blur(0)";
                }
            }).then(() => {
                const container = document.querySelector(".d-flex.flex-column-fluid");
                if (container) {
                    container.style.opacity = 0;
                    container.style.transition = "opacity .5s ease-in-out";
                }
                setTimeout(() => {
                    window.location.href = "{{ route('dashboard') }}";
                }, 500);
            });


            // ADD DOT LOADER STYLES
            const styleDots = document.createElement('style');
            styleDots.textContent = `
        .dot-loader {
            width: 12px;
            height: 12px;
            background-color: #22c55e;
            border-radius: 50%;
            animation: bounceDot 0.6s infinite alternate;
        }
        .dot-loader--2 { animation-delay: 0.15s; }
        .dot-loader--3 { animation-delay: 0.3s; }

        @keyframes bounceDot {
            0% { transform: translateY(0); opacity: 1; }
            100% { transform: translateY(-10px); opacity: 0.4; }
        }
    `;
            document.head.appendChild(styleDots);
        }
    </script>
@endpush
