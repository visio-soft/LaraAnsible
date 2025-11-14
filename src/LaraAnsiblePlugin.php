<?php

namespace VisioSoft\LaraAnsible;

use Filament\Contracts\Plugin;
use Filament\Panel;
use VisioSoft\LaraAnsible\Filament\Resources\DeploymentResource;
use VisioSoft\LaraAnsible\Filament\Resources\InventoryResource;
use VisioSoft\LaraAnsible\Filament\Resources\KeystoreResource;
use VisioSoft\LaraAnsible\Filament\Resources\TaskTemplateResource;
use VisioSoft\LaraAnsible\Filament\Widgets\DeploymentStatsWidget;
use VisioSoft\LaraAnsible\Filament\Widgets\LatestDeployments;

class LaraAnsiblePlugin implements Plugin
{
    public function getId(): string
    {
        return 'laraansible';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                InventoryResource::class,
                TaskTemplateResource::class,
                KeystoreResource::class,
                DeploymentResource::class,
            ])
            ->widgets([
                DeploymentStatsWidget::class,
                LatestDeployments::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }
}
