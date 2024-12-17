<!doctype html>
<html lang="en">

<head>
    <title>
        {{ $title ?? '' }}
    </title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

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

        #top a,
        .nav-link {
            color: black;

        }

        #top a:hover,
        .nav-link:hover {
            color: rgb(220, 53, 69);
            /* Warna link saat hover */
        }

        .nav-pills .nav-link.active,
        .nav-pills .show>.nav-link {
            color: var(--bs-nav-pills-link-active-color);
            background-color: rgb(220, 53, 69);
        }
    </style>

    @livewireStyles

    @stack('css')

    @vite([])
</head>

<body>
    <header id="top" class="position-sticky z-3 d-print-none">
        <nav class="navbar bg-white pt-3">
            <div class="container">
                <div class="d-flex align-items-center g-4">
                    <a class="navbar-brand d-flex" href="/">
                        <h2 class="fw-bold">{{ $setting ?? 'Testing' }}</h2>
                    </a>
                </div>

                <x-guest-nav></x-guest-nav>

                @auth
                    <x-guest-sidebar></x-guest-sidebar>
                @else
                    <a class="btn btn-dark text-white" href="/login" role="button">Masuk</a>
                @endauth
            </div>
        </nav>
    </header>

    {{ $slot }}


    <footer id="footer" class="bg-black text-white py-5 d-print-none">
        <div class="container-sm text-center">
            <h2 class="fw-bold">{{ $setting ?? 'Testing' }}</h2>
            <div class="row g-md-5 mb-5 text-center">
                <div class="col-12">
                    <div class="info-box">
                        <p>
                            © 2024 Futsal Arena. Semua hak dilindungi.
                            Booking lapangan futsal dengan mudah dan cepat.
                            Hubungi kami di <a href="mailto:info@futsalarena.com"
                                class="text-white text-decoration-none">info@futsalarena.com</a>
                        </p>
                    </div>
                </div>
            </div>
    </footer>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>

    @livewireScripts

    @stack('scripts')

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <x-livewire-alert::scripts />
</body>

</html>
