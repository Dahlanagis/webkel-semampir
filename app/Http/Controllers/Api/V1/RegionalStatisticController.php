<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RegionalStatisticResource;
use App\Models\RegionalStatistic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RegionalStatisticController extends Controller
{
    /**
     * GET /api/v1/statistik-wilayah
     *
     * Mengambil data statistik wilayah, demografi, dan administratif terkini.
     * Mendukung query parameter opsional: ?tahun=2026
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        $tahun = $request->query('tahun');

        // Cache key per tahun atau default aktif
        $cacheKey = 'api_statistik_wilayah_' . ($tahun ? (int)$tahun : 'latest');

        $statistic = Cache::remember($cacheKey, 3600, function () use ($tahun) {
            $query = RegionalStatistic::query();

            if ($tahun) {
                return $query->where('tahun', (int) $tahun)->first();
            }

            // Ambil data aktif tahun terbaru, atau fallback record pertama
            return $query->where('is_active', true)->orderByDesc('tahun')->first()
                ?? $query->orderByDesc('tahun')->first();
        });

        if (!$statistic) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' => $tahun 
                    ? "Data statistik wilayah untuk tahun anggaran {$tahun} tidak ditemukan."
                    : "Data statistik wilayah belum tersedia.",
                'data'    => null,
                'meta'    => [
                    'version'     => 'v1',
                    'server_time' => now()->toIso8601String(),
                ]
            ], 404);
        }

        $executionTimeMs = round((microtime(true) - $startTime) * 1000, 2);

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data statistik wilayah Kelurahan Semampir berhasil diambil.',
            'data'    => new RegionalStatisticResource($statistic),
            'meta'    => [
                'version'          => 'v1',
                'tahun_anggaran'   => $statistic->tahun,
                'cached'           => Cache::has($cacheKey),
                'execution_time'   => $executionTimeMs . ' ms',
                'server_time'      => now()->toIso8601String(),
            ]
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }
}
