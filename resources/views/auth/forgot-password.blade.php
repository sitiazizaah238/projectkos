<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - FindKos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="{{ asset('images/logo2.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body>

<div class="flex min-h-screen">

    <!-- LEFT -->
    <div class="w-1/2 bg-gradient-to-b from-blue-500 to-blue-700 text-white flex flex-col justify-center px-20 relative">

        <div style="position:absolute; top:10px; left:40px; display:flex; align-items:center; gap:12px; font-weight:700; font-size:22px;">
            <img src="{{ asset('images/logo.png') }}" style="width:40px;">
            <span>FindKos</span>
        </div>

        <img src="{{ asset('images/kos.png') }}" style="width:420px; margin-bottom:30px;">

        <h2 class="text-4xl font-bold mb-4">
            Lupa Password?
        </h2>

        <p class="text-blue-100">
            Masukkan email Anda untuk mendapatkan link reset password
        </p>
    </div>

    <!-- RIGHT -->
    <div class="w-1/2 bg-gray-100 flex items-center justify-center">

        <div class="w-[420px] bg-white p-10 rounded-2xl shadow-sm">

            <h2 class="text-2xl font-semibold mb-4">
                Reset Password
            </h2>

            <p class="text-sm text-gray-500 mb-6">
                Kami akan mengirimkan link reset ke email Anda
            </p>

            <!-- STATUS -->
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- FORM -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- EMAIL -->
                <div class="mb-6">
                    <label class="block mb-2 text-gray-700 font-medium">Email</label>

                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-5 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Masukan Email..">

                    @error('email')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- BUTTON -->
                <button type="submit"
                    class="w-full py-3 rounded-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold text-lg">
                    Kirim Link Reset
                </button>

                <!-- BACK -->
                <p class="text-center mt-5 text-sm">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                        Kembali ke Login
                    </a>
                </p>

            </form>

        </div>
    </div>

</div>

</body>
</html>
