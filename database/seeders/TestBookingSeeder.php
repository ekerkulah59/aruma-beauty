<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing services
        $services = Service::all();

        if ($services->isEmpty()) {
            $this->command->error('No services found. Please run ServiceSeeder first.');
            return;
        }

        // Generate bookings for the current month
        $startDate = Carbon::now('America/New_York')->startOfMonth();
        $endDate = Carbon::now('America/New_York')->endOfMonth();

        $statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'rescheduled', 'no_show'];
        $times = ['09:00', '10:30', '12:00', '13:30', '15:00', '16:30'];

        $clients = [
            ['name' => 'Sarah Johnson', 'email' => 'sarah.j@email.com', 'phone' => '555-0101'],
            ['name' => 'Maria Rodriguez', 'email' => 'maria.r@email.com', 'phone' => '555-0102'],
            ['name' => 'Ashley Williams', 'email' => 'ashley.w@email.com', 'phone' => '555-0103'],
            ['name' => 'Jasmine Davis', 'email' => 'jasmine.d@email.com', 'phone' => '555-0104'],
            ['name' => 'Keisha Brown', 'email' => 'keisha.b@email.com', 'phone' => '555-0105'],
            ['name' => 'Tiffany Wilson', 'email' => 'tiffany.w@email.com', 'phone' => '555-0106'],
            ['name' => 'Nicole Moore', 'email' => 'nicole.m@email.com', 'phone' => '555-0107'],
            ['name' => 'Destiny Taylor', 'email' => 'destiny.t@email.com', 'phone' => '555-0108'],
        ];

        // Clear existing test bookings
        Booking::whereIn('email', collect($clients)->pluck('email'))->delete();

        $this->command->info('Creating sample bookings for chart visualization...');

        // Create bookings for each day in the current month
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            // Skip Sundays (salon closed)
            if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                // Random number of bookings per day (1-4)
                $bookingsPerDay = rand(1, 4);

                for ($i = 0; $i < $bookingsPerDay; $i++) {
                    $client = $clients[array_rand($clients)];
                    $service = $services->random();
                    $time = $times[array_rand($times)];

                    // Weight status distribution to be more realistic
                    $statusWeights = [
                        'confirmed' => 40,
                        'completed' => 25,
                        'pending' => 15,
                        'cancelled' => 10,
                        'rescheduled' => 7,
                        'no_show' => 3,
                    ];

                    $status = $this->getWeightedRandomStatus($statusWeights);

                    // Past dates should have more completed/cancelled appointments
                    if ($currentDate->isPast()) {
                        $pastStatusWeights = [
                            'completed' => 60,
                            'cancelled' => 20,
                            'no_show' => 15,
                            'rescheduled' => 5,
                        ];
                        $status = $this->getWeightedRandomStatus($pastStatusWeights);
                    }

                    // Future dates should be more confirmed/pending
                    if ($currentDate->isFuture()) {
                        $futureStatusWeights = [
                            'confirmed' => 70,
                            'pending' => 25,
                            'rescheduled' => 5,
                        ];
                        $status = $this->getWeightedRandomStatus($futureStatusWeights);
                    }

                    Booking::create([
                        'service_id' => $service->id,
                        'booking_date' => $currentDate->format('Y-m-d'),
                        'booking_time' => $time,
                        'name' => $client['name'],
                        'email' => $client['email'],
                        'phone' => $client['phone'],
                        'status' => $status,
                        'notes' => 'Sample booking for chart demonstration',
                        'created_at' => $currentDate->copy()->subDays(rand(1, 7)),
                        'updated_at' => now(),
                    ]);
                }
            }

            $currentDate->addDay();
        }

        // Add some recent bookings for the "Recent Activity" section
        for ($i = 0; $i < 5; $i++) {
            $client = $clients[array_rand($clients)];
            $service = $services->random();
            $time = $times[array_rand($times)];
            $futureDate = Carbon::now('America/New_York')->addDays(rand(1, 14));

            Booking::create([
                'service_id' => $service->id,
                'booking_date' => $futureDate->format('Y-m-d'),
                'booking_time' => $time,
                'name' => $client['name'],
                'email' => $client['email'],
                'phone' => $client['phone'],
                'status' => 'confirmed',
                'notes' => 'Recent booking for activity demonstration',
                'created_at' => now()->subMinutes(rand(30, 1440)), // Created within last 24 hours
                'updated_at' => now(),
            ]);
        }

        $totalBookings = Booking::whereIn('email', collect($clients)->pluck('email'))->count();
        $this->command->info("Created {$totalBookings} sample bookings successfully!");
    }

    private function getWeightedRandomStatus(array $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($weights as $status => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $status;
            }
        }

        return array_key_first($weights);
    }
}
