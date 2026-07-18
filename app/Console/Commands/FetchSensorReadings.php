<?php

// app/Console/Commands/FetchSensorReadings.php
namespace App\Console\Commands;

use App\Models\SensorDevice;
use App\Models\SensorReading;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchSensorReadings extends Command
{
    protected $signature = 'sensors:fetch-readings';
    protected $description = 'Ambil data terbaru tiap sensor kit dan simpan sebagai riwayat';

    public function handle(): int
    {
        foreach (SensorDevice::activeDeviceIds() as $deviceId) {
            try {
                $response = Http::timeout(10)
                    ->get(config('services.smartcitizen.base_url') . $deviceId);

                if (! $response->successful()) {
                    $this->warn("Device {$deviceId}: gagal fetch ({$response->status()})");
                    continue;
                }

                $json = $response->json();
                $sensors = $json['data']['sensors'] ?? $json['sensors'] ?? [];

                SensorReading::create([
                    'device_id'   => $deviceId,
                    'temp'        => $this->findValue($sensors, ['temperature', 'air temperature', '°c', 'ºc']),
                    'rh'          => $this->findValue($sensors, ['humidity', 'relative humidity']),
                    'iaq'         => $this->findValue($sensors, ['iaq', 'air quality', 'air-freshness']),
                    'pressure'    => $this->findValue($sensors, ['pressure', 'barometric']),
                    'state'       => $json['state'] ?? null,
                    'recorded_at' => $json['last_recorded_at'] ?? now(),
                ]);
            } catch (\Throwable $e) {
                $this->error("Device {$deviceId}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }

    private function findValue(array $sensors, array $keywords): ?float
    {
        foreach ($sensors as $sensor) {
            $text = strtolower(implode(' ', array_filter([
                $sensor['name'] ?? null,
                $sensor['description'] ?? null,
                $sensor['unit'] ?? null,
                $sensor['measurement']['name'] ?? null,
                $sensor['measurement']['description'] ?? null,
            ])));

            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    return isset($sensor['value']) ? (float) $sensor['value'] : null;
                }
            }
        }

        return null;
    }
}
