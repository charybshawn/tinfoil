<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->url(fn (Transaction $record) => route('transaction.print', $record))
                ->openUrlInNewTab()
                ->icon('heroicon-o-printer'),
            Action::make('refund')
                ->action(function () {
                    // Add refund logic using Square SDK
                    // This will be implemented when we add the refund functionality
                })
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn ($record) => $record->status === 'completed'),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
} 