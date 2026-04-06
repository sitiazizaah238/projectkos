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
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .left {
            width: 50%;
            background: #0d8bff;
            color: white;
            padding: 40px;
            position: relative;
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

        .logo img { width: 40px; }

        .left-content {
            margin-top: 80px;
            padding-left: 40px;
        }

        .left-content img {
            width: 420px;
            display: block;
            margin-bottom: 30px;
        }

        .left-content h1 {
            margin: 0;
            font-size: 42px;
            font-weight: 700;
        }

        .left-content p {
            margin-top: 12px;
            font-size: 16px;
        }

        .right {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            width: 80%;
            max-width: 400px;
        }

        .card h2 {
            font-weight: 600;
            margin-bottom: 15px;
        }

        .form-group { margin-bottom: 11px; }

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

        /* TAMBAHAN ERROR */
        .error-text {
            color: red;
            font-size: 13px;
            margin-top: 5px;
        }

        .error-input {
            border: 1px solid red;
        }

        button {
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
            @if(session('success'))
                <div style="background:#d4edda; color:#155724; padding:10px; border-radius:8px; margin-bottom:10px;">
                    {{ session('success') }}
                </div>
            @endif

            <form action="/register-pemilik" method="POST">
                @csrf

                <!-- Nama -->
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="@error('name') error-input @enderror"
                        placeholder="Masukan nama Lengkap....">

                    @error('name')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="@error('email') error-input @enderror"
                        placeholder="Masukan email....">

                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- No HP -->
                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                        class="@error('no_hp') error-input @enderror"
                        placeholder="Masukan no HP....."
                        inputmode="numeric"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                    @error('no_hp')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat -->
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="alamat" value="{{ old('alamat') }}"
                        class="@error('alamat') error-input @enderror"
                        placeholder="Masukan alamat lengkap.....">

                    @error('alamat')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group" style="display:flex; gap:10px;">

                    <div style="flex:1; position:relative;">
                        <label>Password</label>
                        <input type="password" name="password" id="password"
                            class="@error('password') error-input @enderror"
                            placeholder="Masukan password"
                            style="padding-right:45px; width:100%;">

                        <span onclick="togglePassword('password', this)"
                            style="position:absolute; right:15px; top:38px; cursor:pointer; color:#888;">
                            <i class="bi bi-eye"></i>
                        </span>

                        @error('password')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="flex:1; position:relative;">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="@error('password_confirmation') error-input @enderror"
                            placeholder="Ulangi password"
                            style="padding-right:45px; width:100%;">

                        <span onclick="togglePassword('password_confirmation', this)"
                            style="position:absolute; right:15px; top:38px; cursor:pointer; color:#888;">
                            <i class="bi bi-eye"></i>
                        </span>

                        @error('password_confirmation')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit">Daftar</button>
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
