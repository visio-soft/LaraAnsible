<?php

namespace App\Filament\Resources\KeystoreResource\Pages;

use App\Filament\Resources\KeystoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKeystores extends ListRecords
{
    protected static string $resource = KeystoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
