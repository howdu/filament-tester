<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LayoutResource\Pages;
use App\Models\Layout;
use App\Models\Widget;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class LayoutResource extends Resource
{
    protected static ?string $model = Layout::class;

    protected static ?string $navigationIcon = 'heroicon-o-template';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),

                Forms\Components\Builder::make('containers')
                    ->blocks([
                        Forms\Components\Builder\Block::make('default')
                            ->label(fn($state): string => 'Default: '.(! empty($state['widgets']) ? ' ('.count($state['widgets']).')' : ''))
                            ->schema([
                                Forms\Components\Repeater::make('layoutWidgets')
                                    ->relationship('layoutWidgets')
                                    ->schema([
                                        Forms\Components\Hidden::make('container')
                                            ->dehydrateStateUsing(fn() => 'default'),

                                        Forms\Components\Select::make('widget_id')
                                            ->relationship('widget', 'name')
                                            ->reactive()
                                            ->required()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')->required()
                                            ]),

                                        Forms\Components\Group::make()
                                            ->relationship('widget')
                                            ->schema(function ($state): array {
                                                // Logic to dynamically fetch the widget schema
                                                return [];
                                            }),
                                ])
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight('bold')
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLayouts::route('/'),
            'create' => Pages\CreateLayout::route('/create'),
            'edit' => Pages\EditLayout::route('/{record}/edit'),
        ];
    }
}
