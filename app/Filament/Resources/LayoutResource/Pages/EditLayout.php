<?php

namespace App\Filament\Resources\LayoutResource\Pages;

use App\Components\Forms\Actions\DeleteAction;
use App\Components\Forms\Actions\ReplicateAction;
use App\Components\Forms\Actions\SaveAction;
use App\Filament\Resources\LayoutResource;
use Filament\Forms;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditLayout extends EditRecord
{
    protected static string $resource = LayoutResource::class;
}
