<?php

namespace App\Filament\Widgets;

use App\Models\Deployment;
use App\Models\Inventory;
use App\Models\TaskTemplate;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeploymentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Servers', Inventory::where('is_active', true)->count())
                ->description('Active servers in inventory')
                ->descriptionIcon('heroicon-o-server')
                ->color('success'),
            Stat::make('Task Templates', TaskTemplate::where('is_active', true)->count())
                ->description('Available task templates')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary'),
            Stat::make('Total Deployments', Deployment::count())
                ->description(Deployment::where('status', 'success')->count() . ' successful')
                ->descriptionIcon('heroicon-o-rocket-launch')
                ->color('info'),
            Stat::make('Running Deployments', Deployment::where('status', 'running')->count())
                ->description('Currently executing')
                ->descriptionIcon('heroicon-o-play')
                ->color('warning'),
        ];
    }
}
