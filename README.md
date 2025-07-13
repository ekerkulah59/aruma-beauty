<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Salon Booking Application

A modern Laravel-based salon booking system with admin dashboard, real-time booking management, and comprehensive analytics.

## Features

- **Booking System**: Intuitive appointment booking with service selection
- **Admin Dashboard**: Comprehensive analytics and appointment management
- **Real-time Updates**: Live dashboard updates with Livewire
- **Analytics**: Detailed reporting with visual charts
- **Email Notifications**: Automated booking confirmations and reminders
- **Responsive Design**: Mobile-friendly interface

## New Analytics Feature: Monthly Appointment Status by Day

### getThisMonthAppointmentStatusByDay()

This function provides detailed daily breakdown of appointment statuses for the current month, perfect for monthly reporting and analysis.

#### Usage

**In DashboardAnalyticsService:**
```php
$service = app(App\Services\DashboardAnalyticsService::class);
$monthlyData = $service->getThisMonthAppointmentStatusByDay();
```

**In AdminDashboard Livewire Component:**
```php
$monthlyData = $this->getThisMonthAppointmentStatusByDay();
```

#### Return Structure

```php
[
    'daily_data' => [
        // Array of daily data with status counts for each day
        [
            'pending' => 2,
            'confirmed' => 5,
            'completed' => 3,
            'cancelled' => 1,
            'rescheduled' => 0,
            'no_show' => 1,
            'total' => 12,
            'date' => '2025-06-15',
            'formatted_date' => 'Monday, June 15, 2025',
            'is_today' => true,
            'is_past' => false,
            'is_future' => false
        ],
        // ... more days
    ],
    'labels' => ['Jun 1', 'Jun 2', '...'], // Day labels for charts
    'totals' => [
        'pending' => 4,
        'confirmed' => 18,
        'completed' => 25,
        'cancelled' => 16,
        'rescheduled' => 4,
        'no_show' => 9
    ],
    'total_bookings' => 76,
    'working_days' => 24, // Days with at least 1 appointment
    'average_per_day' => 3.17,
    'period' => [
        'start' => '2025-06-01',
        'end' => '2025-06-30',
        'formatted_start' => 'June 1, 2025',
        'formatted_end' => 'June 30, 2025',
        'days_in_month' => 30
    ],
    'status_percentages' => [
        'pending' => 5.3,
        'confirmed' => 23.7,
        'completed' => 32.9,
        'cancelled' => 21.1,
        'rescheduled' => 5.3,
        'no_show' => 11.8
    ]
]
```

#### Example Usage

**Generate Monthly Report:**
```php
$monthlyData = $service->getThisMonthAppointmentStatusByDay();

echo "Monthly Report: " . $monthlyData['period']['formatted_start'] 
     . " - " . $monthlyData['period']['formatted_end'];
echo "Total Appointments: " . $monthlyData['total_bookings'];
echo "Working Days: " . $monthlyData['working_days'];
echo "Completion Rate: " . $monthlyData['status_percentages']['completed'] . "%";
```

**Find Busiest Days:**
```php
$busiestDays = collect($monthlyData['daily_data'])
    ->sortByDesc('total')
    ->take(5)
    ->pluck('total', 'formatted_date');
```

**Chart Integration:**
```php
$chartLabels = $monthlyData['labels'];
$chartDatasets = [
    [
        'label' => 'Completed',
        'data' => collect($monthlyData['daily_data'])->pluck('completed'),
        'backgroundColor' => '#10b981'
    ],
    [
        'label' => 'Pending',
        'data' => collect($monthlyData['daily_data'])->pluck('pending'),
        'backgroundColor' => '#fbbf24'
    ]
    // ... more datasets
];
```

## Installation

// ... existing code ...
# aruma-beauty
# aruma-beauty
