<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Services\DashboardAnalyticsService;

class AppointmentStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Appointment Status Over Time';

        protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'md' => 2,
        'lg' => 4,
        'xl' => 3,
        '2xl' => 4,
    ];

    protected static ?string $maxHeight = '350px';

    public function getData(): array
    {
        $data = app(DashboardAnalyticsService::class)
            ->getAppointmentStatusDataForChart(now()->startOfMonth(), now()->endOfMonth());

        return [
            'datasets' => $data['datasets'],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
