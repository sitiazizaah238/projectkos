<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RecommendationTestingSeeder extends Seeder
{
    public function run(): void
    {
        $pemilik = User::firstOrCreate(
            ['email' => 'pemilik.rekom.testing@kos.com'],
            [
                'name' => 'Pemilik Rekomendasi Testing',
                'password' => Hash::make('password123'),
                'role' => 'pemilik',
                'status' => 'aktif',
            ]
        );

        $dataKos = [
            [
                'nama_kos' => 'Kos Harmoni Putri Dago',
                'lokasi' => 'Jl. Ir. H. Juanda No. 18, Dago, Bandung',
                'tipe_kos' => 'Putri',
                'deskripsi' => 'Kos putri dengan suasana tenang, dekat kampus, dan akses transportasi mudah.',
                'fasilitas' => ['Wifi', 'CCTV', 'Parkir', 'Dapur', 'Mushola'],
                'kamars' => [
                    [
                        'nama_kamar' => 'Tipe Standard Sakura',
                        'deskripsi' => 'Kamar simple untuk mahasiswa, pencahayaan alami, cocok untuk harian belajar.',
                        'harga' => 850000,
                        'tipe_harga' => 'bulanan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Meja Belajar', 'Wifi'],
                    ],
                    [
                        'nama_kamar' => 'Tipe Study Plus',
                        'deskripsi' => 'Fokus untuk yang butuh ruang belajar nyaman dengan meja besar dan kursi ergonomis.',
                        'harga' => 1100000,
                        'tipe_harga' => 'bulanan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Meja Belajar', 'AC', 'Wifi'],
                    ],
                    [
                        'nama_kamar' => 'Tipe Compact Hemat',
                        'deskripsi' => 'Ukuran efisien dengan fasilitas inti, cocok untuk budget minimalis.',
                        'harga' => 700000,
                        'tipe_harga' => 'bulanan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Wifi'],
                    ],
                    [
                        'nama_kamar' => 'Tipe Annual Smart',
                        'deskripsi' => 'Paket tahunan dengan biaya lebih hemat untuk komitmen tinggal jangka panjang.',
                        'harga' => 12000000,
                        'tipe_harga' => 'tahunan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Meja Belajar', 'AC', 'CCTV'],
                    ],
                    [
                        'nama_kamar' => 'Tipe Annual Premium',
                        'deskripsi' => 'Kamar tahunan premium dengan ruang lebih luas dan kenyamanan maksimal.',
                        'harga' => 15000000,
                        'tipe_harga' => 'tahunan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Meja Belajar', 'AC', 'Kamar Mandi Dalam'],
                    ],
                ],
            ],
            [
                'nama_kos' => 'Kos Nusantara Campur Cihampelas',
                'lokasi' => 'Jl. Cihampelas No. 77, Bandung',
                'tipe_kos' => 'Campur',
                'deskripsi' => 'Kos campur modern dengan komunitas aktif, dekat pusat kuliner dan area perbelanjaan.',
                'fasilitas' => ['Wifi', 'Parkir', 'CCTV', 'Dapur'],
                'kamars' => [
                    [
                        'nama_kamar' => 'Tipe Loft Urban',
                        'deskripsi' => 'Nuansa urban minimalis dengan tata ruang ringkas dan fungsional.',
                        'harga' => 950000,
                        'tipe_harga' => 'bulanan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Meja Belajar', 'Wifi'],
                    ],
                    [
                        'nama_kamar' => 'Tipe Creator Room',
                        'deskripsi' => 'Dirancang untuk content creator, area kerja lega dengan pencahayaan baik.',
                        'harga' => 1300000,
                        'tipe_harga' => 'bulanan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Meja Belajar', 'AC', 'Wifi'],
                    ],
                    [
                        'nama_kamar' => 'Tipe Eco Shared',
                        'deskripsi' => 'Pilihan ekonomis untuk penghuni aktif dengan kebutuhan dasar lengkap.',
                        'harga' => 780000,
                        'tipe_harga' => 'bulanan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Wifi', 'CCTV'],
                    ],
                    [
                        'nama_kamar' => 'Tipe Annual Growth',
                        'deskripsi' => 'Kamar paket tahunan untuk pekerja muda yang ingin stabilitas biaya.',
                        'harga' => 12600000,
                        'tipe_harga' => 'tahunan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Meja Belajar', 'AC'],
                    ],
                    [
                        'nama_kamar' => 'Tipe Annual Executive',
                        'deskripsi' => 'Paket tahunan kelas eksekutif dengan ruang nyaman dan fasilitas lengkap.',
                        'harga' => 16800000,
                        'tipe_harga' => 'tahunan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Meja Belajar', 'AC', 'Kamar Mandi Dalam'],
                    ],
                ],
            ],
            [
                'nama_kos' => 'Kos Garuda Putra Setiabudi',
                'lokasi' => 'Jl. Setiabudi No. 102, Bandung',
                'tipe_kos' => 'Putra',
                'deskripsi' => 'Kos putra strategis untuk karyawan dan mahasiswa, lingkungan aman dan tertib.',
                'fasilitas' => ['Wifi', 'Parkir', 'CCTV', 'Dapur', 'Mushola'],
                'kamars' => [
                    [
                        'nama_kamar' => 'Tipe Starter Active',
                        'deskripsi' => 'Kamar awal yang cocok untuk pekerja baru dengan kebutuhan praktis.',
                        'harga' => 800000,
                        'tipe_harga' => 'bulanan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Wifi'],
                    ],
                    [
                        'nama_kamar' => 'Tipe Focus Work',
                        'deskripsi' => 'Ruang tinggal yang mendukung produktivitas dengan area kerja khusus.',
                        'harga' => 1050000,
                        'tipe_harga' => 'bulanan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Meja Belajar', 'Wifi', 'CCTV'],
                    ],
                    [
                        'nama_kamar' => 'Tipe Comfort Plus',
                        'deskripsi' => 'Keseimbangan harga dan kenyamanan untuk penghuni jangka menengah.',
                        'harga' => 1250000,
                        'tipe_harga' => 'bulanan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Meja Belajar', 'AC'],
                    ],
                    [
                        'nama_kamar' => 'Tipe Annual Saver',
                        'deskripsi' => 'Skema tahunan hemat untuk penghuni yang ingin mengurangi biaya bulanan.',
                        'harga' => 11400000,
                        'tipe_harga' => 'tahunan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Meja Belajar', 'Wifi'],
                    ],
                    [
                        'nama_kamar' => 'Tipe Annual Prime',
                        'deskripsi' => 'Kamar tahunan terbaik dengan fasilitas lengkap untuk kenyamanan maksimal.',
                        'harga' => 15600000,
                        'tipe_harga' => 'tahunan',
                        'fasilitas' => ['Kasur', 'Lemari', 'Meja Belajar', 'AC', 'Kamar Mandi Dalam'],
                    ],
                ],
            ],
        ];

        foreach ($dataKos as $itemKos) {
            $kos = Kos::updateOrCreate(
                [
                    'user_id' => $pemilik->id,
                    'nama_kos' => $itemKos['nama_kos'],
                ],
                [
                    'lokasi' => $itemKos['lokasi'],
                    'tipe_kos' => $itemKos['tipe_kos'],
                    'deskripsi' => $itemKos['deskripsi'],
                    'fasilitas' => $itemKos['fasilitas'],
                    'foto' => [],
                    'status' => 'disetujui',
                    'tanggal_verifikasi' => now(),
                    'is_read' => true,
                    'edit_request_status' => 'tidak_ada',
                    'edit_request_data' => null,
                    'edit_request_alasan' => null,
                    'edit_requested_at' => null,
                    'alasan' => null,
                ]
            );

            Kamar::where('kos_id', $kos->id)->delete();

            foreach ($itemKos['kamars'] as $itemKamar) {
                Kamar::create([
                    'kos_id' => $kos->id,
                    'nama_kamar' => $itemKamar['nama_kamar'],
                    'deskripsi' => $itemKamar['deskripsi'],
                    'harga' => $itemKamar['harga'],
                    'tipe_harga' => $itemKamar['tipe_harga'],
                    'status' => 'tersedia',
                    'foto' => [],
                    'fasilitas' => $itemKamar['fasilitas'],
                ]);
            }
        }
    }
}
