<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SensorProxyController extends Controller
{
    public function smartCitizen(string $deviceId)
    {
        $cacheKey = "smartcitizen_{$deviceId}";

        $data = Cache::remember($cacheKey, 60, function () use ($deviceId) {
            $response = Http::timeout(10)
                ->get(config('services.smartcitizen.base_url') . $deviceId);

            return $response->successful() ? $response->json() : null;
        });

        if (! $data) {
            return response()->json(['error' => 'Gagal mengambil data Smart Citizen'], 502);
        }

        return response()->json($data);
    }

    public function bmkg(Request $request)
    {
        $adm4 = $request->query('adm4', '36.03.03.1001');
        $cacheKey = "bmkg_{$adm4}";

        $data = Cache::remember($cacheKey, 600, function () use ($adm4) {
            $response = Http::timeout(10)
                ->get(config('services.bmkg.base_url'), ['adm4' => $adm4]);

            return $response->successful() ? $response->json() : null;
        });

        if (! $data) {
            return response()->json(['error' => 'Gagal mengambil data BMKG'], 502);
        }

        return response()->json($data);
    }

    public function aiAnalysis(Request $request)
    {
        $validated = $request->validate([
            'temp'     => 'nullable|numeric',
            'rh'       => 'nullable|numeric',
            'iaq'      => 'nullable|numeric',
            'pressure' => 'nullable|numeric',
            'location' => 'nullable|string',
        ]);

        $cacheKey = 'ai_' . md5(json_encode($validated));

        $result = Cache::remember($cacheKey, 1800, function () use ($validated) {
            $prompt = "Kamu adalah analis lingkungan hidup untuk Kabupaten Tangerang. "
                . "Berikan analisis singkat (2-3 kalimat) dalam Bahasa Indonesia berdasarkan data sensor berikut:\n"
                . "- Suhu: {$validated['temp']} °C\n"
                . "- Kelembapan: {$validated['rh']} %\n"
                . "- IAQ/VOC Index: {$validated['iaq']}\n"
                . "- Tekanan Udara: {$validated['pressure']} hPa\n"
                . "- Lokasi: {$validated['location']}\n\n"
                . "Sebutkan kondisi udara saat ini, potensi risiko kesehatan atau lingkungan, "
                . "dan satu saran praktis. Jawab langsung tanpa pembuka atau salam.";

            $response = Http::timeout(15)
                ->post(config('services.gemini.url') . '?key=' . config('services.gemini.key'), [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['maxOutputTokens' => 200, 'temperature' => 0.4],
                ]);

            if (! $response->successful()) {
                return 'Analisis tidak tersedia saat ini.';
            }

            return $response->json('candidates.0.content.parts.0.text', 'Analisis tidak tersedia.');
        });

        return response()->json(['analysis' => $result]);
    }
}