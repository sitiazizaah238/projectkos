<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register Pemilik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
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
            top: 25px;
            left: 40px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 18px;
        }

        .logo img {
            width: 30px;
        }

        .left-content {
            margin-top: 110px;
            padding-left: 40px;
        }

        .left-content img {
            width: 380px;
            /* dikecilin dikit */
            display: block;
            margin-bottom: 30px;
        }

        .left-content h1 {
            margin: 0;
            font-size: 42px;
            /* dikecilin dikit */
            font-weight: 700;
            line-height: 1.2;
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

        .form-group {
            margin-bottom: 15px;
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
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-family: 'Poppins', sans-serif;
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
            font-family: 'Poppins', sans-serif;
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
                <span>FINDKOS</span>
            </div>

            <div class="left-content">
                <img src="{{ asset('images/ilustrasi.png') }}">

                <h1>Halo....<br>Selamat Datang</h1>
                <p>Di Website FindKos di wilayah<br>Lohbener Indramayu</p>
            </div>
        </div>

        <div class="right">
            <div class="card">
                <h2>Register Pemilik Kos</h2>

                <form action="/register-pemilik" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Nama Pemilik</label>
                        <input type="text" name="name" placeholder="Masukan nama pemilik">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Masukan email">
                    </div>

                    <div class="form-group">
                        <label>No HP</label>
                        <input type="text" name="no_hp" placeholder="Masukan no HP" inputmode="numeric" required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>



                    <div class="form-group" style="position:relative;">
                        <label>Password</label>
                        <input type="password" name="password" id="password" placeholder="Masukan password"
                            style="padding-right:45px;">

                        <span onclick="togglePassword('password')"
                            style="position:absolute; right:15px; top:38px; cursor:pointer; color:#888;">
                            👁
                        </span>
                    </div>

                    <div class="form-group" style="position:relative;">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="Ulangi password" style="padding-right:45px;">

                        <span onclick="togglePassword('password_confirmation')"
                            style="position:absolute; right:15px; top:38px; cursor:pointer; color:#888;">
                            👁
                        </span>
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
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === "password" ? "text" : "password";
        }
    </script>

</body>

</html>
