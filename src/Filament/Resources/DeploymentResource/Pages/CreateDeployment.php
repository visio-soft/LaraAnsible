<?php

namespace VisioSoft\LaraAnsible\Filament\Resources\DeploymentResource\Pages;

use VisioSoft\LaraAnsible\Filament\Resources\DeploymentResource;
use VisioSoft\LaraAnsible\Jobs\ExecuteAnsibleDeployment;
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

    protected function afterCreate(): void
    {
        // Immediately kick off the deployment after it is created
        $this->record->update(['status' => 'running']);

        ExecuteAnsibleDeployment::dispatch($this->record);
    }

    protected function getRedirectUrl(): string
    {
        return DeploymentResource::getUrl('edit', ['record' => $this->record]);
    }
}
