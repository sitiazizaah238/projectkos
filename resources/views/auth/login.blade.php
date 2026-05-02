<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - FindKos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo2.png') }}" type="image/png">
    <!-- Google Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- AlpineJS -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css'])

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .auth-shell {
            display: flex;
            min-height: 100vh;
        }

        .auth-panel-left,
        .auth-panel-right {
            width: 50%;
        }

        .auth-panel-left {
            background: linear-gradient(to bottom, #3b82f6, #1d4ed8);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 5rem;
            position: relative;
        }

        .auth-logo {
            position: absolute;
            top: 10px;
            left: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 22px;
        }

        .auth-logo img {
            width: 40px;
            height: auto;
        }

        .auth-left-content {
            margin-top: 80px;
        }

        .auth-left-content img {
            width: min(100%, 420px);
            display: block;
            margin-bottom: 30px;
        }

        .auth-left-title {
            font-size: clamp(28px, 4vw, 45px);
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .auth-left-copy {
            font-size: clamp(14px, 1.7vw, 18px);
            line-height: 1.7;
        }

        .auth-panel-right {
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-card {
            width: min(100%, 420px);
            background: #fff;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .auth-modal {
            width: min(100%, 420px);
            padding: 2.5rem;
            border-radius: 1.5rem;
        }

        .auth-input,
        .auth-button,
        .auth-modal-link {
            border-radius: 9999px;
        }

        .auth-button {
            font-size: 1rem;
        }

        @media (max-width: 1024px) {

            .auth-panel-left,
            .auth-panel-right {
                width: 100%;
            }

            .auth-panel-left {
                padding: 5rem 2rem 3rem;
                align-items: center;
                text-align: center;
            }

            .auth-logo {
                left: 20px;
                transform: none;
            }

            .auth-left-content {
                margin-top: 4rem;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .auth-panel-right {
                padding: 2rem 1.25rem 2.5rem;
            }
        }

        @media (max-width: 640px) {
            .auth-shell {
                flex-direction: column;
            }

            .auth-panel-left {
                padding: 5rem 1.25rem 2rem;
                min-height: auto;
            }

            .auth-logo {
                top: 14px;
                font-size: 18px;
            }

            .auth-logo img {
                width: 34px;
            }

            .auth-left-content img {
                width: min(100%, 260px);
                margin-bottom: 20px;
            }

            .auth-card {
                padding: 1.5rem;
                border-radius: 1.25rem;
            }

            .auth-modal {
                padding: 1.5rem;
                border-radius: 1.25rem;
            }

            .auth-modal h2 {
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
            }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="{ openModal: false }">

    <div class="auth-shell">

        <!-- LEFT SIDE -->
        <div class="auth-panel-left">

            <!-- Logo (pakai gambar + link) -->
            <div class="auth-logo">
                <img src="{{ asset('images/logo.png') }}">
                <span>FindKos</span>
            </div>

            <!-- Image & Text -->
            <div class="auth-left-content">
                <img src="{{ asset('images/kos.png') }}">

            </div>
            <h2 class="auth-left-title">
                Selamat Datang Kembali!
            </h2>

            <p class="auth-left-copy text-blue-100">
                Masuk Untuk Mengakses Dashboard <br>
                Anda dan Kelola Akun Dengan Mudah
            </p>


        </div>

        <!-- RIGHT SIDE -->
        <div class="auth-panel-right">

            <div class="auth-card">

                <!-- TITLE -->
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Masuk ke Akun Anda
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Masukkan Email dan Password Untuk Melanjutkan
                    </p>
                </div>
                @if (session('error'))
                    <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- EMAIL -->
                    <div class="mb-6">
                        <label class="block mb-2 text-gray-700 font-medium">Email</label>

                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="auth-input w-full px-5 py-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="Masukan Email..">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                    <div class="mb-6">
                        <label class="block mb-2 text-gray-700 font-medium">
                            Password
                        </label>

                        <div class="relative flex items-center">
                            <input type="password" name="password" id="password" required
                                class="auth-input w-full px-5 py-3 pr-12 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                placeholder="Masukan Password..">

                            <button type="button" onclick="togglePassword(this)"
                                class="absolute right-4 text-gray-400 hover:text-gray-600 text-lg">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror


                    <!-- LOGIN BUTTON -->
                    <button type="submit"
                        class="auth-button w-full py-3 bg-cyan-500 hover:bg-cyan-600 text-white font-semibold text-lg transition">
                        Masuk
                    </button>
                    <div class="text-right mb-4">
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-500 hover:underline">
                            Lupa Password?
                        </a>
                    </div>
                    <!-- REGISTER -->
                    <p class="text-center mt-5 text-sm text-gray-600">
                        Tidak Memiliki Akun?
                        <button type="button" @click="openModal = true"
                            class="text-blue-600 font-semibold hover:underline">
                            Register
                        </button>
                    </p>

                </form>

            </div>

        </div>

    </div>
    <!-- MODAL -->
    <div x-show="openModal" x-transition x-cloak class="fixed inset-0 z-50 flex items-center justify-center">

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="openModal = false"></div>

        <!-- Modal Box -->
        <div class="relative bg-white auth-modal shadow-2xl text-center z-50">

            <!-- Close -->
            <button @click="openModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-black text-xl">
                ✕
            </button>

            <h2 class="text-2xl font-bold mb-8">Daftar Sebagai</h2>

            <div class="space-y-6">

                <a href="{{ route('register.pemilik') }}"
                    class="auth-modal-link block w-full py-4 bg-blue-500 hover:bg-blue-600 text-white text-xl font-semibold transition">
                    Sebagai Pemilik
                </a>

                <a href="{{ route('register.penyewa') }}"
                    class="auth-modal-link block w-full py-4 bg-green-500 hover:bg-green-600 text-white text-xl font-semibold transition">
                    Sebagai Penyewa
                </a>

            </div>

        </div>
    </div>
    <script>
        function togglePassword(el) {
            const input = document.getElementById("password");
            const icon = el.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("bi-eye", "bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("bi-eye-slash", "bi-eye");
            }
        }
    </script>


</body>

</html>
