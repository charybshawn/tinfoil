<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariationsRelationManager extends RelationManager
{
    protected static string $relationship = 'variations';  // Changed from 'items'

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('variation_id')
                ->relationship('variation', 'name')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\TextInput::make('quantity')
                ->required()
                ->numeric()
                ->minValue(1),

            Forms\Components\TextInput::make('price')
                ->required()
                ->numeric()
                ->prefix('$'),

            Forms\Components\TextInput::make('unit_type')
                ->required(),

            Forms\Components\TextInput::make('unit_value')
                ->numeric(),

            Forms\Components\TextInput::make('weight_unit'),

            Forms\Components\Textarea::make('notes')
                ->rows(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('variation.name')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('unit_type'),
                
                Tables\Columns\TextColumn::make('unit_value'),
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