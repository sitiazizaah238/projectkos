<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>FindKos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/images/logo2.png">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #a2c5fd 0%, #e8f1f9 50%, #82a2f3 100%);
            font-family: 'Segoe UI', sans-serif;
        }

        /* NAVBAR */
        .navbar-custom {
            background: linear-gradient(90deg, #2f80ed, #8dd4fa);
            padding: 12px 0;
        }

        .navbar-brand img {
            height: 40px;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .search-box input {
            border-radius: 12px;
            padding: 12px 15px;
        }

        .nav-link i {
            font-size: 14px;
        }

        /* HERO */
        .hero {
            padding: 80px 0;
        }

        @media (min-width: 992px) {
            .hero-text {
                margin-top: -30px;
            }
        }

        .hero-title {
            font-size: 36px;
            font-weight: 700;
            color: #2b2b6b;
        }

        .hero-title span {
            color: #3f51b5;
        }

        .hero-sub {
            margin: 20px 0;
            color: #151414;
        }

        .search-box input {
            border-radius: 10px;
            padding: 12px;
        }

        .btn-primary {
            background-color: #3f8efc;
            border: none;
            border-radius: 8px;
        }

        /* SECTION CARA KERJA */
        .section-title {
            font-weight: 700;
            color: #2b2b6b;
            margin-bottom: 50px;
        }

        .card-step {
            background: white;
            border-radius: 15px;
            padding: 15px 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .card-step:hover {
            transform: translateY(-5px);
        }

        .card-step img {
            height: 90px;
            margin-bottom: 15px;
        }

        /* CTA */
        .cta-section {
            background-color: #dbe9ff;
            padding: 60px 0;
            margin-top: 70px;
        }

        footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }

        /* HERO BACKGROUND  */
        .hero {
            position: relative;
            min-height: 532px;
            padding: 90px 0 80px 0;

            background-image: url("{{ asset('images/hero.png') }}");
            background-repeat: no-repeat;
            background-position: right 53%;
            background-size: contain;
        }

        @media (min-width: 992px) {
            .hero {
                background-position: right 75%;
            }
        }

        /* supaya teks tidak terlalu lebar */
        .hero-content {
            max-width: 550px;
        }

        /* judul */
        .hero-title {
            font-size: 44px;
            font-weight: 700;
            color: #2b2b6b;
            line-height: 1.2;
        }

        .hero-title span {
            color: #3f51b5;
        }

        /* sub */
        .hero-sub {
            margin: 20px 0 25px;
            color: #555;
            font-size: 17px;
        }

        /* search */
        .search-box input {
            height: 45px;
            border-radius: 12px;
            padding-left: 18px;
        }

        /* tombol */
        .hero-buttons {
            display: flex;
            gap: 14px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Responsiveness Mobile */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: linear-gradient(90deg, #2f80ed, #56ccf2);
                border-radius: 12px;
                padding: 10px;
                margin-top: 10px;
            }

            .nav-link {
                padding: 10px 15px !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .nav-item:last-child .nav-link {
                border-bottom: none;
            }
        }

        @media (max-width: 767px) {
            .hero {
                padding: 80px 0;
                min-height: auto;
            }

            .hero {
                padding-top: 40px;
                /* sebelumnya 90px, ini bikin naik */
                padding-bottom: 60px;
            }

            .hero-text {
                margin-top: -20px;
                /* dorong teks naik dikit */
            }


            .hero-title {
                font-size: 32px;
            }

            .search-box.w-75 {
                width: 100% !important;
            }

            .card-step img {
                height: 70px;
            }

            .cta-section {
                padding: 40px 0;
            }

            #tentang>.row.align-items-center {
                flex-direction: column-reverse;
            }

            #tentang .section-title {
                text-align: center !important;
            }

            #tentang p {
                text-align: center !important;
            }

            #tentang img {
                margin-bottom: 20px;
            }

            /* 🔥 INI YANG DIUBAH */
            #cara-kerja {
                margin-top: -80px;
                /* tarik lebih ke atas */
                position: relative;
                z-index: 2;
            }

            #cara-kerja .section-title {
                margin-top: -75px !important;

                margin-bottom: 20px;
            }

            #cara-kerja .row {
                margin-top: -20px;
            }
        }

        /* ================= ANIMATION GLOBAL ================= */

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Fade Up Animation */
        .fade-up {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s ease;
        }

        .fade-up.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Fade In */
        .fade-in {
            opacity: 0;
            transition: opacity 1s ease;
        }

        .fade-in.show {
            opacity: 1;
        }

        /* Zoom */
        .zoom-in {
            transform: scale(0.9);
            opacity: 0;
            transition: all 0.6s ease;
        }

        .zoom-in.show {
            transform: scale(1);
            opacity: 1;
        }

        /* Navbar hover efek */
        .nav-link {
            position: relative;
            transition: 0.3s;
        }

        .nav-link::after {
            content: "";
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0%;
            height: 2px;
            background: white;
            transition: 0.3s;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Button hover */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        /* Hero animation */
        .hero-title {
            animation: fadeSlide 1s ease forwards;
        }

        .hero-sub {
            animation: fadeSlide 1.2s ease forwards;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modal animation */
        [x-cloak] {
            display: none !important;
        }

        .modal-animate {
            animation: zoomFade 0.3s ease;
        }

        @keyframes zoomFade {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body x-data="{ openModal: false }">


    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#" style="height: 50px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                    style="height: 60px; width: auto; object-fit: contain;">

                <span
                    style="
                margin-left: 10px;
                font-family: 'Poppins', sans-serif;
                font-weight: 700;
                font-size: 20px;
                letter-spacing: 0.5px;
                color: white;
            ">
                    FindKos
                </span>
            </a>

            <!-- Hamburger Button -->
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-2 py-2 py-lg-0">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="#home">
                            <i class="bi bi-house-door"></i>
                            <span>Beranda</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="#tentang">
                            <i class="bi bi-building"></i>
                            <span>Tentang Findkos</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="#cara-kerja">
                            <i class="bi bi-gear"></i>
                            <span>Cara Kerja</span>
                        </a>
                    </li>


                    {{-- KONTAK --}}
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="{{ route('kontak') }}">
                            <i class="bi bi-telephone"></i>
                            <span>Kontak</span>
                        </a>
                    </li>
                </ul>

            </div>
        </div>
    </nav>
    <!-- MODAL REGISTER -->
    <template x-if="openModal">
        <div class="position-fixed top-0 start-0 w-100 vh-100 d-flex align-items-center justify-content-center"
            style="background: rgba(15,23,42,0.65); backdrop-filter: blur(6px); z-index:9999;">

            <div class="bg-white rounded-4 shadow-lg p-5 position-relative modal-animate">

                <!-- Close Button -->
                <button type="button" @click="openModal = false"
                    class="btn btn-light position-absolute top-0 end-0 m-3 rounded-circle shadow-sm"
                    style="width:35px; height:35px;">
                    <i class="bi bi-x-lg"></i>
                </button>

                <!-- Icon -->
                <div class="text-center mb-3">
                    <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow"
                        style="width:70px; height:70px;">
                        <i class="bi bi-person-plus-fill fs-4"></i>
                    </div>
                </div>

                <!-- Title -->
                <h4 class="text-center fw-bold mb-2">
                    Bergabung dengan FindKos
                </h4>

                <p class="text-center text-muted mb-4" style="font-size:14px;">
                    Pilih tipe akun yang sesuai dengan kebutuhan Anda
                </p>

                <!-- Buttons -->
                <div class="d-grid gap-3">

                    <a href="{{ route('register.pemilik') }}" class="btn btn-primary rounded-3 fw-semibold shadow-sm">
                        <i class="bi bi-building me-2"></i>
                        Daftar sebagai Pemilik
                    </a>

                    <a href="{{ route('register.penyewa') }}" class="btn btn-outline-primary rounded-3 fw-semibold">
                        <i class="bi bi-person me-2"></i>
                        Daftar sebagai Penyewa
                    </a>

                </div>

            </div>
        </div>
    </template>

    <!-- HERO -->
    <section id="home" class="hero">
        <div class="container">
            <div class="row align-items-end">
                <div class="col-md-6 hero-text fade-up">
                   <h1 class="hero-title mb-5">
                        Temukan Kos Terbaik <br>
                        di <span>Lohbener</span> dengan Mudah
                    </h1>
                    <p class="hero-sub mobile-sub text-dark mt-4 mt-md-5">
                        Sistem Rekomendasi Kos Berbasis Web <br>
                        di Kecamatan Lohbener, Indramayu
                    </p>

                    <div class="search-box mb-3 position-relative w-75">
                        <input type="text" class="form-control" placeholder="Cari Kos di Lohbener..." maxlength="39">
                        <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                    </div>
                    <div class="hero-buttons">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            Cari Kos
                        </a>
                        <button @click="openModal = true" class="btn btn-outline-primary">
                            <i class="bi bi-person-plus me-1"></i> Daftar
                        </button>

                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                    </div>
                </div>


            </div>
        </div>
    </section>

    <!-- CARA KERJA -->
    <section id="cara-kerja" class="container text-center mt-5">

        <h3 class="section-title">Cara Kerja Sistem</h3>

        <div class="row text-center justify-content-center">

            <div class="col-md-3 col-sm-6 mb-4 d-flex">
                <div class="card-step w-100 fade-up">
                    <img src="{{ asset('images/step1.png') }}" class="img-fluid" alt="">
                    <h5>1. Daftar Akun</h5>
                    <p>Buat akun Anda terlebih dahulu</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4 d-flex">
                <div class="card-step w-100 fade-up">
                    <img src="{{ asset('images/step2.png') }}" class="img-fluid" alt="">
                    <h5>2. Isi Preferensi</h5>
                    <p>Pilih kriteria kos sesuai kebutuhan</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4 d-flex">
                <div class="card-step w-100 fade-up">
                    <img src="{{ asset('images/step3.png') }}" class="img-fluid" alt="">
                    <h5>3. Dapat Rekomendasi</h5>
                    <p>Temukan kos terbaik untuk Anda</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4 d-flex">
                <div class="card-step w-100 fade-up">
                    <img src="{{ asset('images/step4.png') }}" class="img-fluid" alt="">
                    <h5>4. Pilih & Sewa Kos</h5>
                    <p>Pilih kos dan lakukan proses sewa</p>
                </div>
            </div>

        </div>
    </section>

    <!-- TENTANG WEBSITE -->
    <section id="tentang" class="container mt-5 mb-5">
        <div class="row align-items-center">
            <div class="col-md-6 fade-left">

                <h3 class="section-title text-start mb-3">
                    Tentang <span style="color:#3f51b5;">FindKos</span>
                </h3>

                <p
                    style="
        color:#555;
        line-height:1.9;
        text-align:justify;
        font-size:15px;
    ">
                    <strong>FindKos</strong> merupakan sistem informasi pencarian dan
                    rekomendasi kamar kos berbasis web yang dirancang untuk membantu
                    mahasiswa, pekerja, maupun masyarakat umum dalam menemukan tempat
                    tinggal yang nyaman dan sesuai kebutuhan di wilayah Lohbener Indramayu.
                </p>

                <p
                    style="
        color:#555;
        line-height:1.9;
        text-align:justify;
        font-size:15px;
    ">
                    Dengan memanfaatkan sistem rekomendasi berbasis preferensi pengguna,
                    FindKos mampu memberikan saran kos terbaik berdasarkan harga,
                    fasilitas, lokasi, serta tingkat kenyamanan sehingga proses pencarian
                    menjadi lebih cepat, praktis, dan efisien.
                </p>

                <!-- FITUR MINI -->
                <div class="row mt-4 g-2">

                    <div class="col-6">
                        <div class="bg-white rounded-4 shadow-sm p-2 h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-house-check-fill text-primary"></i>
                                <h6 class="mb-0 fw-bold" style="font-size:14px;">Kos Terverifikasi</h6>
                            </div>

                            <small class="text-muted" style="font-size:12px;">
                                Menampilkan informasi kos yang lebih jelas dan terpercaya.
                            </small>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="bg-white rounded-4 shadow-sm p-3 h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-geo-alt-fill text-primary"></i>
                                <h6 class="mb-0 fw-bold">Lokasi Strategis</h6>
                            </div>

                            <small class="text-muted">
                                Membantu pengguna menemukan kos di area Lohbener.
                            </small>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="bg-white rounded-4 shadow-sm p-3 h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-stars text-primary"></i>
                                <h6 class="mb-0 fw-bold">Rekomendasi Pintar</h6>
                            </div>

                            <small class="text-muted">
                                Sistem memberikan saran kos sesuai preferensi pengguna.
                            </small>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="bg-white rounded-4 shadow-sm p-3 h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-phone-fill text-primary"></i>
                                <h6 class="mb-0 fw-bold">Responsive</h6>
                            </div>

                            <small class="text-muted">
                                Tampilan nyaman digunakan di mobile maupun desktop.
                            </small>
                        </div>
                    </div>

                </div>

            </div>
            <div class="col-md-6 text-center fade-up">
                <img src="{{ asset('images/kos.png') }}" class="img-fluid" style="max-height:450px;"
                    alt="Tentang FindKos">
            </div>
        </div>
    </section>
    <!-- CTA -->
    <section class="py-4 text-center text-white fade-in"
        style="background: linear-gradient(135deg, #4e8ad8, #6ea0ea);">
        <div class="container">

            <h2 class="fw-bold mb-3">
                Temukan Kos Impianmu Sekarang!
            </h2>

            <p class="mb-4">
                Mudah, cepat, dan sesuai preferensimu hanya di FindKos.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap">

                <button @click="openModal = true" class="btn btn-light px-4 fw-semibold shadow">
                    Daftar Sekarang
                </button>

                <a href="{{ route('login') }}" class="btn btn-outline-light px-4 fw-semibold">
                    Login
                </a>

            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-3">
        © {{ date('Y') }} <strong>FindKos</strong> | Sistem Penyewaan Kamar Kos berbasis AI
    </footer>
    <script>
        // Reveal on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                }
            });
        });

        document.querySelectorAll('.fade-up, .fade-in, .zoom-in').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>

</html>
