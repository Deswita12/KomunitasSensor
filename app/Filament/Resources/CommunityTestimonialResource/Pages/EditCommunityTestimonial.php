<?php

namespace App\Filament\Resources\CommunityTestimonialResource\Pages;

use App\Filament\Resources\CommunityTestimonialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommunityTestimonial extends EditRecord
{
    protected static string $resource = CommunityTestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
