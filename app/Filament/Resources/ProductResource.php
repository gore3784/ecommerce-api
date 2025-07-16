<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Illuminate\Support\Str;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('description')->required(),
            Forms\Components\TextInput::make('price')
                ->required()
                ->numeric()
                ->prefix('Rp'),
            // Forms\Components\FileUpload::make('image')
            //     ->image()
            //     ->directory('products'),
            Forms\Components\TextInput::make('image')
                ->label('URL Gambar')
                ->url()
                ->placeholder('https://pixvid.org/images/2025/07/12/example.jpeg')
                ->rule('regex:/\.(jpg|jpeg|png|webp|gif)$/i')
                ->required(),
            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->required(),
            Forms\Components\TextInput::make('stock')
                ->required()
                ->numeric()
                ->minValue(0),
            Forms\Components\Toggle::make('is_featured')
                ->label('Featured Product')
                ->inline(false)
                ->default(false),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Tables\Columns\ImageColumn::make('image')->rounded(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->url(fn($record) => $record->image)
                    ->rounded(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->sortable(),
                Tables\Columns\TextColumn::make('price')->money('IDR'),
                Tables\Columns\TextColumn::make('stock'),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured Only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
