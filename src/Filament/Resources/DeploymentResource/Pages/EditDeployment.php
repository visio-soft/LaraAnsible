<?php

namespace VisioSoft\LaraAnsible\Filament\Resources\DeploymentResource\Pages;

use VisioSoft\LaraAnsible\Filament\Resources\DeploymentResource;
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

    protected function getFormActions(): array
    {
        // Remove Save/Cancel actions on Edit page - read-only & actions handled separately
        return [];
    }
}
