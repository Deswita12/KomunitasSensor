<?php

namespace App\Filament\Resources\CommunityTestimonialResource\Pages;

use App\Filament\Resources\CommunityTestimonialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommunityTestimonials extends ListRecords
{
    protected static string $resource = CommunityTestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
