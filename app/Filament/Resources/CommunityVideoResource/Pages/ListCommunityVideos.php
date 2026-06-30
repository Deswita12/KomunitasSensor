<?php

namespace App\Filament\Resources\CommunityVideoResource\Pages;

use App\Filament\Resources\CommunityVideoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommunityVideos extends ListRecords
{
    protected static string $resource = CommunityVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
