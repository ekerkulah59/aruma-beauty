<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;

class RecentActivityTable extends BaseWidget
{
    protected static ?string $heading = 'Recent Activity';

        protected static ?int $sort = 5;

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
            ->whereBetween('booking_date', [
                now()->startOfMonth()->format('Y-m-d'),
                now()->endOfMonth()->format('Y-m-d')
            ])
            ->orderByDesc('updated_at')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')->label('Client'),
            Tables\Columns\TextColumn::make('service.name')->label('Service'),
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'confirmed' => 'success',
                    'pending' => 'warning',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                    'rescheduled' => 'info',
                    'no_show' => 'gray',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Last Updated')
                ->dateTime()
                ->sortable(),
        ];
    }

    public function getTableRecordKey($record): string
    {
        return (string) ($record->id ?? uniqid());
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
