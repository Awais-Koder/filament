<?php

namespace App\Filament\App\Widgets;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AppOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', Team::find(Filament::getTenant())->first()->members->count())
                ->description('Total team members')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Departments', Department::query()->whereBelongsTo(Filament::getTenant())->count())
                ->description('Total Departments')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Employees', Employee::query()->whereBelongsTo(Filament::getTenant())->count())
                ->description('Totla Employees')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
