<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use Filament\Actions\ActionGroup;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    public function getRelationManagers(): array
    {
        return [
            \App\Filament\Resources\InvoiceResource\RelationManagers\ItemsRelationManager::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->action(function () {
                    $this->save();
                    
                    Notification::make()
                        ->success()
                        ->title('Saved successfully')
                        ->send();

                    return redirect()->to(InvoiceResource::getUrl('index'));
                })
                ->label('Save')
                ->color('success'),
            Action::make('send')
                ->action(function () {
                    $this->record->sendEmail();
                    
                    Notification::make()
                        ->success()
                        ->title('Invoice sent successfully')
                        ->send();
                })
                ->label('Send Invoice')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane')
                ->visible(fn () => $this->record->status !== 'sent'),
            Action::make('saveDraft')
                ->label('Draft')
                ->color('gray')
                ->action(function (array $data) {
                    $data['status'] = 'draft';
                    $this->record->update($data);
                    
                    $this->notify('success', 'Invoice saved as draft');
                }),
            ActionGroup::make([
                Action::make('print')
                    ->url(fn ($record) => route('invoice.print', $record))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-printer'),
                DeleteAction::make(),
            ])->grouped()->label('Actions'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!isset($data['status'])) {
            $data['status'] = 'sent';
        }
        
        // Make sure any item data uses product_variation_id
        if (isset($data['items'])) {
            foreach ($data['items'] as &$item) {
                if (isset($item['variation_id'])) {
                    $item['product_variation_id'] = $item['variation_id'];
                    unset($item['variation_id']);
                }
            }
        }
        
        return $data;
    }

    protected function hasRelationManagerTabsWithContent(): bool
    {
        return false;
    }
} 