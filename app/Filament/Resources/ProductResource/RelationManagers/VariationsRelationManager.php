<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class VariationsRelationManager extends RelationManager
{
    protected static string $relationship = 'variations';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Variation Name')
                            ->required()
                            ->columnSpan('full'),

                        Forms\Components\TextInput::make('upc')
                            ->label('UPC')
                            ->columnSpan('full'),

                        Forms\Components\Select::make('unit_type')
                            ->label('Unit Type')
                            ->options([
                                'item' => 'Per Item',
                                'weight' => 'By Weight',
                            ])
                            ->required()
                            ->live()
                            ->columnSpan('full'),

                        Forms\Components\TextInput::make('unit_value')
                            ->label(fn (Forms\Get $get) => $get('unit_type') === 'weight' ? 'Weight Value' : 'Item Weight')
                            ->numeric()
                            ->columnSpan('full'),

                        Forms\Components\Select::make('weight_unit')
                            ->label('Unit of Measurement')
                            ->options([
                                'g' => 'Grams',
                                'kg' => 'Kilograms',
                            ])
                            ->default('g')
                            ->required()
                            ->columnSpan('full'),

                        Forms\Components\TextInput::make('retail_price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->columnSpan('full'),
                    ])
                    ->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('upc')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_type'),
                Tables\Columns\TextColumn::make('unit_value')
                    ->label('Weight (g)')
                    ->numeric(),
                Tables\Columns\TextColumn::make('retail_price')
                    ->money('USD')
                    ->label('Price'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
} 