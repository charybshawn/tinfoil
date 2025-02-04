<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Group;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Invoice Details')
                            ->collapsible()
                            ->collapsed(false)
                            ->schema([
                                TextEntry::make('customer.name')
                                    ->label('Customer'),
                                TextEntry::make('number')
                                    ->label('Invoice ID'),
                                TextEntry::make('title')
                                    ->label('Invoice Title')
                                    ->columnSpan(3),
                                TextEntry::make('paymentTerms.name')
                                    ->label('Payment Terms'),
                                TextEntry::make('issue_date')
                                    ->label('Invoice Date')
                                    ->date(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public function getRelationManagers(): array
    {
        return [
            \App\Filament\Resources\InvoiceResource\RelationManagers\ItemsRelationManager::class,
            \App\Filament\Resources\InvoiceResource\RelationManagers\PaymentsRelationManager::class,
        ];
    }

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

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return false;
    }
} 