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
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="{ openModal: false }">

    <div class="flex min-h-screen">

        <!-- LEFT SIDE -->
        <div
            class="w-1/2 bg-gradient-to-b from-blue-500 to-blue-700 text-white flex flex-col justify-center px-20 relative">

            <!-- Logo (pakai gambar + link) -->
            <div class="logo"
                style="position:absolute; top:10px; left:40px; display:flex; align-items:center; gap:12px; font-weight:700; font-size:22px;">
                <img src="{{ asset('images/logo.png') }}" style="width:40px;">
                <span>FindKos</span>
            </div>

            <!-- Image & Text -->
            <div class="left-content" style="margin-top:80px; padding-left:0px;">
                <img src="{{ asset('images/kos.png') }}" style="width:420px; display:block; margin-bottom:30px;">

            </div>
            <h2 style="font-size:45px; font-weight:700; line-height:1.2; margin-bottom:16px;">
    Selamat Datang Kembali!
</h2>

            <p class="text-lg text-blue-100 leading-relaxed">
                Masuk Untuk Mengakses Dashboard <br>
                Anda dan Kelola Akun Dengan Mudah
            </p>


        </div>

        <!-- RIGHT SIDE -->
        <!-- RIGHT SIDE -->
        <div class="w-1/2 bg-gray-100 flex items-center justify-center">

            <div class="w-[420px] bg-white p-10 rounded-2xl shadow-sm">

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

                <form method="POST" action="{{ route('login') }}">
                    @csrf


                    <!-- EMAIL -->
                    <div class="mb-6">
                        <label class="block mb-2 text-gray-700 font-medium">Email</label>

                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-5 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="Masukan Email..">
                    </div>

                    <!-- PASSWORD -->
                    <div class="mb-6 relative">

                        <label class="block mb-2 text-gray-700 font-medium">
                            Password
                        </label>

                        <input type="password" name="password" id="password" required
                            class="w-full px-5 py-3 pr-14 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="Masukan Password..">

                        <span onclick="togglePassword(this)"
                            class="absolute right-5 top-[42px] cursor-pointer text-gray-500 text-xl">
                            <i class="bi bi-eye"></i>
                        </span>

                    </div>

                    <!-- LOGIN BUTTON -->
                    <button type="submit"
                        class="w-full py-3 rounded-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold text-lg transition">
                        Masuk
                    </button>

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
        <div class="relative bg-white w-[420px] p-10 rounded-2xl shadow-2xl text-center z-50">

            <!-- Close -->
            <button @click="openModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-black text-xl">
                ✕
            </button>

            <h2 class="text-2xl font-bold mb-8">Daftar Sebagai</h2>

            <div class="space-y-6">

                <a href="{{ route('register.pemilik') }}"
                    class="block w-full py-4 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-xl font-semibold transition">
                    Sebagai Pemilik
                </a>

                <a href="{{ route('register.penyewa') }}"
                    class="block w-full py-4 rounded-xl bg-green-500 hover:bg-green-600 text-white text-xl font-semibold transition">
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
