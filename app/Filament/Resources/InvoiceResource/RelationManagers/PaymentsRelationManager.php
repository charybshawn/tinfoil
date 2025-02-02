<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Payments';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('amount')
                ->required()
                ->numeric()
                ->prefix('$')
                ->default(fn ($livewire) => 
                    $livewire->ownerRecord->total - $livewire->ownerRecord->payments->sum('amount')),

            Forms\Components\Select::make('method')
                ->options([
                    'bank_transfer' => 'Bank Transfer',
                    'check' => 'Check',
                    'cash' => 'Cash',
                    'stripe' => 'Stripe',
                ])
                ->required(),

            Forms\Components\TextInput::make('reference_number')
                ->maxLength(255),

            Forms\Components\DatePicker::make('payment_date')
                ->required()
                ->default(now()),

            Forms\Components\Textarea::make('notes')
                ->rows(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'stripe' => 'success',
                        'bank_transfer' => 'info',
                        'check' => 'warning',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('payment_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'check' => 'Check',
                        'cash' => 'Cash',
                        'stripe' => 'Stripe',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($record) {
                        // Update invoice status based on payments
                        $invoice = $record->invoice;
                        $totalPaid = $invoice->payments->sum('amount');
                        
                        $newStatus = match (true) {
                            $totalPaid >= $invoice->total => 'paid',
                            $totalPaid > 0 => 'partial',
                            default => $invoice->status,
                        };
                        
                        $invoice->update(['status' => $newStatus]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        // Update invoice status based on payments
                        $invoice = $record->invoice;
                        $totalPaid = $invoice->payments->sum('amount');
                        
                        $newStatus = match (true) {
                            $totalPaid >= $invoice->total => 'paid',
                            $totalPaid > 0 => 'partial',
                            default => $invoice->status,
                        };
                        
                        $invoice->update(['status' => $newStatus]);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        // Update invoice status based on payments
                        $invoice = $record->invoice;
                        $totalPaid = $invoice->payments->sum('amount');
                        
                        $newStatus = match (true) {
                            $totalPaid >= $invoice->total => 'paid',
                            $totalPaid > 0 => 'partial',
                            default => 'sent',
                        };
                        
                        $invoice->update(['status' => $newStatus]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 