<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\ProductVariation;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Invoice Items';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_variation_id')
                ->relationship('productVariation', 'name')
                ->required()
                ->searchable()
                ->preload()
                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                    if ($variation = ProductVariation::find($state)) {
                        $set('unit_type', $variation->unit_type);
                        $set('unit_value', $variation->unit_value);
                        $set('weight_unit', $variation->weight_unit);
                        $set('price', $variation->wholesale_price ?? $variation->retail_price);
                    }
                }),

            Forms\Components\TextInput::make('quantity')
                ->required()
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->reactive()
                ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) => 
                    $set('line_total', $state * $get('price'))),

            Forms\Components\TextInput::make('price')
                ->required()
                ->numeric()
                ->prefix('$')
                ->reactive()
                ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) => 
                    $set('line_total', $state * $get('quantity'))),

            Forms\Components\TextInput::make('line_total')
                ->disabled()
                ->prefix('$')
                ->numeric(),

            Forms\Components\TextInput::make('unit_type')
                ->required(),

            Forms\Components\TextInput::make('unit_value')
                ->numeric(),

            Forms\Components\TextInput::make('weight_unit'),

            Forms\Components\Toggle::make('is_recurring')
                ->default(false),

            Forms\Components\Textarea::make('notes')
                ->rows(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('productVariation.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('line_total')
                    ->money()
                    ->state(fn ($record) => $record->quantity * $record->price)
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit_type'),

                Tables\Columns\IconColumn::make('is_recurring')
                    ->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($record, $data) {
                        // Recalculate invoice totals
                        $invoice = $record->invoice;
                        $subtotal = $invoice->items->sum(fn ($item) => $item->quantity * $item->price);
                        $tax = $subtotal * 0.1; // 10% tax
                        
                        $invoice->update([
                            'subtotal' => $subtotal,
                            'tax' => $tax,
                            'total' => $subtotal + $tax,
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record, $data) {
                        // Recalculate invoice totals
                        $invoice = $record->invoice;
                        $subtotal = $invoice->items->sum(fn ($item) => $item->quantity * $item->price);
                        $tax = $subtotal * 0.1; // 10% tax
                        
                        $invoice->update([
                            'subtotal' => $subtotal,
                            'tax' => $tax,
                            'total' => $subtotal + $tax,
                        ]);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        // Recalculate invoice totals
                        $invoice = $record->invoice;
                        $subtotal = $invoice->items->sum(fn ($item) => $item->quantity * $item->price);
                        $tax = $subtotal * 0.1; // 10% tax
                        
                        $invoice->update([
                            'subtotal' => $subtotal,
                            'tax' => $tax,
                            'total' => $subtotal + $tax,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 