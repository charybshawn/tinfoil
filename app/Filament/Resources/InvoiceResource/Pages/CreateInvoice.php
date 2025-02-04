<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->action(function () {
                    $this->data['status'] = 'sent';
                    $this->create();
                    
                    Notification::make()
                        ->success()
                        ->title('Invoice created')
                        ->send();

                    return redirect()->to(InvoiceResource::getUrl('index'));
                })
                ->label('Save & Send')
                ->color('success')
                ->icon('heroicon-o-paper-airplane'),

            Action::make('draft')
                ->action(function () {
                    $this->data['status'] = 'draft';
                    $this->create();
                    
                    Notification::make()
                        ->success()
                        ->title('Draft saved')
                        ->send();

                    return redirect()->to(InvoiceResource::getUrl('index'));
                })
                ->label('Save as Draft')
                ->color('gray')
                ->icon('heroicon-o-document'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['number'] = Carbon::now()->format('Ymd') . '-' . 
            sprintf('%03d', \App\Models\Invoice::whereDate('created_at', Carbon::today())->count() + 1);

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

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }
} 