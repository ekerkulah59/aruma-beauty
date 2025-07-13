<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 4,
            '2xl' => 6,
        ];
    }

    public function getWidgets(): array
    {
        return [
            // Top Row: Key Performance Indicators (KPIs) - Full width
            \App\Filament\Widgets\DashboardStatsOverview::class,

            // Second Row: Primary Chart and Status Breakdown
            \App\Filament\Widgets\AppointmentStatusChart::class,
            \App\Filament\Widgets\StatusBreakdown::class,

            // Third Row: Data Tables Side by Side
            \App\Filament\Widgets\PopularServicesTable::class,
            \App\Filament\Widgets\RecentActivityTable::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}
