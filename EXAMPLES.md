# Example Usage

This document provides examples of how to use the LaraAnsible plugin in your FilamentPHP 3 application.

## Basic Setup

### 1. Install the Plugin

Add to your `composer.json`:

```json
{
    "require": {
        "visio-soft/laraansible": "^1.0"
    }
}
```

Or install via Composer:

```bash
composer require visio-soft/laraansible
```

### 2. Register the Plugin

In your Panel Provider (`app/Providers/Filament/AdminPanelProvider.php`):

```php
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use VisioSoft\LaraAnsible\LaraAnsiblePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->plugin(LaraAnsiblePlugin::make())
            // ... other configuration
            ;
    }
}
```

### 3. Run Migrations

```bash
php artisan migrate
```

## Using Resources

Once the plugin is registered, the following resources will be available in your admin panel:

- **Inventory** - at `/admin/inventories`
- **Keystores** - at `/admin/keystores`
- **Task Templates** - at `/admin/task-templates`
- **Deployments** - at `/admin/deployments`

## Using Widgets

### Add to Dashboard

To display the LaraAnsible widgets on your dashboard, add them to your panel configuration:

```php
use VisioSoft\LaraAnsible\Filament\Widgets\DeploymentStatsWidget;
use VisioSoft\LaraAnsible\Filament\Widgets\LatestDeployments;

public function panel(Panel $panel): Panel
{
    return $panel
        ->widgets([
            DeploymentStatsWidget::class,
            LatestDeployments::class,
        ])
        // ... other configuration
        ;
}
```

## Programmatic Usage

### Create an Inventory Programmatically

```php
use VisioSoft\LaraAnsible\Models\Inventory;
use VisioSoft\LaraAnsible\Models\Keystore;

// First, create a keystore
$keystore = Keystore::create([
    'name' => 'Production SSH Key',
    'description' => 'SSH key for production servers',
    'type' => 'ssh',
    'private_key' => file_get_contents('/path/to/private/key'),
    'public_key' => file_get_contents('/path/to/public/key'),
]);

// Then create an inventory
$inventory = Inventory::create([
    'name' => 'Web Server 1',
    'description' => 'Production web server',
    'hostname' => '192.168.1.100',
    'port' => 22,
    'username' => 'deploy',
    'keystore_id' => $keystore->id,
    'is_active' => true,
]);
```

### Create a Task Template

```php
use VisioSoft\LaraAnsible\Models\TaskTemplate;

$template = TaskTemplate::create([
    'name' => 'Update System Packages',
    'description' => 'Updates all system packages using apt',
    'type' => 'playbook',
    'playbook_content' => <<<YAML
---
- name: Update system packages
  hosts: all
  become: yes
  tasks:
    - name: Update apt cache
      apt:
        update_cache: yes
        cache_valid_time: 3600
    - name: Upgrade all packages
      apt:
        upgrade: dist
YAML,
    'is_active' => true,
]);
```

### Execute a Deployment

```php
use VisioSoft\LaraAnsible\Models\Deployment;
use VisioSoft\LaraAnsible\Jobs\ExecuteAnsibleDeployment;

// Create a deployment
$deployment = Deployment::create([
    'task_template_id' => $template->id,
    'user_id' => auth()->id(),
    'inventory_ids' => [$inventory->id], // or ['all'] for all active inventories
    'status' => 'pending',
]);

// Execute the deployment
ExecuteAnsibleDeployment::dispatch($deployment);
```

### Monitor Deployment Status

```php
use VisioSoft\LaraAnsible\Models\Deployment;

$deployment = Deployment::find($deploymentId);

echo "Status: " . $deployment->status; // pending, running, success, failed
echo "Started at: " . $deployment->started_at;
echo "Completed at: " . $deployment->completed_at;
echo "Exit code: " . $deployment->exit_code;
echo "Output:\n" . $deployment->command_output;
```

## Advanced Usage

### Custom Navigation Group

You can customize the navigation group name in the config file:

```php
// config/laraansible.php
return [
    'navigation_group' => 'DevOps Tools',
];
```

### Using with Laravel Horizon

The plugin uses Laravel Horizon for job processing. Make sure Horizon is running:

```bash
php artisan horizon
```

Monitor jobs at `/horizon` in your application.

### Encrypted Keystore Fields

For additional security, you can encrypt keystore fields using Laravel's encryption:

```php
use Illuminate\Support\Facades\Crypt;

$keystore = Keystore::create([
    'name' => 'Encrypted SSH Key',
    'type' => 'ssh',
    'private_key' => Crypt::encryptString(file_get_contents('/path/to/key')),
]);

// When using:
$decryptedKey = Crypt::decryptString($keystore->private_key);
```

## Customization

### Extending Models

You can extend the plugin models in your application:

```php
namespace App\Models;

use VisioSoft\LaraAnsible\Models\Deployment as BaseDeployment;

class Deployment extends BaseDeployment
{
    // Add custom methods or relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
```

### Custom Ansible Service

You can extend the AnsibleService to customize deployment behavior:

```php
namespace App\Services;

use VisioSoft\LaraAnsible\Services\AnsibleService as BaseService;

class CustomAnsibleService extends BaseService
{
    protected function buildAnsibleCommand($deployment, $inventoryPath, $playbookPath): array
    {
        $command = parent::buildAnsibleCommand($deployment, $inventoryPath, $playbookPath);
        
        // Add custom flags or modifications
        $command['wrapped_command'] .= ' --verbose';
        
        return $command;
    }
}
```

Then bind it in your service provider:

```php
$this->app->bind(
    \VisioSoft\LaraAnsible\Services\AnsibleService::class,
    \App\Services\CustomAnsibleService::class
);
```
