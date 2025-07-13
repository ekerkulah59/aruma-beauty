<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Services\DashboardAnalyticsService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardStatsOverview extends BaseWidget
{
        protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 5;
    }

    protected function getCards(): array
    {
        $service = app(DashboardAnalyticsService::class);
        $period = CarbonPeriod::create(now()->startOfMonth(), now()->endOfMonth());
        $today = now();
        $todayBreakdown = $service->getAppointmentsBreakdownForDate($today);
        $completion = $service->getCompletionMetrics($period);

        return [
            Card::make("Today's Appointments", $todayBreakdown['total'] ?? 0),
            Card::make('Period Revenue', '$' . number_format($service->getTotalRevenue('completed', $period), 2)),
            Card::make('Unique Clients', $service->getUniqueClientsCount($period)),
            Card::make('Completion Rate', ($completion['completion_rate'] ?? 0) . '%'),
            Card::make('Avg. Appointment', '$' . number_format($service->getAverageAppointmentValue($period), 2)),
        ];
    }
}
