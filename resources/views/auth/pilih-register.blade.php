<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pilih Register - FINDKOS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-10 rounded-2xl shadow-xl border w-[420px] text-center">

    <h2 class="text-2xl font-bold mb-8">Daftar Sebagai</h2>

    <div class="space-y-6">

        <!-- PEMILIK -->
        <a href="/register-pemilik"
           class="block w-full py-4 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-xl font-semibold transition">
           Sebagai Pemilik
        </a>

        <!-- PENYEWA -->
        <a href="/register-penyewa"
           class="block w-full py-4 rounded-xl bg-green-500 hover:bg-green-600 text-white text-xl font-semibold transition">
           Sebagai Penyewa
        </a>

    </div>

</div>

</body>
</html>
