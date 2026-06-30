<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Komunitas';
    protected static ?string $navigationLabel = 'Aksi Nyata di Lapangan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Judul')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                            $set('slug', \Illuminate\Support\Str::slug($state)))
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('tag')
                        ->label('Tag/Label (opsional)')
                        ->placeholder('contoh: Sosialisasi, Pemasangan Node, Workshop'),

                    Forms\Components\TextInput::make('author_name')
                        ->label('Nama Penulis'),

                    Forms\Components\FileUpload::make('cover_image')
                        ->label('Foto Sampul')
                        ->image()
                        ->directory('posts')
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('excerpt')
                        ->label('Ringkasan Singkat (tampil di kartu daftar)')
                        ->rows(2)
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('content')
                        ->label('Isi Lengkap Blog')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Tanggal Publikasi'),

                    Forms\Components\Toggle::make('is_published')
                        ->label('Publikasikan')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')->label('Foto'),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('tag')->label('Tag'),
                Tables\Columns\IconColumn::make('is_published')->label('Publish')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->label('Tanggal')->date('d M Y')->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
