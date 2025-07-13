<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CalendarServiceContract;
use App\Services\LocalCalendarService;
use App\Services\MistralApiService;
use App\Services\AIQuestionAnswerService;
use App\Services\BookingService;
use App\Services\DashboardAnalyticsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register calendar service
        $this->app->bind(CalendarServiceContract::class, LocalCalendarService::class);

        // Register booking service
        $this->app->singleton(BookingService::class, function ($app) {
            return new BookingService($app->make(CalendarServiceContract::class));
        });

        // Register Mistral API service
        $this->app->singleton(MistralApiService::class, function ($app) {
            return new MistralApiService();
        });

        // Register AI Q&A service
        $this->app->singleton(AIQuestionAnswerService::class, function ($app) {
            return new AIQuestionAnswerService($app->make(MistralApiService::class));
        });

        // Register Dashboard Analytics service
        $this->app->singleton(DashboardAnalyticsService::class, function ($app) {
            return new DashboardAnalyticsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
