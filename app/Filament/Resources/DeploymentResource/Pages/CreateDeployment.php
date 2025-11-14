<?php

namespace App\Filament\Resources\DeploymentResource\Pages;

use App\Filament\Resources\DeploymentResource;
use Filament\Actions;
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
