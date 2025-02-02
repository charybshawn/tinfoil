<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\ViewField;

class InvoiceItemsField extends ViewField
{
    protected string $view = 'filament.forms.components.invoice-items-field';

    public static function make(string $name): static
    {
        return parent::make($name)
            ->columnSpanFull();
    }
} 