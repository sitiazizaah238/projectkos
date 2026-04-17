@extends('layouts.app')

@section('content')
    <style>
        .contact-hero {
            background: linear-gradient(135deg, #3f8efc, #6fb1ff);
            color: white;
            padding: 50px 0;
            border-radius: 0 0 30px 30px;
        }

        .contact-card {
            border: 1px solid #f1f1f1;
            border-radius: 16px;
            box-shadow: none;
            padding: 28px;
            background: white;
        }

        .form-control {
            border-radius: 10px;
            padding: 11px;
            border: 1px solid #e5e7eb;
        }

        .form-control:focus {
            border-color: #3f8efc;
            box-shadow: none;
        }

        .btn-primary {
            border-radius: 10px;
            padding: 11px;
            font-weight: 500;
            background: #3f8efc;
            border: none;
        }

        .btn-primary:hover {
            background: #2f7df0;
        }

        .contact-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f7fb;
            color: #3f8efc;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .contact-item:last-child {
            border-bottom: none;
        }

        .contact-label {
            font-size: 12px;
            color: #888;
        }

        .contact-value {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        .section-title-sm {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #222;
        }
    </style>

    {{-- HERO --}}
    <div class="contact-hero text-center">
        <h4 class="fw-semibold mb-1">
            <i class="bi bi-headset me-1"></i> Hubungi Kami
        </h4>
        <small>Kami siap membantu Anda kapan saja</small>
    </div>

    <div class="container py-5">
        <div class="row g-4 align-items-stretch">

            {{-- FORM --}}
            <div class="col-md-7 d-flex">
                <div class="contact-card w-100">

                    <div class="section-title-sm d-flex align-items-center gap-2 mb-3 pb-2"
                        style="border-bottom:1px solid #f1f5f9;">
                        <i class="bi bi-send text-primary"></i>
                        <span>Kirim Pesan</span>
                    </div>

                    <form onsubmit="kirimWA(event)">

                        <div class="mb-3">
                            <input type="text" name="nama" class="form-control" placeholder="Nama">
                        </div>

                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email">
                        </div>

                        <div class="mb-3">
                            <textarea name="pesan" rows="4" class="form-control" placeholder="Tulis pesan..."></textarea>
                        </div>

                        <button class="btn btn-primary w-100">
                            <i class="bi bi-send me-1"></i> Kirim Pesan
                        </button>

                    </form>

                </div>
            </div>

            {{-- INFO --}}
            <div class="col-md-5 d-flex">
                <div class="contact-card w-100">

                    <div class="section-title-sm d-flex align-items-center gap-2">
                        <i class="bi bi-person-lines-fill text-primary"></i>
                        <span>Informasi Kontak</span>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div>
                            <div class="contact-label">Email</div>
                            <div class="contact-value">findkos@gmail.com</div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <div class="contact-label">WhatsApp</div>
                            <div class="contact-value">0831-2087-0370</div>
                        </div>
                    </div>

                    <div class="contact-item mb-3">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <div class="contact-label">Lokasi</div>
                            <div class="contact-value">Lohbener, Indramayu</div>
                        </div>
                    </div>

                    <a href="https://wa.me/6283120870370" class="btn btn-outline-success w-100">
                        <i class="bi bi-whatsapp me-1"></i> Chat WhatsApp
                    </a>

                </div>
            </div>

        </div>
    </div>

    <script>
        function kirimWA(e) {
            e.preventDefault();

            let nama = document.querySelector('[name=nama]').value;
            let email = document.querySelector('[name=email]').value;
            let pesan = document.querySelector('[name=pesan]').value;

            if (!nama || !email || !pesan) {
                alert("Harap isi semua data!");
                return;
            }

            let text = `Halo Admin FindKos 👋
Nama: ${nama}
Email: ${email}

Pesan:
${pesan}`;

            let url = `https://wa.me/6283120870370?text=${encodeURIComponent(text)}`;
            window.open(url, '_blank');
        }
    </script>
@endsection
