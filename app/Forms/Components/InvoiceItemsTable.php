<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Product;
use App\Models\Variation;
use Illuminate\Support\Collection;

class InvoiceItemsTable extends Repeater
{
    protected string $view = 'forms.components.invoice-items-table';

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        
        $static->schema([
            Select::make('product_id')
                ->label('Product')
                ->options(Product::query()->pluck('name', 'id'))
                ->required()
                ->live()
                ->afterStateUpdated(function (Set $set) {
                    $set('variation_id', null);
                    $set('unit_price', 0);
                }),

            Select::make('variation_id')
                ->label('Variation')
                ->options(function (Get $get) {
                    $productId = $get('product_id');
                    if (!$productId) return [];
                    
                    $product = Product::find($productId);
                    return $product?->variations()->pluck('name', 'id') ?? [];
                })
                ->required()
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set) {
                    $variation = Variation::find($get('variation_id'));
                    if ($variation) {
                        $set('unit_price', $variation->price);
                    }
                }),

            TextInput::make('quantity')
                ->numeric()
                ->default(1)
                ->required()
                ->minValue(1)
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set) {
                    $quantity = $get('quantity');
                    $unitPrice = $get('unit_price');
                    $set('subtotal', $quantity * $unitPrice);
                }),

            TextInput::make('unit_price')
                ->label('Unit Price')
                ->numeric()
                ->required()
                ->prefix('$')
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set) {
                    $quantity = $get('quantity');
                    $unitPrice = $get('unit_price');
                    $set('subtotal', $quantity * $unitPrice);
                }),

            TextInput::make('subtotal')
                ->label('Subtotal')
                ->disabled()
                ->prefix('$')
                ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                ->dehydrated(false),
        ])
        ->itemLabel(function (array $state): ?string {
            $product = Product::find($state['product_id'] ?? null);
            $variation = Variation::find($state['variation_id'] ?? null);
            return $product ? "{$product->name} - {$variation?->name}" : null;
        })
        ->addActionLabel('Add Item')
        ->reorderableWithButtons()
        ->defaultItems(0)
        ->columns(5)
        ->columnSpanFull();

        return $static;
    }
} 