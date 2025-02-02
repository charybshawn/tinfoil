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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['number'] = Carbon::now()->format('Ymd') . '-' . 
            sprintf('%03d', \App\Models\Invoice::whereDate('created_at', Carbon::today())->count() + 1);
        $data['status'] = 'sent';

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->action(function () {
                    $this->save();
                    
                    Notification::make()
                        ->success()
                        ->title('Created successfully')
                        ->send();

                    return redirect()->to(InvoiceResource::getUrl('index'));
                })
                ->label('Save')
                ->color('success')
                ->icon('heroicon-o-check'),
            Action::make('saveDraft')
                ->label('Save Draft')
                ->color('info')
                ->icon('heroicon-m-document-arrow-down')
                ->action(function (array $data) {
                    $data['status'] = 'draft';
                    $this->record = $this->handleRecordCreation($data);
                    
                    return redirect()->to(
                        $this->getResource()::getUrl('edit', ['record' => $this->record])
                    );
                }),
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
} 