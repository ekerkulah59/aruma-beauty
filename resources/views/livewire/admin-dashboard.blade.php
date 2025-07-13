{{--
    Admin Dashboard View

    Available Methods for Monthly Analysis:
    - $this->getThisMonthAppointmentStatusByDay() - Returns detailed daily breakdown for current month

    Example Usage:
    @php
        $monthlyData = $this->getThisMonthAppointmentStatusByDay();
        $dailyBreakdown = $monthlyData['daily_data'];
        $totalBookings = $monthlyData['total_bookings'];
        $statusPercentages = $monthlyData['status_percentages'];
    @endphp
--}}

<div class="min-h-screen bg-gray-50">
    <!-- Dashboard Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-6 gap-4">
                <div class="flex items-center">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                    <div class="ml-4 px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-full">
                        {{ Auth::user()->name }}
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <!-- Dynamic Date Range Selector -->
                    <div class="flex items-center gap-3">
                        <label for="dateRange" class="text-sm font-medium text-gray-700 whitespace-nowrap">Date Range:</label>
                        <select wire:model.live="selectedRange" id="dateRange"
                                class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm min-w-[140px]">
                            <option value="today">Today</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="last_7_days">Last 7 Days</option>
                            <option value="last_30_days">Last 30 Days</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    <!-- Custom Date Pickers -->
                    @if($showCustomDatePickers)
                        <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-md border">
                            <input type="date" wire:model.blur="customStartDate"
                                   class="border-gray-300 rounded text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Start Date">
                            <span class="text-gray-500 text-sm">to</span>
                            <input type="date" wire:model.blur="customEndDate"
                                   class="border-gray-300 rounded text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="End Date">
                        </div>
                    @endif

                    <!-- Navigation Links -->
                    <div class="flex gap-2">
                        <a href="{{ route('admin.booking.calendar') }}"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center gap-1">
                            📅 Calendar
                        </a>
                        <a href="{{ route('home') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center gap-1">
                            🏠 Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        @if($isLoading)
            <div class="flex justify-center items-center py-16">
                <div class="flex items-center gap-3">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    <span class="text-gray-600 font-medium">Loading dashboard data...</span>
                </div>
            </div>
        @else

            <!-- Section 1: Key Performance Metrics -->
            <section class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Key Metrics</h2>
                    <span class="text-sm text-gray-500">{{ $this->getDateRangeLabel() }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Today's Appointments -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <span class="text-white text-lg">📅</span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-600">Today's Appointments</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $dashboardData['today']['total'] ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Confirmed:</span>
                                <span class="font-medium">{{ $dashboardData['today']['confirmed'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Pending:</span>
                                <span class="font-medium">{{ $dashboardData['today']['pending'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Completed:</span>
                                <span class="font-medium">{{ $dashboardData['today']['completed'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Cancelled:</span>
                                <span class="font-medium">{{ $dashboardData['today']['cancelled'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Rescheduled:</span>
                                <span class="font-medium">{{ $dashboardData['today']['rescheduled'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>No Show:</span>
                                <span class="font-medium">{{ $dashboardData['today']['no_show'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                    <span class="text-white text-lg">💰</span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-600">Period Revenue</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ $this->formatCurrency($this->getSelectedPeriodData()['revenue']) }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            <div class="flex justify-between items-center">
                                <span>Potential:</span>
                                <span class="font-medium text-blue-600">{{ $this->formatCurrency($this->getSelectedPeriodData()['potential']) }}</span>
                            </div>
                            <p class="text-gray-400 mt-1">From confirmed + pending</p>
                        </div>
                    </div>

                    <!-- Unique Clients -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <span class="text-white text-lg">👥</span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-600">Unique Clients</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $dashboardData['unique_clients'] ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            <div class="flex justify-between">
                                <span>Total Bookings:</span>
                                <span class="font-medium">{{ $dashboardData['total_bookings'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Completion Rate -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center">
                                    <span class="text-white text-lg">📊</span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-600">Completion Rate</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $this->formatPercentage($dashboardData['completion_metrics']['completion_rate'] ?? 0) }}</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Cancelled:</span>
                                <span class="font-medium text-red-600">{{ $this->formatPercentage($dashboardData['completion_metrics']['cancellation_rate'] ?? 0) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>No-Show:</span>
                                <span class="font-medium text-gray-600">{{ $this->formatPercentage($dashboardData['completion_metrics']['no_show_rate'] ?? 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Average Value -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                                    <span class="text-white text-lg">💵</span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-600">Avg. Appointment</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $this->formatCurrency($dashboardData['average_value'] ?? 0) }}</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            <p>Per completed appointment</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 2: Primary Insights -->
            <section class="space-y-3">
                <h2 class="text-lg font-semibold text-gray-900">Appointment Insights</h2>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <!-- Appointment Status Chart (takes 2/3 width on xl screens) -->
                    <div class="xl:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-medium text-gray-900">{{ $this->getDateRangeLabel() }} Appointment Status</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                @if(!empty($chartData['period_type']))
                                    Grouped by {{ $chartData['period_type'] === 'day' ? 'day' : 'week' }}
                                @endif
                            </p>
                        </div>
                        <div class="p-6">
                            <div class="relative h-80">
                                <canvas id="appointmentChart" class="w-full h-full"></canvas>
                                <!-- Overlay for no data state -->
                                @if(empty($chartData) || empty($chartData['datasets']) || count($chartData['datasets']) == 0)
                                    <div id="chart-no-data-overlay" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 rounded">
                                        <div class="text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            <p class="mt-2 text-sm font-medium">No appointments found</p>
                                            <p class="text-xs text-gray-400">for this period</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status Summary (takes 1/3 width on xl screens) -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-medium text-gray-900">Status Breakdown</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $this->getDateRangeLabel() }}</p>
                        </div>
                        <div class="p-6">
                            @php $selectedPeriodData = $this->getSelectedPeriodData(); @endphp
                            @if(!empty($selectedPeriodData['counts']))
                                <div class="space-y-4">
                                    @foreach($selectedPeriodData['counts'] as $status => $count)
                                        @if($count > 0)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-xl">{{ $this->getStatusIcon($status) }}</span>
                                                    <span class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-lg font-bold text-gray-900">{{ $count }}</span>
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $this->getStatusColor($status) }}">
                                                        {{ array_sum($selectedPeriodData['counts']) > 0 ? round(($count / array_sum($selectedPeriodData['counts'])) * 100, 1) : 0 }}%
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <p class="text-gray-500 mt-2">No appointments found for this period</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 3: Actionable Data -->
            <section class="space-y-3">
                <h2 class="text-lg font-semibold text-gray-900">Appointments & Activity</h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Appointments List -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Appointments</h3>
                                <p class="text-sm text-gray-500">{{ $this->getDateRangeLabel() }}</p>
                            </div>
                                                         <a href="{{ route('admin.booking.calendar') }}"
                               class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center gap-1 transition-colors">
                                View Calendar
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                        <div class="p-6">
                            @if(!empty($dashboardData['appointments_list']))
                                <div class="space-y-3 max-h-80 overflow-y-auto">
                                    @foreach($dashboardData['appointments_list'] as $appointment)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ $appointment['client_name'] }}</p>
                                                <p class="text-sm text-gray-600">{{ $appointment['service_name'] }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ \Carbon\Carbon::parse($appointment['date'])->format('M j, g:i A') }}
                                                </p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full {{ $this->getStatusColor($appointment['status']) }} mb-1">
                                                    {{ ucfirst($appointment['status']) }}
                                                </span>
                                                <p class="text-sm font-medium text-gray-900">{{ $this->formatCurrency($appointment['price']) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h8a2 2 0 012 2v4m0 4v10a2 2 0 01-2 2H6a2 2 0 01-2-2V11a2 2 0 012-2h4m8-4H8a2 2 0 00-2 2v4m8-6V7a2 2 0 00-2-2H8a2 2 0 00-2 2v4"></path>
                                    </svg>
                                    <p class="text-gray-500 mt-2">No appointments found for this period</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                            <p class="text-sm text-gray-500">{{ $this->getDateRangeLabel() }}</p>
                        </div>
                        <div class="p-6">
                            @if(!empty($dashboardData['recent_activity']))
                                <div class="space-y-3 max-h-80 overflow-y-auto">
                                    @foreach($dashboardData['recent_activity'] as $activity)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ $activity['client_name'] }}</p>
                                                <p class="text-sm text-gray-600">{{ $activity['service_name'] }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Booked {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}
                                                </p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full {{ $this->getStatusColor($activity['status']) }} mb-1">
                                                    {{ ucfirst($activity['status']) }}
                                                </span>
                                                <p class="text-xs text-gray-600">
                                                    {{ \Carbon\Carbon::parse($activity['booking_date'])->format('M j') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-gray-500 mt-2">No recent activity found</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 4: Supporting Data -->
            <section class="space-y-3">
                <h2 class="text-lg font-semibold text-gray-900">Business Insights</h2>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Popular Services -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-medium text-gray-900">Popular Services</h3>
                            <p class="text-sm text-gray-500">{{ $this->getDateRangeLabel() }}</p>
                        </div>
                        <div class="p-6">
                            @if(!empty($dashboardData['popular_services']))
                                <div class="space-y-4">
                                    @foreach($dashboardData['popular_services'] as $service)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ $service['service_name'] }}</p>
                                                <p class="text-sm text-gray-600">{{ $service['booking_count'] }} bookings</p>
                                            </div>
                                            <span class="text-lg font-bold text-green-600">
                                                {{ $this->formatCurrency($service['total_revenue']) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <p class="text-gray-500 mt-2">No service data available</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Placeholder for future insights -->
                    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 border-dashed">
                        <div class="p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Additional Insights</h3>
                            <p class="mt-1 text-sm text-gray-500">Future business analytics and insights will be displayed here</p>
                        </div>
                    </div>
                </div>
            </section>

        @endif
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-lg z-50 max-w-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-lg z-50 max-w-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let appointmentChart = null;

    function initializeChart() {
        const ctx = document.getElementById('appointmentChart');

        // Debug: Check if canvas element exists
        console.log('Canvas element found:', !!ctx);

        if (!ctx) {
            console.log('Canvas element not found, skipping chart initialization');
            return;
        }

        const chartData = @json($chartData);

        // Debug: Log chart data
        console.log('Chart data from server:', chartData);
        console.log('Chart data type:', typeof chartData);
        console.log('Has datasets:', chartData && chartData.datasets && chartData.datasets.length > 0);

        // Destroy existing chart if it exists
        if (appointmentChart) {
            appointmentChart.destroy();
            console.log('Destroyed existing chart');
        }

        // Manage overlay visibility
        const overlay = document.getElementById('chart-no-data-overlay');
        const hasData = chartData && chartData.datasets && chartData.datasets.length > 0;

        if (overlay) {
            overlay.style.display = hasData ? 'none' : 'flex';
        }

        // Create chart regardless of data (empty chart is better than no chart)
        if (chartData && chartData.labels) {
            console.log('Creating chart with data. Datasets count:', chartData.datasets ? chartData.datasets.length : 0);

            appointmentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels || [],
                    datasets: chartData.datasets || []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: false
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            callbacks: {
                                title: function(context) {
                                    return chartData.period_type === 'day'
                                        ? context[0].label
                                        : 'Week of ' + context[0].label;
                                },
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' appointments';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : '';
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    }
                }
            });

            console.log('Chart created successfully');
        } else {
            console.log('No valid chart data structure, creating empty chart');
            // Create empty chart to maintain canvas structure
            appointmentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        title: { display: false }
                    }
                }
            });
        }
    }

    // Initialize chart on page load
    console.log('Initializing chart on page load');
    initializeChart();

    // Listen for Livewire events to update chart
    document.addEventListener('updateChart', function(event) {
        console.log('Received updateChart event (document listener):', event.detail);
        handleChartUpdate(event.detail.chartData || event.detail[0]);
    });

    window.addEventListener('updateChart', function(event) {
        console.log('Received updateChart event (window listener):', event.detail);
        handleChartUpdate(event.detail.chartData || event.detail[0]);
    });

    function handleChartUpdate(newChartData) {
        console.log('Handling chart update with data:', newChartData);

        // Toggle overlay visibility
        const overlay = document.getElementById('chart-no-data-overlay');
        const hasData = newChartData && newChartData.datasets && newChartData.datasets.length > 0;

        if (overlay) {
            overlay.style.display = hasData ? 'none' : 'flex';
        }

        if (appointmentChart) {
            if (hasData) {
                console.log('Updating existing chart');
                appointmentChart.data.labels = newChartData.labels || [];
                appointmentChart.data.datasets = newChartData.datasets || [];
                appointmentChart.update('active'); // Use active animation
            } else {
                console.log('No data, keeping chart but clearing data');
                appointmentChart.data.labels = [];
                appointmentChart.data.datasets = [];
                appointmentChart.update('none');
            }
        } else if (hasData) {
            console.log('Creating new chart from event');
            // Re-initialize chart if it was destroyed but now we have data
            setTimeout(initializeChart, 100);
        }
    }

    // Also listen for Livewire events
    document.addEventListener('livewire:initialized', function() {
        console.log('Livewire initialized, reinitializing chart');
        setTimeout(initializeChart, 500);
    });

    document.addEventListener('livewire:navigated', function() {
        console.log('Livewire navigated, reinitializing chart');
        setTimeout(initializeChart, 500);
    });
});
</script>
