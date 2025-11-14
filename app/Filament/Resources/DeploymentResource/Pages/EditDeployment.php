<?php

namespace App\Filament\Resources\DeploymentResource\Pages;

use App\Filament\Resources\DeploymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeployment extends EditRecord
{
    protected static string $resource = DeploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
