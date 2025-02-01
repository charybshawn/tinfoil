<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Card;
use App\Models\Category;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Forms\Components\ViewField;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ProductResource\RelationManagers\VariationsRelationManager;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';
    
    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // First section: Name and Image, then Description below
            Forms\Components\Section::make()
                ->schema([
                    // First row: Name and Image
                    Forms\Components\Grid::make()
                        ->schema([
                            // Left side (name and category in a vertical stack)
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (string $state, Forms\Set $set) {
                                            $set('slug', Str::slug($state));
                                        })
                                        ->columnSpan(2),

                                    Forms\Components\Hidden::make('slug'),

                                    Forms\Components\Select::make('category_id')
                                        ->relationship('category', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->required()
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                                    if (!$state) return;

                                                    // Check for similar categories only when creating new category
                                                    $similar = Category::query()
                                                        ->get()
                                                        ->filter(function ($category) use ($state) {
                                                            $distance = levenshtein(strtolower($category->name), strtolower($state));
                                                            return $distance <= 2;
                                                        })
                                                        ->pluck('name')
                                                        ->toArray();

                                                    if (!empty($similar)) {
                                                        $similarList = implode(', ', $similar);
                                                        Notification::make()
                                                            ->warning()
                                                            ->title('Similar Categories Found')
                                                            ->body("Similar categories exist: {$similarList}")
                                                            ->persistent()
                                                            ->actions([
                                                                NotificationAction::make('continue')
                                                                    ->label('Create Anyway')
                                                                    ->close(),
                                                            ])
                                                            ->send();
                                                    }
                                                })
                                        ])
                                        ->createOptionUsing(
                                            function (string $value) {
                                                try {
                                                    // Check for exact match (case insensitive)
                                                    $exists = Category::query()
                                                        ->whereRaw('LOWER(name) = ?', [strtolower($value)])
                                                        ->exists();

                                                    if ($exists) {
                                                        Notification::make()
                                                            ->danger()
                                                            ->title('Error')
                                                            ->body('A category with this name already exists.')
                                                            ->send();
                                                        return null;
                                                    }

                                                    // Create the category
                                                    $category = Category::create(['name' => $value]);
                                                    
                                                    Notification::make()
                                                        ->success()
                                                        ->title('Success')
                                                        ->body("Category '{$value}' created successfully.")
                                                        ->send();

                                                    return ['id' => $category->id, 'name' => $category->name];

                                                } catch (\Exception $e) {
                                                    Notification::make()
                                                        ->danger()
                                                        ->title('Error')
                                                        ->body('Failed to create category.')
                                                        ->send();
                                                    return null;
                                                }
                                            }
                                        )
                                        ->createOptionModalHeading('Create Category')
                                        ->editOptionModalHeading('Edit Category')
                                        ->optionsLimit(15)
                                        ->searchDebounce(500)
                                        ->searchPrompt('Search categories...')
                                        ->noSearchResultsMessage('No categories found. Create one?')
                                        ->loadingMessage('Loading categories...')
                                        ->columnSpan(2),
                                ])
                                ->columns(2)
                                ->columnSpan(2),

                            // Right side (featured image)
                            Forms\Components\FileUpload::make('featured_image')
                                ->image()
                                ->imageEditor()
                                ->directory('products')
                                ->label('Featured Image')
                                ->imageResizeMode('cover')
                                ->imageCropAspectRatio('1:1')
                                ->imageResizeTargetWidth('300')
                                ->imageResizeTargetHeight('300')
                                ->columnSpan(1)
                                ->imagePreviewHeight('100')
                                ->panelLayout('compact'),
                        ])
                        ->columns(3),

                    // Second row: Description (full width)
                    Forms\Components\TextArea::make('description')
                        ->rows(6)
                        ->columnSpanFull(),
                ])
                ->collapsible(),

            // Second section: Gallery with drag and drop
            Forms\Components\Section::make('Product Gallery')
                ->schema([
                    Forms\Components\FileUpload::make('gallery')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->imageEditor()
                        ->directory('products/gallery')
                        ->downloadable()
                        ->columnSpanFull()
                        ->maxFiles(5)
                        ->helperText('Drag and drop images here, or click to browse'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('variations_count')
                    ->label('Item Price')
                    ->counts('variations')
                    ->formatStateUsing(fn (int $state): string => "{$state} " . str('price')->plural($state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            VariationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public function editVariation($variationId): void
    {
        $this->mountAction('edit_variation', [
            'record' => $variationId
        ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = $data['status'] ?? 'active';
        return $data;
    }

    public static function afterCreate(Model $record): void
    {
        // Handle variations for new product
        if (isset($record->data['variations']) && is_array($record->data['variations'])) {
            foreach ($record->data['variations'] as $variationData) {
                $record->variations()->create([
                    'name' => $variationData['name'],
                    'upc' => $variationData['upc'] ?? null,
                    'unit_type' => $variationData['unit_type'],
                    'weight' => $variationData['weight'] ?? null,
                    'weight_unit' => $variationData['weight_unit'] ?? 'g',
                    'price' => $variationData['price'],
                    'status' => 'active'
                ]);
            }
        }
    }

    public static function afterSave(Model $record): void
    {
        // Handle variations for existing product
        if (isset($record->variations) && is_array($record->variations)) {
            // Get existing variation IDs
            $existingIds = $record->variations()->pluck('id')->toArray();
            $updatedIds = [];

            foreach ($record->variations as $variationData) {
                if (isset($variationData['id'])) {
                    // Update existing variation
                    $record->variations()->where('id', $variationData['id'])->update([
                        'name' => $variationData['name'],
                        'upc' => $variationData['upc'] ?? null,
                        'unit_type' => $variationData['unit_type'],
                        'weight' => $variationData['weight'] ?? null,
                        'weight_unit' => $variationData['weight_unit'] ?? 'g',
                        'price' => $variationData['price'],
                    ]);
                    $updatedIds[] = $variationData['id'];
                } else {
                    // Create new variation
                    $newVariation = $record->variations()->create([
                        'name' => $variationData['name'],
                        'upc' => $variationData['upc'] ?? null,
                        'unit_type' => $variationData['unit_type'],
                        'weight' => $variationData['weight'] ?? null,
                        'weight_unit' => $variationData['weight_unit'] ?? 'g',
                        'price' => $variationData['price'],
                        'status' => 'active'
                    ]);
                    $updatedIds[] = $newVariation->id;
                }
            }

            // Remove variations that were deleted in the form
            $record->variations()
                ->whereIn('id', array_diff($existingIds, $updatedIds))
                ->delete();
        }
    }
}
