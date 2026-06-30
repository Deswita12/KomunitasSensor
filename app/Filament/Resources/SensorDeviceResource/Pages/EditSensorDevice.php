<?php

namespace App\Filament\Resources\SensorDeviceResource\Pages;

use App\Filament\Resources\SensorDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSensorDevice extends EditRecord
{
    protected static string $resource = SensorDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
