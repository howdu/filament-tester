<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ReplicateAction::make()
                ->form(ProductResource::getFormSchema())
                ->action(function (Model $record, array $data, $form) {
                    $replica = $record->replicate();
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

                    $this->redirect(ProductResource::getUrl('edit', $replica->getKey()));
                })
        ];
    }
}
