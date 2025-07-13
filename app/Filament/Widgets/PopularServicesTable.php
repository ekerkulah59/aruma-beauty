<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;

class PopularServicesTable extends BaseWidget
{
    protected static ?string $heading = 'Popular Services This Month';

        protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'lg' => 2,
        'xl' => 2,
        '2xl' => 3,
    ];

    protected function getTableQuery(): Builder
    {
        return Booking::query()
            ->with('service')
            ->select('service_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as booking_count'), \Illuminate\Support\Facades\DB::raw('MIN(id) as id'))
            ->whereBetween('booking_date', [
                now()->startOfMonth()->format('Y-m-d'),
                now()->endOfMonth()->format('Y-m-d')
            ])
            ->whereNotNull('service_id')
            ->groupBy('service_id')
            ->orderByDesc('booking_count')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('service.name')->label('Service'),
            Tables\Columns\TextColumn::make('booking_count')
                ->label('Bookings')
                ->getStateUsing(fn ($record) => $record->booking_count ?? 0),
        ];
    }

    public function getTableRecordKey($record): string
    {
        return (string) ($record->service_id ?? $record->id ?? uniqid());
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
