<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register Pemilik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="icon" href="{{ asset('images/logo2.png') }}" type="image/png">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .container {
            display: flex;
            min-height: 100vh;
            align-items: stretch;
        }

        .left {
            width: 50%;
            background: #0d8bff;
            color: white;
            padding: 40px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            position: absolute;
            top: 10px;
            left: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 22px;
        }

        .logo img {
            width: 40px;
            height: auto;
        }

        .left-content {
            margin-top: 80px;
            padding-left: 40px;
        }

        .left-content img {
            width: min(100%, 420px);
            display: block;
            margin-bottom: 30px;
        }

        .left-content h1 {
            margin: 0;
            font-size: clamp(30px, 4vw, 42px);
            font-weight: 700;
            line-height: 1.2;
        }

        .left-content p {
            margin-top: 12px;
            font-size: clamp(14px, 1.6vw, 16px);
            line-height: 1.7;
            max-width: 32rem;
        }

        .right {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .card {
            width: min(100%, 400px);
        }

        .card h2 {
            font-weight: 600;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 11px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-family: 'Poppins', sans-serif;
        }

        .password-row {
            display: flex;
            gap: 10px;
        }

        .password-col {
            flex: 1;
            position: relative;
        }

        .password-input {
            padding-right: 45px;
            width: 100%;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 38px;
            cursor: pointer;
            color: #888;
        }

        .password-toggle i {
            pointer-events: none;
        }

        .form-action {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 20px;
            background: #00c6ff;
            color: white;
            font-size: 16px;
            margin-top: 15px;
            cursor: pointer;
        }

        a {
            color: #00c6ff;
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
            }

            .left,
            .right {
                width: 100%;
            }

            .left {
                padding: 5rem 2rem 3rem;
                text-align: center;
                align-items: center;
            }

            .left-content img {
                width: min(100%, 340px);
            }

            .logo {
                left: 20px;
                transform: none;
            }

            .left-content {
                margin-top: 4rem;
                padding-left: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .right {
                padding: 1.5rem 1.25rem 3rem;
            }
        }

        @media (max-width: 640px) {
            .left {
                padding: 5rem 1.25rem 2rem;
                min-height: auto;
            }

            .logo {
                top: 14px;
                font-size: 18px;
            }

            .logo img {
                width: 34px;
            }

            .left-content img {
                width: min(100%, 260px);
                margin-bottom: 20px;
            }

            .card {
                max-width: 100%;
                width: 100%;
            }

            .password-row {
                flex-direction: column;
                gap: 0;
            }

            .password-col+.password-col {
                margin-top: 15px;
            }

            .password-toggle {
                top: 37px;
            }

            .form-action {
                font-size: 15px;
            }
        }

        /* TAMBAHAN ERROR */
        .error-text {
            font-size: 13px;
            margin-top: 5px;
        }

        .error-input {
            border: 1px solid red;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="left">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}">
                <span>FindKos</span>
            </div>

            <div class="left-content">
                <img src="{{ asset('images/kos.png') }}">
                <h1>Registrasi Akun</h1>
                <p>Lengkapi data diri Anda untuk mulai menggunakan layanan FindKos di wilayah Lohbener Indramayu.</p>
            </div>
        </div>

        <div class="right">
            <div class="card">
                <h2>Register Pemilik Kos</h2>

                <!-- ALERT SUKSES -->
                @if (session('success'))
                    <div
                        style="background:#d4edda; color:#155724; padding:10px; border-radius:8px; margin-bottom:10px;">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="/register-pemilik" method="POST">
                    @csrf

                    <!-- Nama -->
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="@error('name') error-input @enderror" placeholder="Masukan nama Lengkap....">

                        @error('name')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="@error('email') error-input @enderror" placeholder="Masukan email....">

                        @error('email')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- No HP -->
                    <div class="form-group">
                        <label>No HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                            class="@error('no_hp') error-input @enderror" placeholder="Masukan no HP....."
                            inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                        @error('no_hp')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="alamat" value="{{ old('alamat') }}"
                            class="@error('alamat') error-input @enderror" placeholder="Masukan alamat lengkap.....">

                        @error('alamat')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group password-row">

                        <div class="password-col">
                            <label>Password</label>
                            <input type="password" name="password" id="password"
                                class="password-input @error('password') error-input @enderror"
                                placeholder="Masukan password">

                            <span onclick="togglePassword('password', this)" class="password-toggle">
                                <i class="bi bi-eye"></i>
                            </span>

                            @error('password')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="password-col">
                            <label>Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="password-input @error('password_confirmation') error-input @enderror"
                                placeholder="Ulangi password">

                            <span onclick="togglePassword('password_confirmation', this)" class="password-toggle">
                                <i class="bi bi-eye"></i>
                            </span>

                            @error('password_confirmation')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="form-action">Daftar</button>
                </form>

                <p style="text-align:center;margin-top:12px">
                    Sudah punya akun? <a href="/login">Login</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id, el) {
            const input = document.getElementById(id);
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
