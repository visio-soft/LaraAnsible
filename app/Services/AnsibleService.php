<?php

namespace App\Services;

use App\Models\Deployment;
use App\Models\Inventory;
use App\Models\Keystore;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class AnsibleService
{
    /**
     * Execute an Ansible deployment
     */
    public function executeDeployment(Deployment $deployment): void
    {
        $deployment->update([
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            // Create temporary inventory file
            $inventoryPath = $this->createInventoryFile($deployment);
            
            // Create temporary playbook file if needed
            $playbookPath = $this->createPlaybookFile($deployment);
            
            // Build ansible-playbook command
            $command = $this->buildAnsibleCommand($deployment, $inventoryPath, $playbookPath);
            
            // Execute the command
            $result = Process::run($command);
            
            // Store output and update deployment
            $deployment->update([
                'status' => $result->successful() ? 'success' : 'failed',
                'console_output' => $result->output(),
                'exit_code' => $result->exitCode(),
                'completed_at' => now(),
            ]);

            // Clean up temporary files
            $this->cleanup($inventoryPath, $playbookPath);
            
        } catch (\Exception $e) {
            $deployment->update([
                'status' => 'failed',
                'console_output' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    /**
     * Create temporary inventory file
     */
    protected function createInventoryFile(Deployment $deployment): string
    {
        $inventoryIds = $deployment->inventory_ids ?? [];
        $inventories = Inventory::whereIn('id', $inventoryIds)->get();

        $content = "[all]\n";
        foreach ($inventories as $inventory) {
            $line = "{$inventory->hostname} ansible_port={$inventory->port} ansible_user={$inventory->username}";
            
            if ($inventory->keystore) {
                $keyPath = $this->createTempKeyFile($inventory->keystore);
                $line .= " ansible_ssh_private_key_file={$keyPath}";
            }
            
            $content .= $line . "\n";
        }

        $path = storage_path('app/ansible/inventory_' . $deployment->id . '.ini');
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($path, $content);

        return $path;
    }

    /**
     * Create temporary playbook file
     */
    protected function createPlaybookFile(Deployment $deployment): ?string
    {
        $taskTemplate = $deployment->taskTemplate;
        
        if ($taskTemplate->playbook_content) {
            $path = storage_path('app/ansible/playbook_' . $deployment->id . '.yml');
            file_put_contents($path, $taskTemplate->playbook_content);
            return $path;
        }

        return $taskTemplate->playbook_path;
    }

    /**
     * Create temporary SSH key file
     */
    protected function createTempKeyFile(Keystore $keystore): string
    {
        $path = storage_path('app/ansible/keys/key_' . $keystore->id);
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }
        file_put_contents($path, $keystore->private_key);
        chmod($path, 0600);

        return $path;
    }

    /**
     * Build Ansible command
     */
    protected function buildAnsibleCommand(Deployment $deployment, string $inventoryPath, ?string $playbookPath): string
    {
        $command = "ansible-playbook -i {$inventoryPath}";

        if ($playbookPath) {
            $command .= " {$playbookPath}";
        }

        // Add environment variables
        if ($deployment->environment && $deployment->environment->variables) {
            foreach ($deployment->environment->variables as $key => $value) {
                $command = "{$key}={$value} " . $command;
            }
        }

        // Add extra vars from task template
        if ($deployment->taskTemplate->extra_vars) {
            $extraVars = json_encode($deployment->taskTemplate->extra_vars);
            $command .= " --extra-vars '{$extraVars}'";
        }

        return $command;
    }

    /**
     * Clean up temporary files
     */
    protected function cleanup(string $inventoryPath, ?string $playbookPath): void
    {
        if (file_exists($inventoryPath)) {
            unlink($inventoryPath);
        }

        if ($playbookPath && file_exists($playbookPath) && str_contains($playbookPath, 'app/ansible/playbook_')) {
            unlink($playbookPath);
        }
    }
}
