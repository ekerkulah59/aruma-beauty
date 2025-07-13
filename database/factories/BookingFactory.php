<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['confirmed', 'pending', 'cancelled', 'completed'];
        $bookingDate = fake()->dateTimeBetween('+1 day', '+30 days');

        // Ensure booking time is within business hours (9 AM - 8 PM)
        $hour = fake()->numberBetween(9, 19);
        $minute = fake()->randomElement([0, 15, 30, 45]);
        $bookingTime = \Carbon\Carbon::parse($bookingDate)->setTime($hour, $minute);

        return [
            'service_id' => Service::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'booking_date' => $bookingTime->format('Y-m-d'),
            'booking_time' => $bookingTime->format('H:i:s'),
            'status' => fake()->randomElement($statuses),
            'notes' => fake()->optional(0.7)->sentence(),
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the booking is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the booking is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Create a booking for today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_date' => now()->format('Y-m-d'),
            'booking_time' => now()->addHours(fake()->numberBetween(1, 8))->format('H:i:s'),
        ]);
    }

    /**
     * Create a booking for tomorrow.
     */
    public function tomorrow(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'booking_time' => now()->addDay()->setTime(fake()->numberBetween(9, 19), fake()->randomElement([0, 15, 30, 45]))->format('H:i:s'),
        ]);
    }

    /**
     * Create a booking for next week.
     */
    public function nextWeek(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_date' => now()->addWeek()->format('Y-m-d'),
            'booking_time' => now()->addWeek()->setTime(fake()->numberBetween(9, 19), fake()->randomElement([0, 15, 30, 45]))->format('H:i:s'),
        ]);
    }

    /**
     * Create a booking with specific service.
     */
    public function forService(Service $service): static
    {
        return $this->state(fn (array $attributes) => [
            'service_id' => $service->id,
        ]);
    }
}
