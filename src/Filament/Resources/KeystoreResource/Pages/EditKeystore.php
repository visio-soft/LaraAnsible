<?php

namespace VisioSoft\LaraAnsible\Filament\Resources\KeystoreResource\Pages;

use VisioSoft\LaraAnsible\Filament\Resources\KeystoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKeystore extends EditRecord
{
    protected static string $resource = KeystoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
