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

        Kos::where('user_id', $pemilik->id)->delete();

        $tipeKosOptions = ['Putra', 'Putri', 'Campur'];
        $lokasiOptions = ['Bandung', 'Jakarta', 'Surabaya', 'Yogyakarta', 'Malang', 'Semarang', 'Depok', 'Bogor'];
        $fasilitasKosOptions = ['Wifi', 'Parkir', 'CCTV', 'Dapur', 'Mushola', 'Ruang Tamu', 'Kulkas Bersama', 'Mesin Cuci'];
        $fasilitasKamarOptions = ['Kasur', 'Lemari', 'Meja Belajar', 'AC', 'Kamar Mandi Dalam', 'Kipas Angin', 'Jendela', 'Rak Buku'];

        for ($i = 1; $i <= 30; $i++) {
            $tipeKos = $tipeKosOptions[array_rand($tipeKosOptions)];
            $lokasi = $lokasiOptions[array_rand($lokasiOptions)];
            
            // Randomize facilities
            $fasilitasKos = [];
            $numFasilitas = rand(3, 6);
            $randomKeys = array_rand($fasilitasKosOptions, $numFasilitas);
            foreach ((array)$randomKeys as $key) {
                $fasilitasKos[] = $fasilitasKosOptions[$key];
            }

            $kos = Kos::create([
                'user_id' => $pemilik->id,
                'nama_kos' => 'Kos Testing ' . $tipeKos . ' ' . $i,
                'lokasi' => 'Jl. Testing No. ' . $i . ', ' . $lokasi,
                'tipe_kos' => $tipeKos,
                'deskripsi' => 'Kos nyaman untuk tipe ' . $tipeKos . ' di ' . $lokasi,
                'fasilitas' => $fasilitasKos,
                'foto' => [],
                'status' => 'disetujui',
                'tanggal_verifikasi' => now(),
                'is_read' => true,
                'edit_request_status' => 'tidak_ada',
            ]);

            // Create 2-4 Kamar for each Kos
            $numKamar = rand(2, 4);
            for ($k = 1; $k <= $numKamar; $k++) {
                $tipeHarga = (rand(1, 10) > 8) ? 'tahunan' : 'bulanan';
                $harga = ($tipeHarga == 'bulanan') ? rand(5, 20) * 100000 : rand(8, 25) * 1000000;

                $fasilitasKamar = [];
                $numKamarFasilitas = rand(3, 6);
                $randomKamarKeys = array_rand($fasilitasKamarOptions, $numKamarFasilitas);
                foreach ((array)$randomKamarKeys as $key) {
                    $fasilitasKamar[] = $fasilitasKamarOptions[$key];
                }

                Kamar::create([
                    'kos_id' => $kos->id,
                    'nama_kamar' => 'Kamar Tipe ' . $k,
                    'deskripsi' => 'Kamar nyaman tipe ' . $tipeHarga,
                    'harga' => $harga,
                    'tipe_harga' => $tipeHarga,
                    'status' => 'tersedia',
                    'foto' => [],
                    'fasilitas' => $fasilitasKamar,
                ]);
            }
        }
    }
}
