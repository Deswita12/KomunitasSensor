<?php

namespace App\Filament\Resources\GuideStepResource\Pages;

use App\Filament\Resources\GuideStepResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuideStep extends EditRecord
{
    protected static string $resource = GuideStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
