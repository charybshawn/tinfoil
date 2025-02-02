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
use App\Models\Variation;
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
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('customer_id')
                                    ->relationship('customer', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                
                                Forms\Components\Select::make('payment_terms_id')
                                    ->relationship('paymentTerms', 'name')
                                    ->required()
                                    ->preload(),

                                Forms\Components\DatePicker::make('issue_date')
                                    ->required()
                                    ->default(now()),

                                Forms\Components\Toggle::make('is_recurring')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (!$state) {
                                            $set('recurring_frequency', null);
                                            $set('next_invoice_date', null);
                                        }
                                    }),

                                Forms\Components\Select::make('recurring_frequency')
                                    ->options([
                                        'weekly' => 'Weekly',
                                        'monthly' => 'Monthly',
                                        'quarterly' => 'Quarterly',
                                    ])
                                    ->visible(fn (Get $get) => $get('is_recurring'))
                                    ->required(fn (Get $get) => $get('is_recurring')),

                                Forms\Components\DatePicker::make('next_invoice_date')
                                    ->visible(fn (Get $get) => $get('is_recurring'))
                                    ->required(fn (Get $get) => $get('is_recurring')),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Items')
                            ->schema([
                                InvoiceItemsField::make('items')
                            ]),

                        Forms\Components\Section::make('Notes')
                            ->schema([
                                Forms\Components\Textarea::make('notes')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 3]),
            ])
            ->columns(3);
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