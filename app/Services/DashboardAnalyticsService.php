<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardAnalyticsService
{
    /**
     * Get appointment counts grouped by status
     */
    public function getAppointmentCountsByStatus(CarbonPeriod $period = null): array
    {
        $query = Booking::query();

        if ($period) {
            $query->whereBetween('booking_date', [
                $period->getStartDate()->format('Y-m-d'),
                $period->getEndDate()->format('Y-m-d')
            ]);
        }

        $counts = $query->groupBy('status')
            ->selectRaw('status, COUNT(*) as count')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all status types are represented
        $allStatuses = [
            'pending' => 0,
            'confirmed' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'rescheduled' => 0,
            'no_show' => 0,
        ];

        return array_merge($allStatuses, $counts);
    }

    /**
     * Get total revenue from completed appointments
     */
    public function getTotalRevenue(string $status = 'completed', CarbonPeriod $period = null): float
    {
        $query = Booking::with('service')
            ->where('status', $status);

        if ($period) {
            $query->whereBetween('booking_date', [
                $period->getStartDate()->format('Y-m-d'),
                $period->getEndDate()->format('Y-m-d')
            ]);
        }

        return $query->get()->sum(function ($booking) {
            return $booking->service->price ?? 0;
        });
    }

    /**
     * Get potential revenue from confirmed and pending appointments
     */
    public function getPotentialRevenue(CarbonPeriod $period = null): float
    {
        $query = Booking::with('service')
            ->whereIn('status', ['confirmed', 'pending']);

        if ($period) {
            $query->whereBetween('booking_date', [
                $period->getStartDate()->format('Y-m-d'),
                $period->getEndDate()->format('Y-m-d')
            ]);
        }

        return $query->get()->sum(function ($booking) {
            return $booking->service->price ?? 0;
        });
    }

    /**
     * Get today's appointments with detailed breakdown
     */
    public function getTodayAppointments(): array
    {
        $today = Carbon::today('America/New_York');

        $appointments = Booking::whereDate('booking_date', $today)->get();

        return [
            'total' => $appointments->count(),
            'confirmed' => $appointments->where('status', 'confirmed')->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'pending' => $appointments->where('status', 'pending')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'no_show' => $appointments->where('status', 'no_show')->count(),
        ];
    }

    /**
     * Get this week's appointments
     */
    public function getWeeklyAppointments(): array
    {
        $startOfWeek = Carbon::now('America/New_York')->startOfWeek();
        $endOfWeek = Carbon::now('America/New_York')->endOfWeek();

        $period = CarbonPeriod::create($startOfWeek, $endOfWeek);

        return [
            'counts' => $this->getAppointmentCountsByStatus($period),
            'revenue' => $this->getTotalRevenue('completed', $period),
            'potential' => $this->getPotentialRevenue($period),
        ];
    }

    /**
     * Get this month's appointments
     */
    public function getMonthlyAppointments(): array
    {
        $startOfMonth = Carbon::now('America/New_York')->startOfMonth();
        $endOfMonth = Carbon::now('America/New_York')->endOfMonth();

        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        return [
            'counts' => $this->getAppointmentCountsByStatus($period),
            'revenue' => $this->getTotalRevenue('completed', $period),
            'potential' => $this->getPotentialRevenue($period),
        ];
    }

    /**
     * Get this month's appointment status grouped by day
     * Returns detailed daily breakdown for current month analysis
     */
    public function getThisMonthAppointmentStatusByDay(): array
    {
        $startOfMonth = Carbon::now('America/New_York')->startOfMonth();
        $endOfMonth = Carbon::now('America/New_York')->endOfMonth();

        // Get all bookings for this month
        $bookings = Booking::whereBetween('booking_date', [
            $startOfMonth->format('Y-m-d'),
            $endOfMonth->format('Y-m-d')
        ])->get();

        // Define all possible statuses
        $allStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'rescheduled', 'no_show'];

        // Initialize data structure
        $dailyData = [];
        $labels = [];
        $totals = array_fill_keys($allStatuses, 0);

        // Create period for each day of the month
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dayLabel = $date->format('M j'); // e.g., "Jan 15"

            $labels[] = $dayLabel;

            // Get bookings for this specific day
            $dayBookings = $bookings->filter(function ($booking) use ($dateStr) {
                return $booking->booking_date->format('Y-m-d') === $dateStr;
            });

            $dayData = [];
            foreach ($allStatuses as $status) {
                $count = $dayBookings->where('status', $status)->count();
                $dayData[$status] = $count;
                $totals[$status] += $count;
            }

            $dayData['total'] = $dayBookings->count();
            $dayData['date'] = $dateStr;
            $dayData['formatted_date'] = $date->format('l, F j, Y'); // e.g., "Monday, January 15, 2024"
            $dayData['is_today'] = $date->isToday();
            $dayData['is_past'] = $date->isPast();
            $dayData['is_future'] = $date->isFuture();

            $dailyData[] = $dayData;
        }

        // Calculate summary statistics
        $totalBookings = array_sum($totals);
        $workingDays = count(array_filter($dailyData, function($day) { return $day['total'] > 0; }));

        return [
            'daily_data' => $dailyData,
            'labels' => $labels,
            'totals' => $totals,
            'total_bookings' => $totalBookings,
            'working_days' => $workingDays,
            'average_per_day' => $workingDays > 0 ? round($totalBookings / $workingDays, 2) : 0,
            'period' => [
                'start' => $startOfMonth->format('Y-m-d'),
                'end' => $endOfMonth->format('Y-m-d'),
                'formatted_start' => $startOfMonth->format('F j, Y'),
                'formatted_end' => $endOfMonth->format('F j, Y'),
                'days_in_month' => $startOfMonth->daysInMonth,
            ],
            'status_percentages' => $totalBookings > 0 ? [
                'pending' => round(($totals['pending'] / $totalBookings) * 100, 1),
                'confirmed' => round(($totals['confirmed'] / $totalBookings) * 100, 1),
                'completed' => round(($totals['completed'] / $totalBookings) * 100, 1),
                'cancelled' => round(($totals['cancelled'] / $totalBookings) * 100, 1),
                'rescheduled' => round(($totals['rescheduled'] / $totalBookings) * 100, 1),
                'no_show' => round(($totals['no_show'] / $totalBookings) * 100, 1),
            ] : array_fill_keys($allStatuses, 0),
        ];
    }

    /**
     * Get upcoming appointments (next 7 days)
     */
    public function getUpcomingAppointments(): array
    {
        $today = Carbon::today('America/New_York');
        $nextWeek = Carbon::today('America/New_York')->addDays(7);

        return $this->getUpcomingAppointmentsForRange($today, $nextWeek);
    }

    /**
     * Get upcoming appointments for a specific date range
     */
    public function getUpcomingAppointmentsForRange(Carbon $startDate, Carbon $endDate): array
    {
        // For past date ranges, show appointments from that period
        // For future/current ranges, show upcoming appointments from today
        $queryStartDate = $startDate->isPast() ? $startDate : Carbon::today('America/New_York');
        $queryEndDate = $endDate->isFuture() ? $endDate : Carbon::today('America/New_York')->addDays(7);

        $appointments = Booking::with(['service'])
            ->whereBetween('booking_date', [$queryStartDate, $queryEndDate])
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->limit(10)
            ->get();

        return $appointments->map(function ($booking) {
            return [
                'id' => $booking->id,
                'client_name' => $booking->name,
                'service_name' => $booking->service->name ?? 'Unknown Service',
                'date' => Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->booking_time, 'America/New_York'),
                'status' => $booking->status,
                'price' => $booking->service->price ?? 0,
            ];
        })->toArray();
    }

    /**
     * Get popular services (most booked)
     */
    public function getPopularServices(int $limit = 5): array
    {
        return $this->getPopularServicesForRange(null, $limit);
    }

    /**
     * Get popular services for a specific date range
     */
    public function getPopularServicesForRange(CarbonPeriod $period = null, int $limit = 5): array
    {
        $query = Booking::with('service')
            ->select('service_id', DB::raw('COUNT(*) as booking_count'))
            ->whereNotIn('status', ['cancelled'])
            ->groupBy('service_id');

        if ($period) {
            $query->whereBetween('booking_date', [
                $period->getStartDate()->format('Y-m-d'),
                $period->getEndDate()->format('Y-m-d')
            ]);
        }

        return $query->orderByDesc('booking_count')
            ->limit($limit)
            ->get()
            ->map(function ($booking) {
                return [
                    'service_name' => $booking->service->name ?? 'Unknown Service',
                    'booking_count' => $booking->booking_count,
                    'total_revenue' => ($booking->service->price ?? 0) * $booking->booking_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get recent activity (last 10 bookings)
     */
    public function getRecentActivity(int $limit = 10): array
    {
        return $this->getRecentActivityForRange(null, $limit);
    }

    /**
     * Get recent activity for a specific date range
     */
    public function getRecentActivityForRange(CarbonPeriod $period = null, int $limit = 10): array
    {
        $query = Booking::with(['service']);

        if ($period) {
            $query->whereBetween('booking_date', [
                $period->getStartDate()->format('Y-m-d'),
                $period->getEndDate()->format('Y-m-d')
            ]);
        }

        return $query->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'client_name' => $booking->name,
                    'service_name' => $booking->service->name ?? 'Unknown Service',
                    'status' => $booking->status,
                    'created_at' => $booking->created_at,
                    'booking_date' => Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->booking_time, 'America/New_York'),
                ];
            })
            ->toArray();
    }

    /**
     * Get unique clients count for a specific period
     */
    public function getUniqueClientsCount(CarbonPeriod $period = null): int
    {
        $query = Booking::query();

        if ($period) {
            $query->whereBetween('booking_date', [
                $period->getStartDate()->format('Y-m-d'),
                $period->getEndDate()->format('Y-m-d')
            ]);
        }

        return $query->distinct('email')->count();
    }

    /**
     * Get total bookings count for a specific period
     */
    public function getTotalBookingsCount(CarbonPeriod $period = null): int
    {
        $query = Booking::query();

        if ($period) {
            $query->whereBetween('booking_date', [
                $period->getStartDate()->format('Y-m-d'),
                $period->getEndDate()->format('Y-m-d')
            ]);
        }

        return $query->count();
    }

    /**
     * Get average appointment value for completed appointments
     */
    public function getAverageAppointmentValue(CarbonPeriod $period = null): float
    {
        $query = Booking::with('service')
            ->where('status', 'completed');

        if ($period) {
            $query->whereBetween('booking_date', [
                $period->getStartDate()->format('Y-m-d'),
                $period->getEndDate()->format('Y-m-d')
            ]);
        }

        $completedBookings = $query->get();
        $totalRevenue = $completedBookings->sum(function ($booking) {
            return $booking->service->price ?? 0;
        });

        return $completedBookings->count() > 0 ? round($totalRevenue / $completedBookings->count(), 2) : 0;
    }

    /**
     * Get appointment breakdown for a specific date
     */
    public function getAppointmentsBreakdownForDate(Carbon $date): array
    {
        $appointments = Booking::whereDate('booking_date', $date)->get();
        $breakdown = [
            'total' => $appointments->count(),
            'confirmed' => $appointments->where('status', 'confirmed')->count(),
            'pending' => $appointments->where('status', 'pending')->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'rescheduled' => $appointments->where('status', 'rescheduled')->count(),
            'no_show' => $appointments->where('status', 'no_show')->count(),
        ];
        return $breakdown;
    }

    /**
     * Get enhanced completion and cancellation rates (final state only)
     */
    public function getCompletionMetrics(CarbonPeriod $period = null): array
    {
        $query = Booking::query();
        if ($period) {
            $query->whereBetween('booking_date', [
                $period->getStartDate()->format('Y-m-d'),
                $period->getEndDate()->format('Y-m-d')
            ]);
        }
        $bookings = $query->get();
        $finalStates = ['completed', 'cancelled', 'no_show'];
        $finalBookings = $bookings->whereIn('status', $finalStates);
        $totalFinal = $finalBookings->count();
        $completed = $finalBookings->where('status', 'completed')->count();
        $cancelled = $finalBookings->where('status', 'cancelled')->count();
        $noShow = $finalBookings->where('status', 'no_show')->count();
        return [
            'total_final' => $totalFinal,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'no_show' => $noShow,
            'completion_rate' => $totalFinal > 0 ? round(($completed / $totalFinal) * 100, 1) : 0,
            'cancellation_rate' => $totalFinal > 0 ? round(($cancelled / $totalFinal) * 100, 1) : 0,
            'no_show_rate' => $totalFinal > 0 ? round(($noShow / $totalFinal) * 100, 1) : 0,
        ];
    }

    /**
     * Get comprehensive dashboard data for a specific date range
     */
    public function getDashboardDataForRange(array $dateRange): array
    {
        $period = $dateRange['period'];
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        return [
            'today' => $this->getTodayAppointments(),
            'selected_period' => [
                'counts' => $this->getAppointmentCountsByStatus($period),
                'revenue' => $this->getTotalRevenue('completed', $period),
                'potential' => $this->getPotentialRevenue($period),
            ],
            'chart_data' => $this->getAppointmentStatusDataForChart($startDate, $endDate),
            'upcoming' => $this->getUpcomingAppointmentsForRange($startDate, $endDate),
            'popular_services' => $this->getPopularServicesForRange($period),
            'recent_activity' => $this->getRecentActivityForRange($period),
            'unique_clients' => $this->getUniqueClientsCount($period),
            'total_bookings' => $this->getTotalBookingsCount($period),
            'average_value' => $this->getAverageAppointmentValue($period),
            'completion_metrics' => $this->getCompletionMetrics($period),
            'total_services' => Service::count(),
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'formatted_start' => $startDate->format('M j, Y'),
                'formatted_end' => $endDate->format('M j, Y'),
            ],
        ];
    }

    /**
     * Get comprehensive dashboard data (legacy method for backward compatibility)
     */
    public function getDashboardData(): array
    {
        $weeklyPeriod = CarbonPeriod::create(
            Carbon::now('America/New_York')->startOfWeek(),
            Carbon::now('America/New_York')->endOfWeek()
        );

        $monthlyPeriod = CarbonPeriod::create(
            Carbon::now('America/New_York')->startOfMonth(),
            Carbon::now('America/New_York')->endOfMonth()
        );

        return [
            'today' => $this->getTodayAppointments(),
            'weekly' => $this->getWeeklyAppointments(),
            'monthly' => $this->getMonthlyAppointments(),
            'upcoming' => $this->getUpcomingAppointments(),
            'popular_services' => $this->getPopularServices(),
            'recent_activity' => $this->getRecentActivity(),
            'weekly_unique_clients' => $this->getUniqueClientsCount($weeklyPeriod),
            'monthly_unique_clients' => $this->getUniqueClientsCount($monthlyPeriod),
            'weekly_total_bookings' => $this->getTotalBookingsCount($weeklyPeriod),
            'monthly_total_bookings' => $this->getTotalBookingsCount($monthlyPeriod),
            'weekly_average_value' => $this->getAverageAppointmentValue($weeklyPeriod),
            'monthly_average_value' => $this->getAverageAppointmentValue($monthlyPeriod),
            'weekly_completion_metrics' => $this->getCompletionMetrics($weeklyPeriod),
            'monthly_completion_metrics' => $this->getCompletionMetrics($monthlyPeriod),
            'total_services' => Service::count(),
        ];
    }

    /**
     * Get appointment status data for chart visualization
     */
    public function getAppointmentStatusDataForChart(Carbon $startDate, Carbon $endDate): array
    {
        $bookings = Booking::whereBetween('booking_date', [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        ])->get();

        // Determine granularity based on date range
        $daysDiff = $startDate->diffInDays($endDate);
        $groupBy = $daysDiff <= 7 ? 'day' : ($daysDiff <= 31 ? 'day' : 'week');

        $chartData = [];
        $labels = [];
        $datasets = [];

        // Define status colors
        $statusColors = [
            'pending' => '#fbbf24',      // yellow
            'confirmed' => '#3b82f6',    // blue
            'completed' => '#10b981',    // green
            'cancelled' => '#ef4444',    // red
            'rescheduled' => '#8b5cf6',  // purple
            'no_show' => '#6b7280',      // gray
        ];

        // Define all possible statuses
        $allStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'rescheduled', 'no_show'];

        if ($groupBy === 'day') {
            // Group by day
            $period = CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $dayBookings = $bookings->filter(function ($booking) use ($dateStr) {
                    return $booking->booking_date->format('Y-m-d') === $dateStr;
                });

                $labels[] = $date->format('M j');

                foreach ($allStatuses as $status) {
                    if (!isset($chartData[$status])) {
                        $chartData[$status] = [];
                    }
                    $chartData[$status][] = $dayBookings->where('status', $status)->count();
                }
            }
        } else {
            // Group by week
            $currentWeekStart = $startDate->copy()->startOfWeek();

            while ($currentWeekStart->lte($endDate)) {
                $weekEnd = $currentWeekStart->copy()->endOfWeek();
                if ($weekEnd->gt($endDate)) {
                    $weekEnd = $endDate->copy();
                }

                $weekBookings = $bookings->filter(function ($booking) use ($currentWeekStart, $weekEnd) {
                    $bookingDate = $booking->booking_date;
                    return $bookingDate->gte($currentWeekStart) && $bookingDate->lte($weekEnd);
                });

                $labels[] = $currentWeekStart->format('M j') . ' - ' . $weekEnd->format('M j');

                foreach ($allStatuses as $status) {
                    if (!isset($chartData[$status])) {
                        $chartData[$status] = [];
                    }
                    $chartData[$status][] = $weekBookings->where('status', $status)->count();
                }

                $currentWeekStart->addWeek();
            }
        }

        // Prepare datasets for Chart.js
        foreach ($allStatuses as $status) {
            if (isset($chartData[$status]) && array_sum($chartData[$status]) > 0) {
                $datasets[] = [
                    'label' => ucfirst(str_replace('_', ' ', $status)),
                    'data' => $chartData[$status],
                    'backgroundColor' => $statusColors[$status],
                    'borderColor' => $statusColors[$status],
                    'borderWidth' => 1,
                ];
            }
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
            'period_type' => $groupBy,
            'total_periods' => count($labels),
        ];
    }

    /**
     * Get performance metrics for a specific period
     */
    public function getPerformanceMetrics(CarbonPeriod $period): array
    {
        $bookings = Booking::with('service')
            ->whereBetween('booking_date', [
                $period->getStartDate()->format('Y-m-d'),
                $period->getEndDate()->format('Y-m-d')
            ])
            ->get();

        $totalBookings = $bookings->count();
        $completedBookings = $bookings->where('status', 'completed')->count();
        $cancelledBookings = $bookings->where('status', 'cancelled')->count();
        $noShowBookings = $bookings->where('status', 'no_show')->count();

        return [
            'total_bookings' => $totalBookings,
            'completion_rate' => $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 2) : 0,
            'cancellation_rate' => $totalBookings > 0 ? round(($cancelledBookings / $totalBookings) * 100, 2) : 0,
            'no_show_rate' => $totalBookings > 0 ? round(($noShowBookings / $totalBookings) * 100, 2) : 0,
            'average_booking_value' => $completedBookings > 0 ?
                round($bookings->where('status', 'completed')->sum(function ($booking) {
                    return $booking->service->price ?? 0;
                }) / $completedBookings, 2) : 0,
        ];
    }

    /**
     * Get a list of appointments for the dashboard (date range, limit)
     */
    public function getDashboardAppointmentsList(Carbon $startDate, Carbon $endDate, int $limit = 5): array
    {
        return Booking::with('service')
            ->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->limit($limit)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'client_name' => $booking->name,
                    'service_name' => $booking->service->name ?? 'Unknown Service',
                    'date' => $booking->booking_date,
                    'time' => $booking->booking_time,
                    'status' => $booking->status,
                    'price' => $booking->service->price ?? 0,
                ];
            })->toArray();
    }

    /**
     * Get recent booking activity for the dashboard (date range, limit)
     */
    public function getDashboardRecentActivity(Carbon $startDate, Carbon $endDate, int $limit = 5): array
    {
        return Booking::with('service')
            ->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'client_name' => $booking->name,
                    'service_name' => $booking->service->name ?? 'Unknown Service',
                    'status' => $booking->status,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                    'booking_date' => $booking->booking_date,
                ];
            })->toArray();
    }

    /**
     * Get popular services for the dashboard (date range, limit)
     */
    public function getDashboardPopularServices(Carbon $startDate, Carbon $endDate, int $limit = 5): array
    {
        $results = Booking::with('service')
            ->select('service_id', DB::raw('COUNT(*) as booking_count'))
            ->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereNotNull('service_id')
            ->groupBy('service_id')
            ->orderByDesc('booking_count')
            ->limit($limit)
            ->get();
        return $results->map(function ($booking) {
            $service = $booking->service;
            $price = $service->price ?? 0;
            return [
                'service_name' => $service->name ?? 'Unknown Service',
                'booking_count' => $booking->booking_count,
                'total_revenue' => $price * $booking->booking_count,
            ];
        })->toArray();
    }
}
