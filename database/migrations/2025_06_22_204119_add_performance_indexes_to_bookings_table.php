<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add index for status column (heavily used in dashboard analytics)
            $table->index('status');

            // Add composite index for booking_date and status (for date-filtered status queries)
            $table->index(['booking_date', 'status']);

            // Add composite index for service_id and status (for revenue calculations)
            $table->index(['service_id', 'status']);

            // Add index for created_at (for recent activity queries)
            $table->index('created_at');

            // Add index for email (for unique client counting)
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['booking_date', 'status']);
            $table->dropIndex(['service_id', 'status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['email']);
        });
    }
};
