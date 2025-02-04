<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use App\Models\Invoice;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use App\Filament\Resources\InvoiceResource\RelationManagers\ItemsRelationManager;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    public ?Invoice $temporaryInvoice = null;

    public function mount(): void
    {
        parent::mount();
        
        // Create a temporary invoice for the relation manager
        $this->temporaryInvoice = Invoice::create([
            'number' => 'TEMP-' . uniqid(),
            'status' => 'draft',
            'issue_date' => now(),
            'customer_id' => 1, // Use your first customer or create a temporary customer
            'payment_terms_id' => 1, // Use your first payment terms or create a default
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
        ]);
    }

    protected function handleRecordCreation(array $data): Invoice
    {
        // Copy items from temporary invoice to new invoice
        $items = $this->temporaryInvoice->items()->get(); // Use get() to load items
        $this->temporaryInvoice->delete();

        $invoice = parent::handleRecordCreation($data);
        
        foreach ($items as $item) {
            $invoice->items()->create($item->toArray());
        }

        return $invoice;
    }

    public function getRecord(): ?Invoice
    {
        return $this->temporaryInvoice;
    }

    public function getRelationManagers(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    protected function hasRelationManagerTabsWithContent(): bool
    {
        return false;
    }

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

    protected function getCreateCustomerFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Company Name')
                ->required(),
            
            TextInput::make('email')
                ->email()
                ->required()
                ->unique('customers', 'email'),
            
            TagsInput::make('secondary_emails')
                ->label('Additional Email Addresses')
                ->placeholder('Add email address')
                ->separator(','),
            
            TextInput::make('phone')
                ->tel()
                ->label('Phone Number'),
            
            Select::make('group_id')
                ->relationship('group', 'name')
                ->required()
                ->createOptionForm([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('discount_percentage')
                        ->numeric()
                        ->label('Discount %')
                        ->suffix('%')
                        ->maxValue(100),
                ]),
            
            TextInput::make('street_address')
                ->label('Street Address'),
            
            TextInput::make('city'),
            
            TextInput::make('prov')
                ->label('Province/State')
                ->length(2),
            
            TextInput::make('postal_code')
                ->label('Postal/ZIP Code'),
            
            Select::make('country')
                ->default('Canada')
                ->options([
                    'Canada' => 'Canada',
                    'United States' => 'United States',
                ]),
            
            Textarea::make('notes')
                ->rows(3),
            
            Select::make('status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->default('active')
                ->required(),
        ];
    }
} 