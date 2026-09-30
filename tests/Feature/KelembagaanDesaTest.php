<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\LembagaDesa;
use App\Models\BumdesDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KelembagaanDesaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test create LKD record.
     */
    public function test_can_create_lkd_record(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.kelembagaan.store'), [
            'nama_lembaga' => 'Karang Taruna Semampir',
            'singkatan' => 'KARTA',
            'jenis_lembaga' => 'LKD',
            'nomor_sk_pendirian' => '140/01/2026',
            'nama_ketua' => 'Ahmad Fauzi',
            'status_aktif' => 1,
        ]);

        $response->assertRedirect(route('admin.kelembagaan.index'));
        $this->assertDatabaseHas('lembaga_desa', [
            'nama_lembaga' => 'Karang Taruna Semampir',
            'jenis_lembaga' => 'LKD',
        ]);
    }

    /**
     * Test create BUMDes record with one-to-one bumdes_detail.
     */
    public function test_can_create_bumdes_with_details(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.kelembagaan.store'), [
            'nama_lembaga' => 'BUMDes Tirta Sejahtera',
            'singkatan' => 'BUMDes Tirta',
            'jenis_lembaga' => 'BUMDes',
            'nomor_badan_hukum_kemenkumham' => 'AHU-99999.AH.01.33.2024',
            'tahun_pendirian' => 2024,
            'kategori_status' => 'Maju',
            'permodalan_awal' => 100000000,
            'total_aset' => 250000000,
            'omzet_terakhir' => 90000000,
            'daftar_unit_usaha' => 'Air Minum Isi Ulang, Toko Pertanian',
            'nama_pelaksana_operasional' => 'Budi Santoso',
            'status_aktif' => 1,
        ]);

        $response->assertRedirect(route('admin.kelembagaan.index'));
        
        $lembaga = LembagaDesa::where('nama_lembaga', 'BUMDes Tirta Sejahtera')->first();
        $this->assertNotNull($lembaga);
        $this->assertEquals('BUMDes', $lembaga->jenis_lembaga);
        
        $this->assertDatabaseHas('bumdes_detail', [
            'lembaga_id' => $lembaga->id,
            'nomor_badan_hukum_kemenkumham' => 'AHU-99999.AH.01.33.2024',
            'kategori_status' => 'Maju',
        ]);
    }

    /**
     * Test filter by jenis_lembaga on index.
     */
    public function test_can_filter_by_jenis_lembaga(): void
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        LembagaDesa::create([
            'nama_lembaga' => 'LKD Test',
            'slug' => 'lkd-test',
            'jenis_lembaga' => 'LKD',
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.kelembagaan.index', ['jenis' => 'LKD']));
        $response->assertStatus(200);
        $response->assertSee('LKD Test');
    }
}
