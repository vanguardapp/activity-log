<?php

namespace Vanguard\UserActivity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Vanguard\User;
use Vanguard\UserActivity\Activity;

class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'description' => substr($this->faker->paragraph, 0, 255),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
        ];
    }
}
