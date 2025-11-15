# LaraAnsible - FilamentPHP 3 Plugin

A powerful FilamentPHP 3 plugin for managing Ansible inventories, task templates, keystores, and deployments directly from your Laravel application.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![FilamentPHP](https://img.shields.io/badge/FilamentPHP-3.x-orange.svg)
![Laravel](https://img.shields.io/badge/Laravel-11.x%20%7C%2012.x-red.svg)

## Features

- **Inventory Management**: Define and manage Ansible servers (inventories)
- **Task Templates**: Create reusable Ansible job templates
- **Keystore Management**: Securely manage SSH keys and credentials
- **Deployment Execution**: Run Ansible tasks on selected servers with real-time console output
- **Job Queue Integration**: Uses Laravel Horizon for background job processing
- **Dashboard Widgets**: Statistics and recent deployment monitoring
- **FilamentPHP 3 Integration**: Seamlessly integrates with any FilamentPHP 3 admin panel

## Requirements

- PHP 8.2 or higher
- Laravel 11.x or 12.x
- FilamentPHP 3.x
- PostgreSQL, MySQL, or SQLite database
- Redis (for Laravel Horizon)
- Ansible (for running playbooks)

## Installation

### 1. Install the package via Composer

\`\`\`bash
composer require visio-soft/laraansible
\`\`\`

### 2. Publish migrations (optional)

If you want to customize the migrations:

\`\`\`bash
php artisan vendor:publish --tag="laraansible-migrations"
\`\`\`

### 3. Run migrations

\`\`\`bash
php artisan migrate
\`\`\`

### 4. Publish config (optional)

If you want to customize the configuration:

\`\`\`bash
php artisan vendor:publish --tag="laraansible-config"
\`\`\`

### 5. Setup Laravel Horizon

LaraAnsible uses Laravel Horizon for queue management. Make sure you have Redis configured and Horizon installed:

\`\`\`bash
composer require laravel/horizon
php artisan horizon:install
\`\`\`

Configure your \`.env\` file:

\`\`\`env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
\`\`\`

Start Horizon:

\`\`\`bash
php artisan horizon
\`\`\`

## Usage

### Register the Plugin

In your FilamentPHP Panel Provider (typically \`app/Providers/Filament/AdminPanelProvider.php\`), register the plugin:

\`\`\`php
use VisioSoft\LaraAnsible\LaraAnsiblePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... other configuration
        ->plugin(LaraAnsiblePlugin::make());
}
\`\`\`

### Access the Resources

Once the plugin is registered, you'll have access to four new resources in your Filament admin panel under the "Ansible Management" navigation group:

1. **Inventory** - Manage your servers
2. **Task Templates** - Create and manage Ansible playbooks
3. **Keystores** - Manage SSH keys and passwords
4. **Deployments** - Execute and monitor deployments

### Dashboard Widgets

The plugin includes two widgets that can be added to your dashboard:

\`\`\`php
use VisioSoft\LaraAnsible\Filament\Widgets\DeploymentStatsWidget;
use VisioSoft\LaraAnsible\Filament\Widgets\LatestDeployments;

// In your panel configuration
->widgets([
    DeploymentStatsWidget::class,
    LatestDeployments::class,
])
\`\`\`

## Quick Start Guide

### 1. Create a Keystore

Navigate to **Keystores** and create a new keystore:

- **SSH Key**: Paste your private key (and optionally public key and passphrase)
- **Password**: Enter the password for authentication

### 2. Add Servers to Inventory

Navigate to **Inventory** and add your servers:

- Enter server name and description
- Provide hostname/IP address
- Set SSH port (default: 22)
- Set username for SSH connection
- Select the keystore for authentication

### 3. Create Task Templates

Navigate to **Task Templates** and create a template:

- Enter template name and description
- Choose type (Playbook or Ad-hoc)
- Either provide a playbook path or paste playbook content directly

Example playbook content:

\`\`\`yaml
---
- name: Update packages
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
\`\`\`

### 4. Execute Deployments

Navigate to **Deployments** and create a new deployment:

1. Select a task template
2. Choose which servers to run on (or select "All Servers")
3. Save the deployment (status: pending)
4. Click the **Execute** button to run the deployment
5. The deployment will be queued and processed by Laravel Horizon
6. View the deployment to see console output and execution status

## Configuration

The plugin publishes a configuration file at \`config/laraansible.php\`:

\`\`\`php
return [
    'navigation_group' => 'Ansible Management',
];
\`\`\`

## Database Tables

The plugin creates the following database tables:

- \`inventories\` - Stores server information
- \`keystores\` - Stores SSH keys and passwords
- \`task_templates\` - Stores Ansible playbook templates
- \`deployments\` - Stores deployment history and outputs

## Security Considerations

- **Keystore Data**: Private keys and passwords are stored in the database. Consider encrypting sensitive columns in production.
- **Ansible Execution**: The system executes Ansible commands on the server. Ensure proper file permissions and user access controls.
- **Authentication**: FilamentPHP handles user authentication. Configure proper user roles and permissions.

## Development

### Code Formatting

\`\`\`bash
composer format
\`\`\`

## Troubleshooting

### Horizon not processing jobs

- Ensure Redis is running: \`redis-cli ping\`
- Check Horizon status: \`php artisan horizon:status\`
- Restart Horizon: \`php artisan horizon:terminate\` then start again

### Ansible execution errors

- Verify Ansible is installed: \`ansible --version\`
- Check server connectivity and credentials
- Review deployment console output for specific errors

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

Open-source software licensed under the [MIT license](LICENSE).

## Credits

- Developed by [VisioSoft](https://github.com/visio-soft)
- Built with [FilamentPHP](https://filamentphp.com)
- Powered by [Laravel](https://laravel.com)

## Support

If you encounter any issues or have questions, please [open an issue](https://github.com/visio-soft/LaraAnsible/issues) on GitHub.
