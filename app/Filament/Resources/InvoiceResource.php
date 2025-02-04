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
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Forms\Components\TagsInput;

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
                        Section::make('Invoice Details')
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
                                            ->label('Company Name')
                                            ->required(),
                                        
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->unique('customers', 'email'),
                                        
                                        Forms\Components\TagsInput::make('secondary_emails')
                                            ->label('Additional Email Addresses')
                                            ->placeholder('Add email address')
                                            ->separator(','),
                                        
                                        Forms\Components\TextInput::make('phone')
                                            ->tel()
                                            ->label('Phone Number'),
                                        
                                        Forms\Components\Select::make('group_id')
                                            ->relationship('group', 'name')
                                            ->required()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->required(),
                                                Forms\Components\TextInput::make('discount_percentage')
                                                    ->numeric()
                                                    ->label('Discount %')
                                                    ->suffix('%')
                                                    ->maxValue(100),
                                            ]),
                                        
                                        Forms\Components\TextInput::make('street_address')
                                            ->label('Street Address'),
                                        
                                        Forms\Components\TextInput::make('city'),
                                        
                                        Forms\Components\TextInput::make('prov')
                                            ->label('Province/State')
                                            ->length(2),
                                        
                                        Forms\Components\TextInput::make('postal_code')
                                            ->label('Postal/ZIP Code'),
                                        
                                        Forms\Components\Select::make('country')
                                            ->default('Canada')
                                            ->options([
                                                'Canada' => 'Canada',
                                                'United States' => 'United States',
                                            ]),
                                        
                                        Forms\Components\Textarea::make('notes')
                                            ->rows(3),
                                        
                                        Forms\Components\Select::make('status')
                                            ->options([
                                                'active' => 'Active',
                                                'inactive' => 'Inactive',
                                            ])
                                            ->default('active')
                                            ->required(),
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