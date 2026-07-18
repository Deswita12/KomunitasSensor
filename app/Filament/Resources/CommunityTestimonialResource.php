<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommunityTestimonialResource\Pages;
use App\Filament\Resources\CommunityTestimonialResource\RelationManagers;
use App\Models\CommunityTestimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommunityTestimonialResource extends Resource
{
    protected static ?string $model = CommunityTestimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Komunitas';
    protected static ?string $navigationLabel = 'Cerita Warga';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('author_name')->label('Nama Warga')->required(),
            Forms\Components\TextInput::make('tag')->label('Tag (opsional)')->placeholder('contoh: Tips Hardware'),
            Forms\Components\Textarea::make('message')->label('Isi Cerita/Komentar')->required()->rows(4)->columnSpanFull(),
            Forms\Components\Select::make('status')
                ->options(['pending' => 'Menunggu Tinjauan', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'])
                ->required(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author_name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('message')->label('Isi')->limit(60),
                Tables\Columns\TextColumn::make('tag')->label('Tag')->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Dikirim')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Menunggu Tinjauan', 'approved' => 'Disetujui', 'rejected' => 'Ditolak']),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')->icon('heroicon-o-check')->color('success')
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->action(function ($record) {
                        $record->update(['status' => 'approved', 'approved_at' => now()]);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')->icon('heroicon-o-x-mark')->color('danger')
                    ->visible(fn ($record) => $record->status !== 'rejected')
                    ->action(fn ($record) => $record->update(['status' => 'rejected'])),
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
            'index' => Pages\ListCommunityTestimonials::route('/'),
            'create' => Pages\CreateCommunityTestimonial::route('/create'),
            'edit' => Pages\EditCommunityTestimonial::route('/{record}/edit'),
        ];
    }
}
