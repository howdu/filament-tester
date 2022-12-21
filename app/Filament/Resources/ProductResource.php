<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function getFormSchema($create = true): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required(),
            Forms\Components\Repeater::make('attributes')
                ->relationship()
                ->required()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('key'),
                            Forms\Components\TextInput::make('value'),
                            Forms\Components\Select::make('group_id')
                                ->relationship('group', 'name')
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                ])
                        ])
                ])
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('attributes_count')
                    ->label('Attributes')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Comments')
                    ->alignCenter()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ReplicateAction::make()
                    ->form(ProductResource::getFormSchema())
                    ->action(function (Model $record, array $data, Tables\Actions\ReplicateAction $action, $form) {
                        $replica = $record->replicate(['attributes_count', 'comments_count']);
                        $replica->name = $data['name'];

                        if (!$replica->save()) {
                            return false;
                        }

                        // Manually save attributes relationship
                        foreach($form->getComponents() as $component) {
                            if (!$component instanceof Forms\Components\Repeater ||
                                $component->getStatePath(false) !== 'attributes') {
                                continue;
                            }

                            $childComponentContainers = $component->getChildComponentContainers();

                            foreach ($childComponentContainers as $item) {
                                $itemData = $item->getState(shouldCallHooksBefore: false);

                                $replica->attributes()->create($itemData);
                            }
                        }

                        Notification::make()
                            ->title('Product replicated successfully')
                            ->success()
                            ->send();

                        $action->redirect(ProductResource::getUrl('edit', $replica->getKey()));
                    })
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['attributes', 'comments'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
