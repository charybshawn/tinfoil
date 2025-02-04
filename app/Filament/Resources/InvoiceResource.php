<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;
use App\Models\PaymentTerms;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater\ItemContainer;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Concerns;
use App\Livewire\InvoiceItemsManager;
use App\Filament\Forms\Components\InvoiceItemsField;
use Filament\Forms\Components\Group;
use App\Models\ProductVariation;
use App\Models\Variation;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Forms\Components\Section::make('Invoice Details')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                Forms\Components\Select::make('customer_id')
                                    ->label('Customer')
                                    ->relationship('customer', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->maxLength(255),
                                        
                                        Forms\Components\TextInput::make('phone')
                                            ->tel()
                                            ->maxLength(255),
                                        
                                        Forms\Components\Textarea::make('address')
                                            ->rows(3)
                                            ->maxLength(500),
                                        
                                        Forms\Components\TextInput::make('tax_number')
                                            ->maxLength(255),
                                    ])
                                    ->createOptionAction(function (Action $action) {
                                        return $action
                                            ->modalHeading('Create Customer')
                                            ->modalButton('Create and Select')
                                            ->modalWidth('lg');
                                    }),
                                Forms\Components\TextInput::make('number')
                                    ->label('Invoice ID')
                                    ->default(function () {
                                        return Invoice::generateInvoiceNumber();
                                    })
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->columnSpan(1),
                                
                                Forms\Components\TextInput::make('title')
                                    ->label('Invoice Title')
                                    ->required()
                                    ->columnSpan(3),

                                Forms\Components\Select::make('payment_terms_id')
                                    ->label('Payment Terms')
                                    ->relationship('paymentTerms', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\DatePicker::make('issue_date')
                                    ->label('Invoice Date')
                                    ->required()
                                    ->default(now())
                                    ->columnSpan(1),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Line Items')
                            ->collapsible()
                            ->schema([
                                Repeater::make('items')
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->label('Product')
                                            ->options(Product::query()->pluck('name', 'id'))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set) {
                                                $set('variation_id', null);
                                                $set('price', null);
                                                $set('subtotal', null);
                                            }),

                                        Forms\Components\Select::make('variation_id')
                                            ->label('Variation')
                                            ->options(function (Get $get) {
                                                $productId = $get('product_id');
                                                if (!$productId) {
                                                    return [];
                                                }
                                                
                                                return ProductVariation::query()
                                                    ->where('product_id', $productId)
                                                    ->pluck('name', 'id');
                                            })
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                if ($variation = ProductVariation::find($state)) {
                                                    $set('price', $variation->retail_price);
                                                }
                                            })
                                            ->searchable(),

                                        Forms\Components\TextInput::make('quantity')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $price = (float)$get('price');
                                                $quantity = (float)$state;
                                                $set('subtotal', $price * $quantity);
                                            }),

                                        Forms\Components\TextInput::make('price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('$')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('subtotal')
                                            ->disabled()
                                            ->prefix('$')
                                            ->numeric(),
                                    ])
                                    ->columns(5)
                                    ->defaultItems(1)
                                    ->reorderableWithButtons()
                            ])
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'overdue' => 'danger',
                        'sent' => 'info',
                        default => 'secondary',
                    }),

                Tables\Columns\IconColumn::make('is_recurring')
                    ->boolean(),

                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'partial' => 'Partially Paid',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'view' => Pages\ViewInvoice::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_invoices');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_invoices');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('edit_invoices');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('delete_invoices');
    }
} 