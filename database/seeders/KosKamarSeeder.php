<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KosKamarSeeder extends Seeder
{
    public function run(): void
    {
        $pemilikAktif = User::where('role', 'pemilik')
            ->where('status', 'aktif')
            ->get();

        if ($pemilikAktif->isEmpty()) {
            $pemilikAktif = collect([
                User::create([
                    'name' => 'Pemilik Seeder',
                    'email' => 'pemilik.seeder@kos.com',
                    'password' => Hash::make('password123'),
                    'role' => 'pemilik',
                    'status' => 'aktif',
                ]),
            ]);
        }

        $imagePool = $this->prepareSeedImagePool();

        foreach ($pemilikAktif as $pemilik) {
            $jumlahKos = random_int(3, 5);

            for ($i = 1; $i <= $jumlahKos; $i++) {
                $kos = Kos::create([
                    'user_id' => $pemilik->id,
                    'nama_kos' => fake()->company() . ' Residence ' . $i,
                    'lokasi' => fake()->streetAddress() . ', Lobener',
                    'tipe_kos' => fake()->randomElement(['Putra', 'Putri', 'Campur']),
                    'deskripsi' => fake()->paragraph(2),
                    'fasilitas' => collect(['Wifi', 'Parkir', 'CCTV', 'Dapur', 'Mushola'])
                        ->shuffle()
                        ->take(random_int(2, 5))
                        ->values()
                        ->all(),
                    'foto' => $this->pickRandomImages($imagePool, random_int(2, 3)),
                    'status' => 'disetujui',
                    'tanggal_verifikasi' => now()->subDays(random_int(1, 30)),
                    'is_read' => true,
                    'edit_request_status' => 'tidak_ada',
                    'edit_request_data' => null,
                    'edit_request_alasan' => null,
                    'edit_requested_at' => null,
                    'alasan' => null,
                ]);

                $jumlahKamar = random_int(2, 3);

                for ($k = 1; $k <= $jumlahKamar; $k++) {
                    Kamar::create([
                        'kos_id' => $kos->id,
                        'nama_kamar' => 'Kamar ' . chr(64 + $k),
                        'deskripsi' => fake()->sentence(10),
                        'harga' => fake()->randomElement([450000, 550000, 650000, 750000, 900000]),
                        'tipe_harga' => 'bulanan',
                        'status' => 'tersedia',
                        'foto' => $this->pickRandomImages($imagePool, random_int(1, 2)),
                        'fasilitas' => collect(['Kasur', 'Lemari', 'Meja Belajar', 'Kamar Mandi Dalam', 'AC'])
                            ->shuffle()
                            ->take(random_int(2, 4))
                            ->values()
                            ->all(),
                    ]);
                }
            }
        }
    }

    private function prepareSeedImagePool(): array
    {
        $sourceDir = public_path('images');

        if (!is_dir($sourceDir)) {
            return [];
        }

        $files = collect(File::files($sourceDir))
            ->filter(function ($file) {
                return in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true);
            })
            ->values();

        $pool = [];

        foreach ($files as $file) {
            $targetRelativePath = 'seed-images/' . $file->getFilename();

            if (!Storage::disk('public')->exists($targetRelativePath)) {
                Storage::disk('public')->put($targetRelativePath, File::get($file->getPathname()));
            }

            $pool[] = $targetRelativePath;
        }

        return $pool;
    }

    private function pickRandomImages(array $pool, int $count): array
    {
        if (empty($pool)) {
            return [];
        }

        shuffle($pool);

        return array_values(array_slice($pool, 0, min($count, count($pool))));
    }
}
