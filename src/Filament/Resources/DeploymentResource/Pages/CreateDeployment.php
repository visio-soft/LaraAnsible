<?php

namespace VisioSoft\LaraAnsible\Filament\Resources\DeploymentResource\Pages;

use VisioSoft\LaraAnsible\Filament\Resources\DeploymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeployment extends CreateRecord
{
    protected static string $resource = DeploymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';

        return $data;
    }
}
