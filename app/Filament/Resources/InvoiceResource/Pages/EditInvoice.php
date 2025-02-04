<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

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
                ->color('success')
                ->icon('heroicon-o-check'),
            Action::make('print')
                ->url(fn ($record) => route('invoice.print', $record))
                ->openUrlInNewTab()
                ->icon('heroicon-o-printer'),
            Action::make('saveDraft')
                ->label('Save Draft')
                ->color('info')
                ->icon('heroicon-m-document-arrow-down')
                ->action(function (array $data) {
                    $data['status'] = 'draft';
                    $this->record->update($data);
                    
                    $this->notify('success', 'Invoice saved as draft');
                }),
            DeleteAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
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
} 