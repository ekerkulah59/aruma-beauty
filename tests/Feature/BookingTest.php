<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test services
        $this->service = Service::factory()->create([
            'name' => 'Test Service',
            'price' => 100.00,
            'duration' => 60,
            'category' => 'Hair Braiding',
            'active' => true
        ]);
    }

    /** @test */
    public function user_can_view_booking_form()
    {
        $response = $this->get('/book');

        $response->assertStatus(200);
        $response->assertSee('Book Your Appointment');
        $response->assertSee($this->service->name);
    }

    /** @test */
    public function user_can_book_appointment_with_valid_data()
    {
        $bookingData = [
            'selectedService' => $this->service->id,
            'selectedDate' => Carbon::tomorrow()->format('Y-m-d'),
            'selectedTime' => '10:00',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'notes' => 'Test booking'
        ];

        $response = $this->post('/book', $bookingData);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'service_id' => $this->service->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'notes' => 'Test booking'
        ]);
    }

    /** @test */
    public function booking_validation_requires_all_fields()
    {
        $response = $this->post('/book', []);

        $response->assertSessionHasErrors([
            'selectedService',
            'selectedDate',
            'selectedTime',
            'name',
            'email',
            'phone'
        ]);
    }

    /** @test */
    public function cannot_book_inactive_service()
    {
        $inactiveService = Service::factory()->create(['active' => false]);

        $bookingData = [
            'selectedService' => $inactiveService->id,
            'selectedDate' => Carbon::tomorrow()->format('Y-m-d'),
            'selectedTime' => '10:00',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890'
        ];

        $response = $this->post('/book', $bookingData);

        $response->assertSessionHasErrors(['selectedService']);
    }

    /** @test */
    public function cannot_book_in_past()
    {
        $bookingData = [
            'selectedService' => $this->service->id,
            'selectedDate' => Carbon::yesterday()->format('Y-m-d'),
            'selectedTime' => '10:00',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890'
        ];

        $response = $this->post('/book', $bookingData);

        $response->assertSessionHasErrors(['selectedDate']);
    }

    /** @test */
    public function cannot_book_on_sunday()
    {
        $sunday = Carbon::now()->next(Carbon::SUNDAY);

        $bookingData = [
            'selectedService' => $this->service->id,
            'selectedDate' => $sunday->format('Y-m-d'),
            'selectedTime' => '10:00',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890'
        ];

        $response = $this->post('/book', $bookingData);

        $response->assertSessionHasErrors(['selectedDate']);
    }

    /** @test */
    public function cannot_book_outside_business_hours()
    {
        $bookingData = [
            'selectedService' => $this->service->id,
            'selectedDate' => Carbon::tomorrow()->format('Y-m-d'),
            'selectedTime' => '07:00', // Before 9 AM
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890'
        ];

        $response = $this->post('/book', $bookingData);

        $response->assertSessionHasErrors(['selectedTime']);
    }

    /** @test */
    public function validates_email_format()
    {
        $bookingData = [
            'selectedService' => $this->service->id,
            'selectedDate' => Carbon::tomorrow()->format('Y-m-d'),
            'selectedTime' => '10:00',
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'phone' => '+1234567890'
        ];

        $response = $this->post('/book', $bookingData);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function validates_phone_format()
    {
        $bookingData = [
            'selectedService' => $this->service->id,
            'selectedDate' => Carbon::tomorrow()->format('Y-m-d'),
            'selectedTime' => '10:00',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => 'invalid-phone'
        ];

        $response = $this->post('/book', $bookingData);

        $response->assertSessionHasErrors(['phone']);
    }

    /** @test */
    public function validates_name_format()
    {
        $bookingData = [
            'selectedService' => $this->service->id,
            'selectedDate' => Carbon::tomorrow()->format('Y-m-d'),
            'selectedTime' => '10:00',
            'name' => 'John123', // Contains numbers
            'email' => 'john@example.com',
            'phone' => '+1234567890'
        ];

        $response = $this->post('/book', $bookingData);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function admin_can_view_all_bookings()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $booking = Booking::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/bookings');

        $response->assertStatus(200);
        $response->assertSee($booking->name);
    }

    /** @test */
    public function non_admin_cannot_access_admin_area()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin/bookings');

        $response->assertStatus(403);
    }
}
