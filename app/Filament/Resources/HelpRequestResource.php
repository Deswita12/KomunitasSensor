<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HelpRequestResource\Pages;
use App\Filament\Resources\HelpRequestResource\RelationManagers;
use App\Models\HelpRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HelpRequestResource extends Resource
{
    protected static ?string $model = HelpRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Komunitas';
    protected static ?string $navigationLabel = 'Pesan Bantuan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama')->disabled(),
            Forms\Components\TextInput::make('email')->label('Email')->disabled(),
            Forms\Components\Textarea::make('message')->label('Pesan')->disabled()->columnSpanFull()->rows(5),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options(['new' => 'Baru', 'read' => 'Sudah Dibaca', 'replied' => 'Sudah Dibalas'])
                ->required(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal')->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('message')->label('Pesan')->limit(50),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options(['new' => 'Baru', 'read' => 'Sudah Dibaca', 'replied' => 'Sudah Dibalas']),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['new' => 'Baru', 'read' => 'Sudah Dibaca', 'replied' => 'Sudah Dibalas']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListHelpRequests::route('/'),
            'view'  => Pages\ViewHelpRequest::route('/{record}'),
        ];
    }
}
