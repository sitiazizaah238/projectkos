<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/logo2.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .container { display: flex; height: 100vh; }

        .left {
            width: 50%;
            background: #0d8bff;
            color: white;
            padding: 40px;
            position: relative;
        }

        .logo {
            position: absolute;
            top: 15px;
            left: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 22px;
        }

        .logo img { width: 40px; }

        .left-content { margin-top: 70px; padding-left: 40px; }

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
            max-width: 420px;
        }

        .card h2 {
            font-weight: 600;
            margin-bottom: 25px;
        }

        .form-group { margin-bottom: 15px; }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

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

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- LEFT -->
    <div class="left">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}">
            <span>FindKos</span>
        </div>

        <div class="left-content">
            <img src="{{ asset('images/kos.png') }}">
            <h1>Reset Password</h1>
            <p>Silakan buat password baru untuk akun Anda.</p>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <div class="card">
            <h2>Reset Password</h2>

            <!-- SUCCESS -->
            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- TOKEN -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- EMAIL -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email"
                        value="{{ old('email', $request->email) }}"
                        class="@error('email') error-input @enderror">

                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PASSWORD -->
                <div class="form-group" style="display:flex; gap:10px;">

                    <div style="flex:1; position:relative;">
                        <label>Password Baru</label>
                        <input type="password" name="password" id="password"
                            class="@error('password') error-input @enderror"
                            style="padding-right:45px;">

                        <span onclick="togglePassword('password')"
                            style="position:absolute; right:15px; top:38px; cursor:pointer;">
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
                            style="padding-right:45px;">

                        <span onclick="togglePassword('password_confirmation')"
                            style="position:absolute; right:15px; top:38px; cursor:pointer;">
                            <i class="bi bi-eye"></i>
                        </span>

                        @error('password_confirmation')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit">Reset Password</button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
