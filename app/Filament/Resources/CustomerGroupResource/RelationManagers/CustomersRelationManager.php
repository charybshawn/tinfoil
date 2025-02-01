<?php

namespace App\Filament\Resources\CustomerGroupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CustomersRelationManager extends RelationManager
{
    protected static string $relationship = 'customers';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Customer Name')
                        ->required()
                        ->columnSpan('full'),

                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->columnSpan('full'),

                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->columnSpan('full'),

                    Forms\Components\TextInput::make('address')
                        ->columnSpan('full'),

                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('city'),
                            Forms\Components\TextInput::make('state'),
                            Forms\Components\TextInput::make('postal_code'),
                        ])
                        ->columns(3)
                        ->columnSpan('full'),

                    Forms\Components\Textarea::make('notes')
                        ->rows(3)
                        ->columnSpan('full'),
                ])
                ->columns(1),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 