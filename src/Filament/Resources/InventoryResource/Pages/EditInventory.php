<?php

namespace VisioSoft\LaraAnsible\Filament\Resources\InventoryResource\Pages;

use VisioSoft\LaraAnsible\Filament\Resources\InventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventory extends EditRecord
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
