<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommunityVideoResource\Pages;
use App\Filament\Resources\CommunityVideoResource\RelationManagers;
use App\Models\CommunityVideo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommunityVideoResource extends Resource
{
    protected static ?string $model = CommunityVideo::class;
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationGroup = 'Komunitas';
    protected static ?string $navigationLabel = 'Video Cuplikan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('Judul Video')
                ->required()
                ->placeholder('contoh: Dokumentasi: Merakit Massal di Tigaraksa')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('subtitle')
                ->label('Keterangan (opsional)')
                ->placeholder('contoh: Bengkel Udara Community x Pemkab Tangerang (0:45)')
                ->columnSpanFull(),

            Forms\Components\FileUpload::make('video_path')
                ->label('Upload Video (MP4)')
                ->disk('public')
                ->directory('community-videos')
                ->visibility('public')
                ->acceptedFileTypes(['video/mp4'])
                ->downloadable()
                ->openable()
                ->helperText('Isi salah satu: upload file ATAU isi link di bawah, tidak perlu keduanya.'),

            Forms\Components\TextInput::make('embed_url')
                ->label('Atau Link YouTube/Vimeo')
                ->url()
                ->placeholder('https://youtube.com/watch?v=...'),

            Forms\Components\FileUpload::make('thumbnail_path')
                ->image()
                ->disk('public')
                ->directory('community-videos/thumbnails')
                ->visibility('public'),

            Forms\Components\Toggle::make('is_featured')
                ->label('Tampilkan di Halaman Komunitas')
                ->helperText('Mengaktifkan ini otomatis menonaktifkan video lain yang sedang tampil.')
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_path')->label('Thumbnail'),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->limit(50),
                Tables\Columns\IconColumn::make('is_featured')->label('Tampil di Komunitas')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Diunggah')->dateTime('d M Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListCommunityVideos::route('/'),
            'create' => Pages\CreateCommunityVideo::route('/create'),
            'edit' => Pages\EditCommunityVideo::route('/{record}/edit'),
        ];
    }
}
