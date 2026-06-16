<?php

namespace Tests\Feature\Penyewa;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\User;
use App\Models\PengajuanSewa;
use App\Models\UserPreference;
use App\Services\RecommendationScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FlexibleRentalAndRecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected $penyewa;
    protected $pemilik;
    protected $kos;
    protected $kamarTahunan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->penyewa = User::create([
            'name' => 'Penyewa Test',
            'email' => 'penyewa@test.com',
            'password' => Hash::make('password'),
            'role' => 'penyewa'
        ]);

        $this->pemilik = User::create([
            'name' => 'Pemilik Test',
            'email' => 'pemilik@test.com',
            'password' => Hash::make('password'),
            'role' => 'pemilik',
            'status' => 'aktif'
        ]);

        $this->kos = Kos::create([
            'user_id' => $this->pemilik->id,
            'nama_kos' => 'Kos Testing',
            'lokasi' => 'Indramayu',
            'tipe_kos' => 'Campur',
            'status' => 'disetujui'
        ]);

        $this->kamarTahunan = Kamar::create([
            'kos_id' => $this->kos->id,
            'nama_kamar' => 'T01',
            'harga' => 12000000, // 12jt per tahun (artinya 1jt per bulan)
            'tipe_harga' => 'tahunan',
            'status' => 'tersedia'
        ]);
    }

    #[Test]
    public function it_calculates_precision_pro_rata_price_for_yearly_room_with_3_months_duration()
    {
        $this->actingAs($this->penyewa);

        $response = $this->post(route('penyewa.pengajuan.store'), [
            'kos_id' => $this->kos->id,
            'kamar_id' => $this->kamarTahunan->id,
            'durasi' => 3,
            'tanggal_mulai' => now()->toDateString(),
            'jenis_sewa' => 'bulanan'
        ]);

        $pengajuan = PengajuanSewa::first();
        
        // Harga 12.000.000 / 12 = 1.000.000 per bulan
        // Untuk 3 bulan = 3.000.000
        $this->assertEquals(3000000, $pengajuan->total_bayar);
    }

    #[Test]
    public function it_calculates_full_price_for_yearly_room_with_12_months_duration()
    {
        $this->actingAs($this->penyewa);

        $this->post(route('penyewa.pengajuan.store'), [
            'kos_id' => $this->kos->id,
            'kamar_id' => $this->kamarTahunan->id,
            'durasi' => 12,
            'tanggal_mulai' => now()->toDateString(),
            'jenis_sewa' => 'tahunan'
        ]);

        $pengajuan = PengajuanSewa::first();
        $this->assertEquals(12000000, $pengajuan->total_bayar);
    }

    #[Test]
    public function it_formats_yearly_duration_label_correctly_in_model()
    {
        // 12 bulan pada kamar tahunan harus jadi "1 Tahun"
        $label = PengajuanSewa::formatDurasiByTipe(12, 'tahunan');
        $this->assertEquals('1 Tahun', $label);

        // 24 bulan pada kamar tahunan harus jadi "2 Tahun"
        $label = PengajuanSewa::formatDurasiByTipe(24, 'tahunan');
        $this->assertEquals('2 Tahun', $label);

        // 3 bulan pada kamar tahunan tetap "3 Bulan"
        $label = PengajuanSewa::formatDurasiByTipe(3, 'tahunan');
        $this->assertEquals('3 Bulan', $label);
    }

    #[Test]
    public function it_learns_from_user_habits_feedback_loop()
    {
        $service = new RecommendationScoringService();
        
        // Awalnya preferensi kosong
        $this->assertNull(UserPreference::where('user_id', $this->penyewa->id)->first());

        // User "melihat" kos tahunan harga 12jt
        $service->learnFromKosView($this->penyewa, $this->kos);

        $pref = UserPreference::where('user_id', $this->penyewa->id)->first();
        $this->assertNotNull($pref);
        
        // Preferensi harga harus mendekati harga kamar yang dilihat
        $this->assertEquals(12000000, $pref->pref_harga);
        $this->assertEquals('tahunan', $pref->pref_tipe_harga);
    }

    #[Test]
    public function it_extends_yearly_room_with_monthly_duration_and_correct_price()
    {
        $this->actingAs($this->penyewa);

        // Buat pengajuan lama yang hampir habis (misah H-2)
        $tanggalMulai = now()->subMonths(12)->addDays(2)->toDateString();

        $pengajuan = PengajuanSewa::create([
            'user_id' => $this->penyewa->id,
            'kos_id' => $this->kos->id,
            'kamar_id' => $this->kamarTahunan->id,
            'durasi' => 12,
            'tanggal_mulai' => $tanggalMulai,
            'status' => 'aktif',
            'total_bayar' => 12000000
        ]);

        // Perpanjang 2 bulan
        $response = $this->post(route('penyewa.pengajuan.perpanjang', $pengajuan->id), [
            'durasi_tambahan' => 2,
            'jenis_sewa' => 'bulanan'
        ]);

        $pengajuan->refresh();
        
        // Durasi jadi 14 (12 + 2)
        $this->assertEquals(14, $pengajuan->durasi);
        // Total bayar jadi 14.000.000 (12jt + 2jt pro-rata)
        $this->assertEquals(14000000, $pengajuan->total_bayar);
    }
}
