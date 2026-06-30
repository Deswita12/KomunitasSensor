<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuideStepResource\Pages;
use App\Models\GuideStep;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GuideStepResource extends Resource
{
    protected static ?string $model = GuideStep::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Panduan';
    protected static ?string $navigationLabel = 'Step Wizard';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Dasar')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Step')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('type')
                        ->label('Model / Template Step')
                        ->options(GuideStep::typeLabels())
                        ->required()
                        ->live() // penting: supaya field di bawah ikut berubah saat type diganti
                        ->helperText('Pilih jenis konten step ini. Field di bawah akan menyesuaikan.'),

                    Forms\Components\TextInput::make('icon')
                        ->label('Icon (Material Symbols)')
                        ->placeholder('contoh: memory, thermostat, usb')
                        ->helperText('Lihat daftar nama icon di fonts.google.com/icons'),

                    Forms\Components\TextInput::make('order')
                        ->label('Urutan Tampil')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif / Tampilkan di Frontend')
                        ->default(true),

                    Forms\Components\Textarea::make('tip_text')
                        ->label('Tips dari Oyen (opsional)')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // ===== MODEL A: Teks + Gambar =====
            Forms\Components\Section::make('Konten: Teks + Gambar')
                ->schema([
                    Forms\Components\RichEditor::make('body_text')
                        ->label('Isi Teks')
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('image_path')
                        ->label('Gambar')
                        ->image()
                        ->directory('guide-steps/images')
                        ->columnSpanFull(),
                ])
                ->visible(fn (Forms\Get $get) => $get('type') === GuideStep::TYPE_TEXT_IMAGE),

            // ===== MODEL B: Video =====
            Forms\Components\Section::make('Konten: Video Tutorial')
                ->schema([
                    Forms\Components\FileUpload::make('video_path_basic')
                        ->label('Video untuk Paket Basic')
                        ->acceptedFileTypes(['video/mp4'])
                        ->directory('guide-steps/videos'),
                    Forms\Components\FileUpload::make('video_path_plus')
                        ->label('Video untuk Paket Plus')
                        ->acceptedFileTypes(['video/mp4'])
                        ->directory('guide-steps/videos'),
                    Forms\Components\Textarea::make('video_caption')
                        ->label('Keterangan Video')
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->visible(fn (Forms\Get $get) => $get('type') === GuideStep::TYPE_VIDEO),

            // ===== MODEL C: Tabel Wiring + Diagram =====
            Forms\Components\Section::make('Konten: Wiring & Diagram')
                ->schema([
                    Forms\Components\Textarea::make('warning_text')
                        ->label('Teks Peringatan (box merah)')
                        ->columnSpanFull(),

                    Forms\Components\Repeater::make('wiring_rows')
                        ->label('Baris Tabel Wiring')
                        ->schema([
                            Forms\Components\TextInput::make('sensor_pin')->label('Pin Sensor')->required(),
                            Forms\Components\TextInput::make('board_pin')->label('Pin Board')->required(),
                            Forms\Components\TextInput::make('function')->label('Fungsi')->required(),
                        ])
                        ->columns(3)
                        ->columnSpanFull()
                        ->defaultItems(1)
                        ->reorderable(),

                    Forms\Components\FileUpload::make('diagram_image_basic')
                        ->label('Diagram untuk Paket Basic')
                        ->image()
                        ->directory('guide-steps/diagrams'),
                    Forms\Components\FileUpload::make('diagram_image_plus')
                        ->label('Diagram untuk Paket Plus')
                        ->image()
                        ->directory('guide-steps/diagrams'),
                ])
                ->columns(2)
                ->visible(fn (Forms\Get $get) => $get('type') === GuideStep::TYPE_WIRING_DIAGRAM),

            // ===== MODEL D: Code Block =====
            Forms\Components\Section::make('Konten: Code Block')
                ->schema([
                    Forms\Components\Select::make('code_language')
                        ->label('Bahasa')
                        ->options([
                            'cpp'        => 'C++ (Arduino)',
                            'javascript' => 'JavaScript',
                            'python'     => 'Python',
                            'json'       => 'JSON',
                        ])
                        ->default('cpp')
                        ->required(),

                    Forms\Components\Textarea::make('code_content')
                        ->label('Isi Kode')
                        ->rows(14)
                        ->extraInputAttributes(['style' => 'font-family: monospace; font-size: 13px;'])
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('code_note')
                        ->label('Catatan / Tips di Bawah Kode')
                        ->columnSpanFull(),
                ])
                ->visible(fn (Forms\Get $get) => $get('type') === GuideStep::TYPE_CODE_BLOCK),

            // ===== MODEL E: Daftar Alat =====
            Forms\Components\Section::make('Konten: Daftar Alat (Basic/Plus)')
                ->schema([
                    Forms\Components\Textarea::make('tool_list_intro')
                        ->label('Teks Pengantar (opsional)')
                        ->columnSpanFull(),

                    Forms\Components\Repeater::make('toolItems')
                        ->relationship('toolItems')
                        ->label('Item Alat')
                        ->schema([
                            Forms\Components\TextInput::make('name')->label('Nama Alat')->required(),
                            Forms\Components\TextInput::make('description')->label('Deskripsi Singkat'),
                            Forms\Components\TextInput::make('icon')->label('Icon (Material Symbols)'),
                            Forms\Components\Select::make('package')
                                ->label('Paket')
                                ->options(['basic' => 'Basic', 'plus' => 'Plus', 'all' => 'Semua Paket'])
                                ->default('all')
                                ->required(),
                            Forms\Components\TextInput::make('order')->label('Urutan')->numeric()->default(0),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->reorderable()
                        ->orderColumn('order')
                        ->defaultItems(1),
                ])
                ->visible(fn (Forms\Get $get) => $get('type') === GuideStep::TYPE_TOOL_LIST),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')->label('Urutan')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Template')
                    ->formatStateUsing(fn (string $state) => GuideStep::typeLabels()[$state] ?? $state)
                    ->badge(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Diubah')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(GuideStep::typeLabels())
                    ->label('Template'),
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Aktif'),
            ])
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGuideSteps::route('/'),
            'create' => Pages\CreateGuideStep::route('/create'),
            'edit'   => Pages\EditGuideStep::route('/{record}/edit'),
        ];
    }
}