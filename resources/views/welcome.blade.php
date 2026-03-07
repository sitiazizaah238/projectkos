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
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background-color: #eef4fb;
            font-family: 'Segoe UI', sans-serif;
        }

        /* NAVBAR */
        .navbar-custom {
            background: linear-gradient(90deg, #2f80ed, #56ccf2);
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
            color: #555;
        }

        .search-box input {
            border-radius: 10px;
            padding: 12px;
        }

        .btn-primary {
            background-color: #3f8efc;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
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
            padding: 30px;
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

        /* HERO BACKGROUND FIGMA */
        .hero {
            position: relative;
            min-height: 560px;
            padding: 120px 0 80px 0;

            background-image: url("{{ asset('images/hero.png') }}");
            background-repeat: no-repeat;
            background-position: right bottom;
            background-size: contain;
        }

        /* supaya teks tidak terlalu lebar */
        .hero-content {
            max-width: 540px;
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
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="{ openModal: false }">


    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <span class="ms-1 text-white fw-bold">FindKos</span>
            </a>

            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="#home">
                            <i class="bi bi-house-door"></i>
                            <span>Beranda</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="#tentang">
                            <i class="bi bi-building"></i>
                            <span>Tentang</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="#cara-kerja">
                            <i class="bi bi-gear"></i>
                            <span>Cara Kerja</span>
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

            <div class="bg-white rounded-4 shadow-lg p-5 position-relative" style="width:420px; max-width:95%;">

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

                    <a href="{{ route('register.pemilik') }}"
                        class="btn btn-primary btn-lg rounded-3 fw-semibold shadow-sm">
                        <i class="bi bi-building me-2"></i>
                        Daftar sebagai Pemilik
                    </a>

                    <a href="{{ route('register.penyewa') }}"
                        class="btn btn-outline-primary btn-lg rounded-3 fw-semibold">
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
                <div class="col-md-6 hero-text">
                    <h1 class="hero-title">
                        Temukan Kos Terbaik <br>
                        di <span>Lohbener</span> dengan Mudah
                    </h1>

                    <p class="hero-sub">
                        Sistem Rekomendasi Kos berbasis Web di wilayah Lohbener Indramayu
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
                            Daftar
                        </button>

                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            Login
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

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card-step">
                    <img src="{{ asset('images/step1.png') }}" class="img-fluid step-img" alt="">
                    <h5>1. Daftar Akun</h5>
                    <p>Buat akun Anda terlebih dahulu</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card-step">
                    <img src="{{ asset('images/step2.png') }}" class="img-fluid step-img" alt="">
                    <h5>2. Isi Preferensi</h5>
                    <p>Pilih kriteria kos sesuai kebutuhan</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card-step">
                    <img src="{{ asset('images/step3.png') }}" class="img-fluid step-img" alt="">
                    <h5>3. Dapat Rekomendasi</h5>
                    <p>Temukan kos terbaik untuk Anda</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card-step">
                    <img src="{{ asset('images/step4.png') }}" class="img-fluid step-img" alt="">
                    <h5>4. Pilih & Sewa Kos</h5>
                    <p>Pilih kos dan lakukan proses sewa</p>
                </div>
            </div>

        </div>
    </section>

    <!-- TENTANG WEBSITE -->
    <section id="tentang" class="container mt-5 mb-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="section-title text-start">Tentang FindKos</h3>
                <p style="text-align: justify; color:#555;">
                    <strong>FindKos</strong> merupakan sistem informasi pencarian dan rekomendasi
                    kamar kos berbasis web yang dirancang untuk membantu masyarakat, khususnya
                    mahasiswa dan pekerja di wilayah Lohbener Indramayu, dalam menemukan kos
                    yang sesuai dengan kebutuhan dan preferensi mereka.
                </p>

                <p style="text-align: justify; color:#555;">
                    Sistem ini menerapkan metode rekomendasi berbasis preferensi pengguna
                    sehingga mampu memberikan saran kos terbaik berdasarkan kriteria seperti
                    harga, fasilitas, lokasi, dan kenyamanan. Dengan tampilan yang sederhana
                    dan mudah digunakan, FindKos bertujuan meningkatkan efisiensi dalam proses
                    pencarian serta pemesanan kamar kos secara digital.
                </p>
            </div>

            <div class="col-md-6 text-center">
                <img src="{{ asset('images/kos.png') }}" class="img-fluid" style="max-height:350px;"
                    alt="Tentang FindKos">
            </div>
        </div>
    </section>
    <!-- CTA -->
    <section class="py-5 text-center bg-primary bg-gradient text-white">
        <div class="container">

            <h2 class="fw-bold mb-3">
                Temukan Kos Impianmu Sekarang!
            </h2>

            <p class="mb-4">
                Mudah, cepat, dan sesuai preferensimu hanya di FindKos.
            </p>

            <div class="d-flex justify-content-center gap-3">

                <button @click="openModal = true" class="btn btn-light btn-lg px-4 fw-semibold shadow">
                    🚀 Daftar Sekarang
                </button>

                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4 fw-semibold">
                    🔐 Login
                </a>

            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-3">
        © {{ date('Y') }} <strong>FindKos</strong> | Sistem Penyewaan Kamar Kos berbasis AI
    </footer>

</body>

</html>
