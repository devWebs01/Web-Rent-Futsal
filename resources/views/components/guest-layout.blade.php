<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>{{ $title ?? "" }} | Alpama Futsal</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Inter:slnt,wght@-10..0,100..900&display=swap"
            rel="stylesheet">

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link rel="stylesheet" href="/guest/lib/animate/animate.min.css" />
        <link href="/guest/lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="/guest/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

        <!-- Customized Bootstrap Stylesheet -->
        <link href="/guest/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="/guest/css/style.css" rel="stylesheet">

        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

        @livewireStyles

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                font-family: "Inter", sans-serif;
                font-optical-sizing: auto;
                font-weight: 700;
                font-style: normal;
            }

            body {
                font-family: "Inter", sans-serif;
                font-optical-sizing: auto;
                font-weight: 400;
                font-style: normal;
            }

            .blog-item {
                display: flex;
                flex-direction: column;
                height: 100%;
                /* Tinggi sama semua */
            }

            .blog-content {
                flex-grow: 1;
                /* Biarkan isi berkembang */
                display: flex;
                flex-direction: column;
            }

            .blog-comment {
                flex-shrink: 0;
                /* Pastikan tetap terlihat */
            }

            .blog-content a.h4 {
                flex-grow: 1;
                /* Biarkan judul menyesuaikan */
                display: -webkit-box;
                -webkit-line-clamp: 2;
                /* Batasi 2 baris */
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .blog-content .btn {
                align-self: flex-start;
                /* Biarkan tombol sejajar */
                margin-top: auto;
                /* Paksa tombol turun ke bawah */
            }
        </style>
        @stack("styles")

    </head>

    <body>

        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <x-guest-nav>

        </x-guest-nav>

        <!-- Navbar & Hero Start -->
        <div class="py-5 my-5">

            @if (session("error"))
                <div class="container-fluid">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">

                        </button>
                        {{ session("error") }}
                    </div>
                </div>
            @endif

            {{ $slot }}
        </div>
        <!-- Navbar & Hero End -->

        <!-- footer -->
        <footer class="py-5 d-print-none">
            <div class="container-fluid px-5">
                <div class="row justify-content-center text-center align-items-center">

                    <div class="col-12 col-md-12 col-xxl-6 px-0 ">
                        <div class="mb-4">
                            <p class="lead">{{ $setting->name ?? "" }}, penyedia lapangan futsal terbaik
                                dengan lebih
                                dari banyak pelanggan puas. Kami berkomitmen untuk memberikan layanan
                                terbaik dan pengalaman bermain yang menyenangkan.</p>
                        </div>

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
                            </span> {{ $setting->name ?? "" }}.</span>
                    </div>

                    <!-- Links -->
                    <div class="col-12 col-md-6 col-lg-7 d-lg-flex justify-content-center">

                    </div>
                    <div class="col-lg-2 col-md-12 col-12 d-lg-flex justify-content-end">
                        <div class="">
                            <!--Facebook-->
                            <a href="#" class="me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
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

        <!-- Back to Top -->
        <a href="#" class="btn btn-primary btn-lg-square rounded-circle back-to-top">
            <i class="fa fa-arrow-up">
            </i>
        </a>

        <!-- JavaScript Libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/guest/lib/wow/wow.min.js"></script>
        <script src="/guest/lib/easing/easing.min.js"></script>
        <script src="/guest/lib/waypoints/waypoints.min.js"></script>
        <script src="/guest/lib/counterup/counterup.min.js"></script>
        <script src="/guest/lib/lightbox/js/lightbox.min.js"></script>
        <script src="/guest/lib/owlcarousel/owl.carousel.min.js"></script>

        <!-- Template Javascript -->
        <script src="/guest/js/main.js"></script>

        @stack("scripts")
        @livewireScripts

        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <x-livewire-alert::scripts />
    </body>

</html>
