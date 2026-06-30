<?php

namespace App\Filament\Resources\GuideStepResource\Pages;

use App\Filament\Resources\GuideStepResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGuideSteps extends ListRecords
{
    protected static string $resource = GuideStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
