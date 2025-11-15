# AnsiblePHP — Filament Admin for Ansible

A compact Filament admin panel for managing Ansible inventories, keystores, task templates, and queued deployments with live console output.

Requirements: PHP 8.4+, Laravel 12, Filament 3, Redis (Horizon), Ansible installed on the host.

Quick start

-   Clone, install & configure:
    ```bash
    git clone <repo>
    cd ansiblephp
    composer install
    cp .env.example .env
    # edit .env DB and Redis values
    php artisan key:generate
    php artisan migrate --seed
    php artisan horizon & php artisan serve
    ```

Admin workflow

-   Create Keystores, Inventories, Task Templates, then create and execute Deployments.

Notes

-   Dev tools: `composer format`, `php artisan test`
-   Security: Protect keystore data (DB encryption); run Ansible under a restricted user.

License: MIT



use VisioSoft\LaraAnsible\LaraAnsiblePlugin;

    return $panel 
        ->plugin(LaraAnsiblePlugin::make());


use VisioSoft\LaraAnsible\Filament\Widgets\DeploymentStatsWidget;
use VisioSoft\LaraAnsible\Filament\Widgets\LatestDeployments;

->widgets([
DeploymentStatsWidget::class,
LatestDeployments::class,
])
