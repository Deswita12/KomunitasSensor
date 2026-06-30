<?php

namespace App\Filament\Resources\SensorDeviceResource\Pages;

use App\Filament\Resources\SensorDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSensorDevices extends ListRecords
{
    protected static string $resource = SensorDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
