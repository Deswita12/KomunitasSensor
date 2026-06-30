<?php

namespace App\Filament\Resources\CommunityVideoResource\Pages;

use App\Filament\Resources\CommunityVideoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommunityVideo extends EditRecord
{
    protected static string $resource = CommunityVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
