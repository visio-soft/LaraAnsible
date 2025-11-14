<?php

namespace App\Filament\Resources\DeploymentResource\Pages;

use App\Filament\Resources\DeploymentResource;
use App\Jobs\ExecuteAnsibleDeployment;
use App\Models\Deployment;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeployment extends EditRecord
{
    protected static string $resource = DeploymentResource::class;

    // Auto-refresh every 2 seconds if deployment is running
    protected static ?string $pollingInterval = '2s';

    public function getPollingInterval(): ?string
    {
        if (in_array($this->record->status, ['pending', 'running'])) {
            return '2s';
        }

        return null; // Stop polling when deployment is complete
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('retry')
                ->label('Retry Deployment')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function () {
                    // Create a new deployment with same parameters
                    $newDeployment = Deployment::create([
                        'task_template_id' => $this->record->task_template_id,
                        'user_id' => auth()->id(),
                        'inventory_ids' => $this->record->inventory_ids,
                        'status' => 'pending',
                    ]);

                    // Dispatch the job
                    ExecuteAnsibleDeployment::dispatch($newDeployment);
                    $newDeployment->update(['status' => 'running']);

                    // Redirect to the new deployment
                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $newDeployment]));
                })
                ->requiresConfirmation()
                ->modalHeading('Retry Deployment')
                ->modalDescription('This will create a new deployment with the same parameters and execute it.')
                ->modalSubmitActionLabel('Retry'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return []; // Remove all form actions (Save, Cancel)
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Make form read-only by returning data as-is
        return $data;
    }
}
