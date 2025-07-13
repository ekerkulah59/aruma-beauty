<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Hair Braiding', 'Hair Styling', 'Hair Extensions', 'Hair Treatment'];
        $durations = [30, 45, 60, 90, 120, 180, 240, 300, 360];

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 25, 300),
            'duration' => fake()->randomElement($durations),
            'category' => fake()->randomElement($categories),
            'active' => true,
        ];
    }

    /**
     * Indicate that the service is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Create a hair braiding service.
     */
    public function hairBraiding(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'Hair Braiding',
            'duration' => fake()->randomElement([180, 240, 300, 360]),
            'price' => fake()->randomFloat(2, 80, 250),
        ]);
    }

    /**
     * Create a hair styling service.
     */
    public function hairStyling(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'Hair Styling',
            'duration' => fake()->randomElement([30, 45, 60, 90]),
            'price' => fake()->randomFloat(2, 25, 100),
        ]);
    }

    /**
     * Create a hair extensions service.
     */
    public function hairExtensions(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'Hair Extensions',
            'duration' => fake()->randomElement([120, 180, 240]),
            'price' => fake()->randomFloat(2, 100, 300),
        ]);
    }

    /**
     * Create a hair treatment service.
     */
    public function hairTreatment(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'Hair Treatment',
            'duration' => fake()->randomElement([30, 45, 60]),
            'price' => fake()->randomFloat(2, 25, 80),
        ]);
    }
}
