<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\SelectColumn;
use App\Models\ProductVariation;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Line Items';
    protected static ?string $recordTitleAttribute = 'id';
    protected static bool $isCollapsible = true;
    protected static bool $isCollapsed = false;

    public function isInline(): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_variation_id')
                    ->relationship('productVariation', 'name')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('unit_type')
                    ->required(),
                Forms\Components\TextInput::make('unit_value')
                    ->nullable(),
                Forms\Components\TextInput::make('weight_unit')
                    ->nullable(),
                Forms\Components\Toggle::make('is_recurring')
                    ->default(false),
                Forms\Components\Textarea::make('notes')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('productVariation.product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                SelectColumn::make('product_variation_id')
                    ->label('Variation')
                    ->options(function ($record) {
                        if ($record->productVariation?->product) {
                            return $record->productVariation->product
                                ->variations()
                                ->pluck('name', 'id');
                        }
                        return [];
                    })
                    ->searchable()
                    ->rules(['required']),
                TextInputColumn::make('quantity')
                    ->type('number')
                    ->rules(['required', 'numeric', 'min:1'])
                    ->sortable(),
                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                TextInputColumn::make('price_input')
                    ->label('Price')
                    ->type('number')
                    ->step('0.01')
                    ->rules(['required', 'numeric', 'min:0'])
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        $record->price = $state;
                        $record->save();
                        $record->invoice->updateTotals();
                    }),
                TextColumn::make('line_total')
                    ->money('USD')
                    ->sortable(),
                TextInputColumn::make('unit_type')
                    ->rules(['required']),
                TextInputColumn::make('unit_value')
                    ->type('number')
                    ->rules(['nullable', 'numeric']),
                TextInputColumn::make('weight_unit')
                    ->rules(['nullable', 'string']),
                Tables\Columns\ToggleColumn::make('is_recurring')
                    ->sortable(),
                TextInputColumn::make('notes')
                    ->rules(['nullable', 'string'])
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->form([
                        Forms\Components\Select::make('product_variation_id')
                            ->label('Product')
                            ->relationship('productVariation', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($variation = ProductVariation::find($state)) {
                                    $set('price', $variation->retail_price);
                                    $set('unit_type', $variation->unit_type);
                                }
                            }),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->live()
                            ->afterStateUpdated(function ($state, $get, Forms\Set $set) {
                                $price = (float) $get('price');
                                $quantity = (float) $state;
                                $set('line_total', $price * $quantity);
                            }),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->live()
                            ->afterStateUpdated(function ($state, $get, Forms\Set $set) {
                                $quantity = (float) $get('quantity');
                                $price = (float) $state;
                                $set('line_total', $price * $quantity);
                            }),
                        Forms\Components\TextInput::make('line_total')
                            ->disabled()
                            ->prefix('$')
                            ->numeric(),
                        Forms\Components\TextInput::make('unit_type')
                            ->required(),
                        Forms\Components\TextInput::make('unit_value')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('weight_unit')
                            ->nullable(),
                        Forms\Components\Toggle::make('is_recurring')
                            ->default(false),
                        Forms\Components\Textarea::make('notes')
                            ->nullable()
                            ->columnSpan('full'),
                    ])
                    ->after(function ($data, $record) {
                        // Recalculate invoice totals after creating item
                        $invoice = $record->invoice;
                        $invoice->updateTotals();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($data, $record) {
                        // Recalculate invoice totals after editing item
                        $invoice = $record->invoice;
                        $invoice->updateTotals();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($data, $record) {
                        // Recalculate invoice totals after deleting item
                        $invoice = $record->invoice;
                        $invoice->updateTotals();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function ($data, $records) {
                            // Get the invoice from the first record (they should all be from the same invoice)
                            if ($records->isNotEmpty()) {
                                $invoice = $records->first()->invoice;
                                $invoice->updateTotals();
                            }
                        }),
                ]),
            ]);
    }
} 