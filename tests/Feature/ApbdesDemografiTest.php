<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Apbdes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

class ApbdesDemografiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure test config path or seed default
    }

    /**
     * 1. Test Validasi Format Tahun APBDes (2000 - 2099).
     */
    public function test_apbdes_year_validation_requires_valid_year_between_2000_and_2099(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        // Test tahun < 2000
        $response1 = $this->actingAs($admin)->post(route('admin.beranda.apbd.store'), [
            'apbd_year' => 1999,
            'apbd_alloc_name' => 'Bidang Sarpras',
            'apbd_alloc_amount' => '100.000.000',
            'apbd_alloc_pct' => '20',
        ]);
        $response1->assertSessionHasErrors(['apbd_year']);

        // Test tahun > 2099
        $response2 = $this->actingAs($admin)->post(route('admin.beranda.apbd.store'), [
            'apbd_year' => 2100,
            'apbd_alloc_name' => 'Bidang Sarpras',
            'apbd_alloc_amount' => '100.000.000',
            'apbd_alloc_pct' => '20',
        ]);
        $response2->assertSessionHasErrors(['apbd_year']);

        // Test tahun valid 2026
        $response3 = $this->actingAs($admin)->post(route('admin.beranda.apbd.store'), [
            'apbd_year' => 2026,
            'apbd_alloc_name' => 'Bidang Pembangunan',
            'apbd_alloc_amount' => '500.000.000',
            'apbd_alloc_pct' => '35',
        ]);
        $response3->assertRedirect(route('admin.beranda.transparansi', ['tahun' => 2026]));
        $this->assertDatabaseHas('apbdes', [
            'tahun' => 2026,
            'nama_bidang' => 'Bidang Pembangunan',
            'anggaran' => 500000000,
        ]);
    }

    /**
     * 2. Test Constraint Unik Kombinasi (tahun, nama_bidang) APBDes.
     */
    public function test_apbdes_unique_combination_of_year_and_name(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        Apbdes::create([
            'tahun' => 2026,
            'nama_bidang' => 'Bidang Kesehatan',
            'anggaran' => 200000000,
            'persentase' => 20,
        ]);

        // Coba masukkan nama yang sama di tahun yang sama (case-insensitive)
        $response = $this->actingAs($admin)->post(route('admin.beranda.apbd.store'), [
            'apbd_year' => 2026,
            'apbd_alloc_name' => 'bidang kesehatan',
            'apbd_alloc_amount' => '150.000.000',
            'apbd_alloc_pct' => '15',
        ]);

        $response->assertSessionHasErrors(['apbd_alloc_name']);

        // Masukkan nama yang sama di tahun berbeda -> berhasil
        $responseDiffYear = $this->actingAs($admin)->post(route('admin.beranda.apbd.store'), [
            'apbd_year' => 2027,
            'apbd_alloc_name' => 'Bidang Kesehatan',
            'apbd_alloc_amount' => '250.000.000',
            'apbd_alloc_pct' => '25',
        ]);

        $responseDiffYear->assertRedirect(route('admin.beranda.transparansi', ['tahun' => 2027]));
        $this->assertDatabaseHas('apbdes', [
            'tahun' => 2027,
            'nama_bidang' => 'Bidang Kesehatan',
        ]);
    }

    /**
     * 3. Test Filter Tahun pada Halaman Transparansi.
     */
    public function test_apbdes_transparansi_page_filters_by_year(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        Apbdes::create([
            'tahun' => 2026,
            'nama_bidang' => 'Bidang Infrastruktur 2026',
            'anggaran' => 300000000,
            'persentase' => 30,
        ]);

        Apbdes::create([
            'tahun' => 2025,
            'nama_bidang' => 'Bidang Irigasi 2025',
            'anggaran' => 200000000,
            'persentase' => 20,
        ]);

        $res2026 = $this->actingAs($admin)->get(route('admin.beranda.transparansi', ['tahun' => 2026]));
        $res2026->assertStatus(200);
        $res2026->assertSee('Bidang Infrastruktur 2026');
        $res2026->assertDontSee('Bidang Irigasi 2025');

        $res2025 = $this->actingAs($admin)->get(route('admin.beranda.transparansi', ['tahun' => 2025]));
        $res2025->assertStatus(200);
        $res2025->assertSee('Bidang Irigasi 2025');
        $res2025->assertDontSee('Bidang Infrastruktur 2026');
    }

    /**
     * 4. Test Otomatisasi Perhitungan Total Penduduk (total_penduduk = jumlah_pria + jumlah_wanita).
     */
    public function test_total_penduduk_automatically_recalculated_on_backend(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.beranda.update'), [
            'section' => 'demografi',
            'demo_total' => '9.999', // User tried to send spoofed total
            'demo_male' => '3.000',
            'demo_female' => '2.500',
            'demo_prod_count' => '3.500',
            'demo_child_count' => '1.000',
            'demo_elderly_count' => '1.000', // Total usia = 5.500 <= 5.500
        ]);

        $response->assertSessionHas('status');

        $profileData = \App\Http\Controllers\Admin\VillageProfileController::getProfileData();
        // Total harus otomatis 5.500 bukan 9.999
        $this->assertEquals('5.500', $profileData['demographics']['total']);
        $this->assertEquals('5.500', $profileData['stats']['penduduk']);
    }

    /**
     * 5. Test Validasi Batas Maksimal Kategori Usia Tidak Boleh Melebihi Total Penduduk.
     */
    public function test_demografi_category_exceeding_total_penduduk_is_rejected_with_exact_message(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        // Total penduduk = 3.000 + 2.500 = 5.500 jiwa
        // Kelompok usia = 3.500 + 1.500 + 1.000 = 6.000 jiwa (> 5.500)
        $response = $this->actingAs($admin)->post(route('admin.beranda.update'), [
            'section' => 'demografi',
            'demo_male' => '3.000',
            'demo_female' => '2.500',
            'demo_prod_count' => '3.500',
            'demo_child_count' => '1.500',
            'demo_elderly_count' => '1.000',
        ]);

        $response->assertSessionHasErrors();
        $errors = session('errors')->all();
        $this->assertContains("Jumlah Kelompok Usia tidak boleh melebihi total penduduk (5.500 jiwa).", $errors);
    }

    /**
     * 6. Test Validasi Batas Maksimal Tingkat Pendidikan & Mata Pencaharian.
     */
    public function test_education_and_occupation_exceeding_total_penduduk_is_rejected(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        // Set total penduduk to 5.000
        $this->actingAs($admin)->post(route('admin.beranda.update'), [
            'section' => 'demografi',
            'demo_male' => '2.500',
            'demo_female' => '2.500',
            'demo_prod_count' => '3.000',
            'demo_child_count' => '1.000',
            'demo_elderly_count' => '1.000',
        ]);

        // Tambah Tingkat Pendidikan melebihi 5.000
        $response = $this->actingAs($admin)->post(route('admin.beranda.statistik.store', 'education'), [
            'stat_name' => 'Sarjana S1',
            'stat_count' => '6.000',
        ]);

        $response->assertSessionHasErrors();
        $errors = session('errors')->all();
        $this->assertContains("Jumlah Tingkat Pendidikan tidak boleh melebihi total penduduk (5.000 jiwa).", $errors);
    }
}
