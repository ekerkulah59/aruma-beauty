<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Services\DashboardAnalyticsService;
use Carbon\CarbonPeriod;

class StatusBreakdown extends Widget
{
    protected static string $view = 'filament.widgets.status-breakdown';

        protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
        '2xl' => 2,
    ];

    public function getViewData(): array
    {
        $service = app(DashboardAnalyticsService::class);
        $period = CarbonPeriod::create(now()->startOfMonth(), now()->endOfMonth());
        $counts = $service->getAppointmentCountsByStatus($period);
        $total = array_sum($counts);

        return [
            'counts' => $counts,
            'total' => $total,
        ];
    }
}
