<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->url(fn ($record) => InvoiceResource::getUrl('edit', ['record' => $record]))
                ->icon('heroicon-o-pencil'),
            Action::make('print')
                ->url(fn ($record) => route('invoice.print', $record))
                ->openUrlInNewTab()
                ->icon('heroicon-o-printer'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)
                    ->schema([
                        Section::make()
                            ->columnSpan(4)
                            ->schema([
                                // Customer details
                                \Filament\Forms\Components\TextInput::make('customer.name')
                                    ->label('Customer Name')
                                    ->disabled(),
                                \Filament\Forms\Components\TextInput::make('customer.email')
                                    ->label('Email')
                                    ->disabled(),
                                \Filament\Forms\Components\TextInput::make('customer.phone')
                                    ->label('Phone')
                                    ->disabled(),
                            ]),

                        Section::make()
                            ->columnSpan(8)
                            ->schema([
                                // Invoice details
                                \Filament\Forms\Components\TextInput::make('invoice_number')
                                    ->disabled(),
                                \Filament\Forms\Components\TextInput::make('total_amount')
                                    ->disabled()
                                    ->prefix('$'),
                                \Filament\Forms\Components\TextInput::make('status')
                                    ->disabled(),
                            ]),
                    ]),
            ]);
    }
} 