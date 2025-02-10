<!doctype html>
<html lang="en">

<head>
    <title>{{ $title ?? '' }} - Futsal Booking Website</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>


    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap');

        * {
            font-family: "DM Sans", sans-serif;
            font-optical-sizing: auto;
            font-weight: <weight>;
            font-style: normal;
        }
    </style>


    @vite([])
</head>

<body>
    <main
        class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <section class="container py-5">
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <div class="pe-lg-3">
                            <a class="text-muted text-decoration-none small" href="/">
                                <i class='bx bx-arrow-back'></i>Kembali
                            </a>
                            <h1 class="display-5 fw-bold mb-2 mb-md-3">Jadilah Juara di Setiap
                                <span class="text-primary">Pertandingan!</span>
                            </h1>
                            <p class="lead mb-4">
                                Daftar atau Masuk sekarang dan buktikan bahwa Anda adalah bagian dari tim yang hebat!
                            </p>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="text-primary">
                                            <svg class="bi bi-chat-right-fill" fill="currentColor" height="32"
                                                viewbox="0 0 16 16" width="32" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M14 0a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-0">24/7</h6>
                                        <p>Aksen Penuh</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex">
                                    <div class="text-primary">
                                        <svg class="bi bi-shield-fill-check" fill="currentColor" height="32"
                                            viewbox="0 0 16 16" width="32" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm2.146 5.146a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647z"
                                                fill-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-0">Jadwal</h6>
                                        <p>Waktu Bermain</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="ps-lg-5">
                            <div class="card shadow-lg border border-primary text-white text-left h-100">
                                <div class="card-body bg-primary p-4 p-xl-5">

                                    {{ $slot }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="pt-5">
        <div class="container">
            <div class="row justify-content-center text-center align-items-center">

                <div class="col-12 col-md-12 col-xxl-6 px-0 ">
                    <div class="mb-4">
                        <p class="lead">{{ $setting->name ?? '' }}, penyedia lapangan futsal terbaik
                            dengan lebih
                            dari banyak pelanggan puas. Kami berkomitmen untuk memberikan layanan
                            terbaik dan pengalaman bermain yang menyenangkan.</p>
                    </div>
                    <nav class="nav nav-footer justify-content-center">
                        <a class="nav-link" href="/">Beranda</a>
                        <span class="my-2 vr opacity-50">
                        </span>

                        <a class="nav-link" href="/#fields">Lapangan</a>
                        <span class="my-2 vr opacity-50">
                        </span>

                        @auth
                            <a class="nav-link" href="{{ route('bookings.index') }}">Riwayat</a>
                            <span class="my-2 vr opacity-50">
                            </span>

                            <a class="nav-link" href="{{ route('profile.guest') }}">Profil</a>
                            <span class="my-2 vr opacity-50">
                            </span>
                        @else
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                            <span class="my-2 vr opacity-50">
                            </span>

                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                            <span class="my-2 vr opacity-50">
                            </span>
                        @endauth
                    </nav>
                </div>
            </div>
            <!-- Desc -->
            <hr class="mt-6 mb-3">
            <div class="row align-items-center">
                <!-- Desc -->
                <div class="col-lg-3 col-md-6 col-12">
                    <span>© <span id="copyright4">
                            <script>
                                document.getElementById('copyright4').appendChild(document.createTextNode(new Date().getFullYear()))
                            </script>
                        </span> {{ $setting->name ?? '' }}.</span>
                </div>

                <!-- Links -->
                <div class="col-12 col-md-6 col-lg-7 d-lg-flex justify-content-center">
                    <nav class="nav nav-footer">
                        <a class="nav-link ps-0" href="#">Kebijakan Privasi</a>
                        <a class="nav-link px-2 px-md-0" href="#">Pemberitahuan Cookie</a>
                        <a class="nav-link" href="#">Syarat dan Ketentuan</a>
                    </nav>
                </div>
                <div class="col-lg-2 col-md-12 col-12 d-lg-flex justify-content-end">
                    <div class="">
                        <!--Facebook-->
                        <a href="#" class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-facebook" viewBox="0 0 16 16">
                                <path
                                    d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z" />
                            </svg>
                        </a>
                        <!--Twitter-->
                        <a href="#" class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16">
                                <path
                                    d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z" />
                            </svg>
                        </a>

                        <!--GitHub-->
                        <a href="#" class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-github" viewBox="0 0 16 16">
                                <path
                                    d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>
</body>

</html>
