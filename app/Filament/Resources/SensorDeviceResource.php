<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SensorDeviceResource\Pages;
use App\Filament\Resources\SensorDeviceResource\RelationManagers;
use App\Models\SensorDevice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SensorDeviceResource extends Resource
{
    protected static ?string $model = SensorDevice::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'Data Sensor';
    protected static ?string $navigationLabel = 'Kit Sensor';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('device_id')
                ->label('Device ID (Smart Citizen)')
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('ID numerik device dari platform sensor.smartcitizen.id, contoh: 19684')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('name')
                ->label('Nama Label (opsional)')
                ->placeholder('contoh: Node Tigaraksa')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('order')
                ->label('Urutan Tampil')
                ->numeric()
                ->default(0),

            Forms\Components\Toggle::make('is_active')
                ->label('Aktif / Ikut Dipantau di Dashboard')
                ->default(true)
                ->helperText('Nonaktifkan jika kit sedang rusak/dilepas, tanpa perlu menghapus datanya.'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')->label('Urutan')->sortable(),
                Tables\Columns\TextColumn::make('device_id')->label('Device ID')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('name')->label('Nama Label')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Didaftarkan')->dateTime('d M Y')->sortable(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListSensorDevices::route('/'),
            'create' => Pages\CreateSensorDevice::route('/create'),
            'edit' => Pages\EditSensorDevice::route('/{record}/edit'),
        ];
    }
}
