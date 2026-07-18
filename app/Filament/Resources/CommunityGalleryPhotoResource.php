<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommunityGalleryPhotoResource\Pages;
use App\Filament\Resources\CommunityGalleryPhotoResource\RelationManagers;
use App\Models\CommunityGalleryPhoto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommunityGalleryPhotoResource extends Resource
{
    protected static ?string $model = CommunityGalleryPhoto::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
protected static ?string $navigationGroup = 'Komunitas';
protected static ?string $navigationLabel = 'Slideshow Foto';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('image_path')
                ->label('Foto')
                ->image()
                ->disk('public')
                ->directory('community-gallery')
                ->visibility('public')
                ->required()
                ->columnSpanFull(),

            Forms\Components\TextInput::make('caption')
                ->label('Keterangan (opsional)')
                ->placeholder('contoh: Dokumentasi: Merakit Massal di Tigaraksa')
                ->columnSpanFull(),

            Forms\Components\Toggle::make('is_active')
                ->label('Tampilkan di Slideshow')
                ->default(true),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')->label('Foto'),
                Tables\Columns\TextColumn::make('caption')->label('Keterangan')->limit(50),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommunityGalleryPhotos::route('/'),
            'create' => Pages\CreateCommunityGalleryPhoto::route('/create'),
            'edit' => Pages\EditCommunityGalleryPhoto::route('/{record}/edit'),
        ];
    }
}
