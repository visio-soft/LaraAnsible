# Installation Guide

This guide provides detailed installation instructions for the LaraAnsible FilamentPHP 3 plugin.

## Prerequisites

Before installing LaraAnsible, ensure you have:

1. **PHP 8.2 or higher**
   ```bash
   php -v
   ```

2. **Laravel 11.x or 12.x application with FilamentPHP 3**
   ```bash
   composer show filament/filament
   ```

3. **Database** (PostgreSQL, MySQL, or SQLite)
   - PostgreSQL recommended for production

4. **Redis** (for Laravel Horizon)
   ```bash
   redis-cli ping
   # Should return: PONG
   ```

5. **Ansible** (for running playbooks)
   ```bash
   ansible --version
   ```

## Step-by-Step Installation

### 1. Install the Package

Install LaraAnsible via Composer:

```bash
composer require visio-soft/laraansible
```

**Note:** Since this is currently a local package, you may need to add it to your `composer.json` manually:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../LaraAnsible"
        }
    ],
    "require": {
        "visio-soft/laraansible": "dev-main"
    }
}
```

Then run:

```bash
composer install
```

### 2. Install Laravel Horizon (if not already installed)

LaraAnsible requires Laravel Horizon for queue management:

```bash
composer require laravel/horizon
php artisan horizon:install
```

### 3. Configure Environment Variables

Add the following to your `.env` file:

```env
# Queue Configuration
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Database Configuration (example for PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Publish Configuration (Optional)

If you want to customize the plugin configuration:

```bash
php artisan vendor:publish --tag="laraansible-config"
```

This will create `config/laraansible.php` where you can customize settings.

### 5. Publish Migrations (Optional)

If you want to customize the migrations before running them:

```bash
php artisan vendor:publish --tag="laraansible-migrations"
```

This will copy the migration files to your `database/migrations` directory.

### 6. Run Migrations

Apply the database migrations:

```bash
php artisan migrate
```

This will create the following tables:
- `keystores` - Stores SSH keys and passwords
- `inventories` - Stores server information
- `task_templates` - Stores Ansible playbook templates
- `deployments` - Stores deployment history and outputs

### 7. Register the Plugin

Open your FilamentPHP Panel Provider (typically `app/Providers/Filament/AdminPanelProvider.php`) and register the plugin:

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

### 8. Start Laravel Horizon

In a separate terminal window, start Horizon to process background jobs:

```bash
php artisan horizon
```

For production environments, configure Horizon to run as a daemon:

```bash
# Add to your supervisor configuration
[program:horizon]
process_name=%(program_name)s
command=php /path/to/your/app/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/horizon.log
stopwaitsecs=3600
```

### 9. Start Your Application

Start your Laravel development server:

```bash
php artisan serve
```

Access your FilamentPHP admin panel at:
```
http://localhost:8000/admin
```

### 10. Verify Installation

After logging in to your admin panel, you should see the following resources under the "Ansible Management" navigation group:

- Inventory
- Task Templates
- Keystores
- Deployments

You can also verify the widgets are registered by checking your dashboard.

## Post-Installation Configuration

### Create Your First User

If you don't have a Filament user yet:

```bash
php artisan make:filament-user
```

### Configure Ansible

Ensure Ansible is properly configured on your server:

```bash
# Check Ansible version
ansible --version

# Test Ansible connectivity
ansible localhost -m ping
```

### Test the Installation

1. Navigate to **Keystores** and create a test SSH key
2. Navigate to **Inventory** and add a test server
3. Navigate to **Task Templates** and create a simple playbook
4. Navigate to **Deployments** and execute a test deployment
5. Check `/horizon` to monitor the job processing

## Troubleshooting

### "Class not found" errors

Clear your application cache:

```bash
php artisan cache:clear
php artisan config:clear
composer dump-autoload
```

### Migrations fail

Ensure your database is properly configured and accessible:

```bash
php artisan migrate:status
```

### Horizon not processing jobs

1. Ensure Redis is running:
   ```bash
   redis-cli ping
   ```

2. Check Horizon status:
   ```bash
   php artisan horizon:status
   ```

3. Restart Horizon:
   ```bash
   php artisan horizon:terminate
   php artisan horizon
   ```

### Plugin not appearing in admin panel

1. Verify the plugin is registered in your Panel Provider
2. Clear FilamentPHP cache:
   ```bash
   php artisan filament:clear-cached-components
   ```

## Updating

To update the LaraAnsible plugin:

```bash
composer update visio-soft/laraansible
php artisan migrate
php artisan optimize:clear
```

## Uninstallation

To remove LaraAnsible:

1. Remove the plugin registration from your Panel Provider
2. Roll back migrations:
   ```bash
   php artisan migrate:rollback --step=4
   ```
3. Remove the package:
   ```bash
   composer remove visio-soft/laraansible
   ```

## Support

If you encounter any issues during installation:

1. Check the [README.md](README.md) for general information
2. Review the [EXAMPLES.md](EXAMPLES.md) for usage examples
3. Open an issue on [GitHub](https://github.com/visio-soft/LaraAnsible/issues)

## Next Steps

After successful installation, check out:

- [README.md](README.md) - General documentation
- [EXAMPLES.md](EXAMPLES.md) - Usage examples and code snippets
- [CHANGELOG.md](CHANGELOG.md) - Version history and changes
