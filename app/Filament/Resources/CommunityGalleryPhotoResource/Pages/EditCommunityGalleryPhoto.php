<?php

namespace App\Filament\Resources\CommunityGalleryPhotoResource\Pages;

use App\Filament\Resources\CommunityGalleryPhotoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommunityGalleryPhoto extends EditRecord
{
    protected static string $resource = CommunityGalleryPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
